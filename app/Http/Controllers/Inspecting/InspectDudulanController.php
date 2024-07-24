<?php

namespace App\Http\Controllers\Inspecting;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Dudulan;
use App\Models\DudulanDetail;
use App\Models\InspectDudulan;
use App\Models\InspectDudulanDetail;
use App\Models\InspectDudulanKualitas;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InspectDudulanController extends Controller
{
    private static $model = 'InspectDudulan';
    private static $modelDetail = 'InspectDudulanDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Inspecting', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Inspect Dudulan', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('inspecting', 'inspect dudulan', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.inspecting.inspect_dudulan.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.inspecting.inspect_dudulan.parent', $data);
    }
    public function table(Request $request)
    {
        $spk = ($request->spk == 'semua') ? null : $request->spk;

        $temp = InspectDudulanDetail::when($spk, function ($q) use ($spk) {
            return $q->where('id_dudulan', $spk);
        })->orderBy('created_at', 'desc');
        $column = [
            'tanggal',
            'tanggal_potong',
            'spk',
            'no_kikw',
            'no_kiks',
            'grade',
            'kualitas',
            'barang',
            'warna',
            'motif',
            'gudang',
            'mesin',
            'action' => ['edit', 'hapus', 'validasi']
        ];
        $position = ['inspect', 'parent'];
        return getDataTable($temp, $column, 'inspect_dudulan', $position);
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
                    $dataLogOut = unsetMultiKeys(['id_dudulan', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'BGD';

                    $dataLogIn = unsetMultiKeys(['id_dudulan', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'BGID';

                    $data['code'] = 'BGID';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);

                    $dataId = InspectDudulanDetail::create($data)->id;
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_dudulan_detail'] = $dataId;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectDudulanKualitas::create($dataKualitas);
                        }
                    }
                    logHistory(self::$modelDetail, 'create');
                } else {
                    $inspect = InspectDudulanDetail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_dudulan', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'BGD';

                    $dataLogIn = unsetMultiKeys(['id_dudulan', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'BGID';

                    $data['code'] = 'BGID';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);
                    InspectDudulanKualitas::where('id_inspect_dudulan_detail', $id)->forceDelete();
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_dudulan_detail'] = $id;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectDudulanKualitas::create($dataKualitas);
                        }
                    }

                    InspectDudulanDetail::find($id)->update($data);
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

            $logIdOut = InspectDudulanDetail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = InspectDudulanDetail::find($id)->id_log_stok_penerimaan_masuk;
            InspectDudulanDetail::find($id)->delete();
            InspectDudulanKualitas::where('id_inspect_dudulan_detail', $id)->delete();
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
        if ($request->input('id_grade') != 1) {
            $rules['id_kualitas'] = 'required|not_in:0';
            $messages['id_kualitas.required'] = 'kualitas harus diisi';
            $messages['id_kualitas.not_in'] = 'kualitas harus diisi';
        }
        $rules['id_dudulan'] = 'required';
        $rules['id_mesin'] = 'required';
        $rules['id_barang'] = 'required';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['id_warna'] = 'required';
        $rules['id_motif'] = 'required';
        $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';

        $messages['id_dudulan.required'] = 'dudulan harus diisi';
        $messages['id_mesin.required'] = 'mesin harus diisi';
        $messages['id_barang.required'] = 'barang harus diisi';
        $messages['id_gudang.required'] = 'gudang harus diisi';
        $messages['id_gudang.not_in'] = 'gudang harus diisi';
        $messages['id_warna.required'] = 'warna harus diisi';
        $messages['id_motif.required'] = 'motif harus diisi';
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
    function getSpk(Request $request)
    {
        $term = $request->input('q');
        $data = Dudulan::selectRaw('id, nomor')->where('nomor', 'like', '%' . $term . '%')->get();

        return $data;
    }
    function getData(Request $request)
    {
        $data = getDataInspecting($request, 'inspect_dudulan');
        return $data;
    }

    function getBarang(Request $request)
    {
        return getBarangInspecting($request, 'inspect_dudulan');
    }

    function getGudang(Request $request)
    {
        $term = $request->input('term');
        $atribut = [
            'code' => 'BGD',
            'search' => $term
        ];
        $data = getGudang($atribut);
        return $data;
    }
    function getGrade()
    {
        $data = Kualitas::whereIn('id', [1, 3])->get();
        return $data;
    }
    function getKualitas(Request $request)
    {
        $data = MappingKualitas::selectRaw('id, kode, name')->where('id_kualitas', $request->grade)->whereIn('id', [12, 23, 33, 34])->get();
        return $data;
    }
    public function getStokBarang(Request $request)
    {
        return getStokInspecting($request, 'inspect_dudulan');
    }
}
