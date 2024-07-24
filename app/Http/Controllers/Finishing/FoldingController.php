<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Folding;
use App\Models\FoldingDetail;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FoldingController extends Controller
{
    private static $model = 'Folding';
    private static $modelDetail = 'FoldingDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Folding', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'folding', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.folding.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.finishing.folding.parent', $data);
    }
    public function table()
    {
        $temp = FoldingDetail::orderBy('created_at', 'desc');
        return DataTables::of($temp)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('mesin', function ($i) {
                $mesin = $i->id_mesin ? $i->relMesin->name : '';
                return $mesin;
            })
            ->addColumn('no_kikw', function ($i) {
                $nomor = $i->id_beam ? $i->relBeam->no_kikw : '';
                return $nomor;
            })
            ->addColumn('grade', function ($i) {
                return $i->relGrade->grade;
            })
            ->addColumn('barang', function ($i) {
                return $i->relBarang->name;
            })
            ->addColumn('warna', function ($i) {
                return $i->relWarna->alias;
            })
            ->addColumn('motif', function ($i) {
                return $i->relMotif->alias;
            })
            ->addColumn('gudang', function ($i) {
                return $i->relGudang->name;
            })
            ->addColumn('action', function ($i) {
                $temp = $i->validated_at;
                $validasi = [
                    'status' => true,
                    'data' => $temp,
                    'model' => 'FoldingDetail'
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
            $mode = $request->mode;
            $tipe = null;
            if ($request->has('tipe')) {
                $tipe = $request->tipe;
            }
            $data = $request->except(['id', '_token', 'mode', 'tipe']);
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'volume_1', 'nomor']);
            $rule = $this->cekRequest($request, $mode, $tipe);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    $dataLogOut = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'JS';

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'FD';

                    $data['code'] = 'FD';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    FoldingDetail::create($data);
                    logHistory(self::$modelDetail, 'create');
                } else {
                    $inspect = FoldingDetail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'JS';

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'FD';

                    $data['code'] = 'FD';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    FoldingDetail::find($id)->update($data);
                    logHistory(self::$modelDetail, 'update');
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

            $logIdOut = FoldingDetail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = FoldingDetail::find($id)->id_log_stok_penerimaan_masuk;
            FoldingDetail::find($id)->delete();
            LogStokPenerimaan::find($logIdOut)->delete();
            LogStokPenerimaan::find($logIdIn)->delete();
            logHistory(self::$modelDetail, 'delete');
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

        $rules['id_grade'] = 'required|not_in:0';
        $messages['id_grade.required'] = 'grade harus diisi';
        $messages['id_grade.not_in'] = 'grade harus diisi';

        // $rules['id_mesin'] = 'required|not_in:0';
        $rules['id_barang'] = 'required|not_in:0';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['id_warna'] = 'required|not_in:0';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['id_motif'] = 'required|not_in:0';
        // $rules['id_beam'] = 'required|not_in:0';
        $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';

        // $messages['id_mesin.required'] = 'mesin harus diisi';
        // $messages['id_mesin.not_in'] = 'mesin harus diisi';
        $messages['id_barang.required'] = 'barang harus diisi';
        $messages['id_barang.not_in'] = 'barang harus diisi';
        $messages['id_gudang.required'] = 'gudang harus diisi';
        $messages['id_gudang.not_in'] = 'gudang harus diisi';
        $messages['id_warna.required'] = 'warna harus diisi';
        $messages['id_warna.not_in'] = 'warna harus diisi';
        $messages['id_motif.required'] = 'motif harus diisi';
        $messages['id_motif.not_in'] = 'motif harus diisi';
        // $messages['id_beam.required'] = 'beam harus diisi';
        // $messages['id_beam.not_in'] = 'beam harus diisi';
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
        $data = FoldingDetail::with('relBarang', 'relWarna', 'relGrade', 'relBeam', 'relMesin', 'relGudang', 'relMotif')->leftJoin('log_stok_penerimaan as log', 'log.id', 'tbl_folding_detail.id_log_stok_penerimaan_keluar')
            ->leftJoin('tbl_kualitas as grade', 'grade.id', 'log.id_grade')
            ->selectRaw('
                tbl_folding_detail.id,
                tbl_folding_detail.tanggal,
                tbl_folding_detail.id_barang,
                tbl_folding_detail.id_gudang,
                tbl_folding_detail.id_warna,
                tbl_folding_detail.id_motif,
                tbl_folding_detail.id_beam,
                tbl_folding_detail.id_mesin,
                log.id_grade id_grade_awal,
                grade.grade nama_grade_awal,
                tbl_folding_detail.id_grade,
                tbl_folding_detail.volume_1
            ')->where('tbl_folding_detail.id', $id)->first();

        return $data;
    }
    function getBarang($gudang)
    {
        $data = LogStokPenerimaan::with('relGudang', 'relBarang', 'relWarna', 'relMotif', 'relBeam', 'relMesin', 'relGrade')
            ->where('code', 'JS')
            ->where('id_satuan_1', 4)
            ->where('id_gudang', $gudang)
            ->selectRaw('
            id_gudang,
            id_barang,
            id_warna,
            id_motif,
            id_beam,
            id_mesin,
            id_grade,
            id_satuan_1,
            sum(volume_masuk_1) - sum(volume_keluar_1) as volume_1
        ')
            ->groupBy('id_gudang', 'id_barang', 'id_warna', 'id_motif', 'id_beam', 'id_mesin', 'id_satuan_1', 'id_grade')
            ->havingRaw('
            sum(volume_masuk_1) - sum(volume_keluar_1) != 0
        ')->get();

        return $data;
    }
    function getGudang()
    {
        $data = LogStokPenerimaan::with('relGudang')->selectRaw('id_gudang')->where('code', 'JS')->groupBy('id_gudang')->get();
        return $data;
    }
    function getGrade()
    {
        $data = Kualitas::whereIn('id', [1, 3])->get();
        return $data;
    }
    function getKualitas(Request $request)
    {
        $data = MappingKualitas::selectRaw('id, kode, name')->where('id_kualitas', $request->grade)->get();
        return $data;
    }
    public function getStokBarang($mesin, $barang, $warna, $gudang, $motif, $beam, $grade)
    {
        $mesin = $mesin == 'null' ? null : $mesin;
        $beam = $beam == 'null' ? null : $beam;
        $data = LogStokPenerimaan::selectRaw('
                id_satuan_1,
                sum(coalesce(volume_masuk_1,0)) - sum(coalesce(volume_keluar_1,0)) as stok_1,
                id_satuan_2,
                sum(coalesce(volume_masuk_2,0)) - sum(coalesce(volume_keluar_2,0)) as stok_2
            ')
            ->where([
                ['id_mesin', $mesin],
                ['id_barang', $barang],
                ['id_warna', $warna],
                ['id_gudang', $gudang],
                ['id_motif', $motif],
                ['id_beam', $beam],
                ['id_grade', $grade],
                ['id_satuan_1', 4],
                ['id_satuan_2', null],
                ['code', 'JS']
            ])
            ->groupBy('id_mesin', 'id_barang', 'id_warna', 'id_gudang', 'id_motif', 'id_beam', 'id_grade', 'id_satuan_1', 'id_satuan_2', 'id_beam', 'code')
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
