<?php

namespace App\Http\Controllers\Finishing;

use App\Http\Controllers\Controller;
use App\Models\ChemicalFinishing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helpers\Date;
use App\Models\DryingDetail;
use App\Models\JiggerDetail;
use App\Models\LogStokPenerimaan;
use Exception;

class ChemicalFinishingBackupController extends Controller
{
    private static $model = 'ChemicalFinishing';
    private static $modelDetail = 'ChemicalFinishingDetail';
    public function view($menu, $id)
    {
        $data['menu'] = $menu;
        $data['id'] = $id;
        if ($menu == 'jigger') {
            $data['data'] = JiggerDetail::where('id', $id)->first();
        } else {
            $data['data'] = DryingDetail::where('id', $id)->first();
        }
        return view('contents.production.finishing.chemical_finishing.chemical', $data);
    }
    public function table($menu, $id)
    {
        $temp = ChemicalFinishing::when($menu, function ($q) use ($menu) {
            if ($menu == 'jigger') {
                return $q->where('code', 'CJ');
            } else {
                return $q->where('code', 'CD');
            }
        })->where('id_detail', $id)->orderBy('created_at', 'desc');
        return DataTables::of($temp)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('barang', function ($i) {
                return $i->relBarang->name;
            })
            ->addColumn('gudang', function ($i) {
                return $i->relGudang->name;
            })
            ->addColumn('volume_1', function ($i) {
                return $i->volume_1;
            })
            ->addColumn('action', function ($i) {
                if ($i->code === 'CJ') {
                    $temp = JiggerDetail::find($i->id_detail)->validated_at;
                    $model = 'JiggerDetail';
                } else {
                    $temp = DryingDetail::find($i->id_detail)->validated_at;
                    $model = 'DryingDetail';
                }
                $validasi = [
                    'status' => false,
                    'data' => $temp,
                    'model' => $model
                ];
                $action = actionBtn($i->id, false, true, true, $validasi);
                return $action;
            })
            ->rawColumns(['action'])
            ->make('true');
    }
    public function simpan(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $data = $request->except(['id', '_token', 'menu']);
            $dataLog = $request->except(['id', '_token', 'menu', 'volume_1', 'id_detail']);

            $code = ($request->menu == 'jigger') ? 'CJ' : 'CD';
            $rule = $this->cekRequest($request);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                $data['code'] = $code;
                $dataLog['code'] = 'CF';
                $dataLog['volume_keluar_1'] = $data['volume_1'];
                if (!$id) {
                    $logId = LogStokPenerimaan::create($dataLog)->id;
                    $data['id_log_stok_penerimaan'] = $logId;
                    ChemicalFinishing::create($data);
                    logHistory(self::$model, 'create');
                } else {
                    $detail = ChemicalFinishing::find($id);
                    LogStokPenerimaan::find($detail->id_log_stok_penerimaan)->update($dataLog);
                    $data['updated_by'] = Auth::id();
                    $detail->update($data);
                    logHistory(self::$model, 'update');
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    public function hapus($id)
    {
        DB::beginTransaction();
        try {
            $logId = ChemicalFinishing::find($id)->id_log_stok_penerimaan;
            ChemicalFinishing::find($id)->delete();
            LogStokPenerimaan::find($logId)->delete();
            logHistory(self::$model, 'delete');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    function cekRequest($request)
    {
        $rules = [
            'tanggal' => 'required',
        ];
        $messages = [];

        $rules['id_detail'] = 'required';
        $messages['id_detail.required'] = 'detail hilang';

        $rules['id_barang'] = 'required|not_in:0';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';

        $messages['id_barang.required'] = 'barang harus diisi';
        $messages['id_barang.not_in'] = 'barang harus diisi';
        $messages['id_gudang.required'] = 'gudang harus diisi';
        $messages['id_gudang.not_in'] = 'gudang harus diisi';
        $messages['volume_1.required'] = 'volume harus diisi';
        $messages['volume_1.numeric'] = 'volume hanya berupa angka';
        $messages['volume_1.not_in'] = 'volume tidak boleh 0';
        $messages['volume_1.gt'] = 'volume harus lebih besar dari 0';


        $messages['tanggal.required'] = 'tanggal harus diisi';

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $data['success'] = false;
            $data['messages'] = $validator->getMessageBag()->toArray();
        } else {
            $data['success'] = true;
            $data['messages'] = '';
        }
        return $data;
    }
    function getData($id)
    {
        $data = ChemicalFinishing::with('relBarang', 'relGudang')->find($id);
        return $data;
    }
    function getBarang($menu)
    {
        // $code = ($menu == 'jigger') ? 'CJ' : 'CD';
        $code = 'CF';
        $data = LogStokPenerimaan::with('relBarang')->select('id_barang')->where('code', $code)->groupBy('id_barang')->get();
        return $data;
    }
    function getGudang($menu)
    {
        // $code = ($menu == 'jigger') ? 'CJ' : 'CD';
        $code = 'CF';
        $data = LogStokPenerimaan::with('relGudang')->select('id_gudang')->where('code', $code)->groupBy('id_gudang')->get();
        return $data;
    }
    function getStokBarang($menu, $barang, $gudang)
    {
        // $code = ($menu == 'jigger') ? 'CJ' : 'CD';
        $code = 'CF';
        $data = LogStokPenerimaan::selectRaw('
                id_satuan_1,
                sum(coalesce(volume_masuk_1,0)) - sum(coalesce(volume_keluar_1,0)) as stok_1,
                id_satuan_2,
                sum(coalesce(volume_masuk_2,0)) - sum(coalesce(volume_keluar_2,0)) as stok_2
            ')
            ->where([
                ['id_barang', $barang],
                ['id_gudang', $gudang],
                ['id_satuan_1', 2],
                ['id_satuan_2', null],
                ['code', $code]
            ])
            ->groupBy('id_barang', 'id_gudang', 'id_satuan_1', 'id_satuan_2', 'code')
            ->first();
        if ($data) {
            $temp = $data;
        } else {
            $temp = [
                'id_satuan_1' => 1,
                'stok_1' => 0,
                'id_satuan_2' => 2,
                'stok_2' => 0,
            ];
        }

        return $temp;
    }
}
