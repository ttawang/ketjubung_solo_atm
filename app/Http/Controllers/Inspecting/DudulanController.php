<?php

namespace App\Http\Controllers\Inspecting;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Dudulan;
use App\Models\DudulanDetail;
use App\Models\LogStokPenerimaan;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class DudulanController extends Controller
{
    private static $model = 'Dudulan';
    private static $modelDetail = 'DudulanDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Inspecting', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dudulan', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('inspecting', 'dudulan', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.inspecting.dudulan.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.inspecting.dudulan.parent');
        } else {
            $data['data'] = Dudulan::find($id);
            if (!$tipe) {
                return view('contents.production.inspecting.dudulan.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.inspecting.dudulan.input', $data);
                } else if ($tipe == 'output') {
                    return view('contents.production.inspecting.dudulan.output', $data);
                } else if ($tipe == 'hilang') {
                    return view('contents.production.inspecting.dudulan.hilang', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $temp = Dudulan::withSum(['relDudulanDetail as total_kirim' => function ($query) {
                $query->where('code', 'BGIG');
            }], 'volume_1')
                ->withSum(['relDudulanDetail as total_terima' => function ($query) {
                    $query->where('code', 'BGD');
                }], 'volume_1')
                ->withSum(['relDudulanDetail as total_hilang' => function ($query) {
                    $query->where('code', 'BGDH');
                }], 'volume_1')
                ->orderBy('created_at', 'desc');
            $column = [
                'tanggal',
                'vendor',
                'action' => ['cetak', 'detail', 'edit', 'hapus', 'validasi']
            ];
            $position = ['jasa_luar', 'parent'];
            return getDataTable($temp, $column, 'dudulan', $position);
        } else {
            $temp = DudulanDetail::where([['id_dudulan', $id]])
                ->when($tipe, function ($q) use ($tipe) {
                    if ($tipe == 'input') {
                        return $q->where('code', 'BGIG');
                    } else if ($tipe == 'output') {
                        return $q->where('code', 'BGD');
                    } else {
                        return $q->where('code', 'BGDH');
                    }
                })->orderBy('created_at', 'desc');;
            $column = [
                'tanggal',
                'tanggal_potong',
                'mesin',
                'no_kikw',
                'no_kiks',
                'barang',
                'warna',
                'motif',
                'gudang',
                'grade',
                'action' => ['edit', 'hapus']
            ];
            $position = ['jasa_luar', 'detail'];
            return getDataTable($temp, $column, 'dudulan', $position);
        }
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
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'id_dudulan', 'volume_1', 'nomor']);

            $rule = $this->cekRequest($request, $mode);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        Dudulan::create($data);
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BGIG';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_grade'], $dataLog);
                            $id_log_stok_penerimaan = LogStokPenerimaan::create($dataLog)->id;
                            $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
                            DudulanDetail::create($data);
                        } else {
                            if ($tipe == 'output') {
                                $data['code'] = 'BGD';
                            }
                            if ($tipe == 'hilang') {
                                $data['code'] = 'BGDH';
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_parent'], $dataLog);
                            $id_log_stok_penerimaan = LogStokPenerimaan::create($dataLog)->id;
                            $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
                            DudulanDetail::create($data);
                        }

                        logHistory(self::$modelDetail, 'create');
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        Dudulan::find($id)->update($data);
                        logHistory(self::$model, 'update');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BGIG';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_grade'], $dataLog);
                            $detail = DudulanDetail::find($id);
                            LogStokPenerimaan::find($detail->id_log_stok_penerimaan)->update($dataLog);
                            $detail->update($data);
                        } else {
                            if ($tipe == 'output') {
                                $data['code'] = 'BGD';
                            }
                            if ($tipe == 'hilang') {
                                $data['code'] = 'BGDH';
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_parent'], $dataLog);
                            $detail = DudulanDetail::find($id);
                            LogStokPenerimaan::find($detail->id_log_stok_penerimaan)->update($dataLog);
                            $detail->update($data);
                        }
                        logHistory(self::$modelDetail, 'update');
                    }
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    function cekRequest($request, $mode)
    {
        $rules = [
            'tanggal' => 'required',
        ];
        $messages = [];
        if ($mode == 'parent') {
            $rules['nomor'] = 'required';
            $rules['id_supplier'] = 'required|not_in:0';
            $messages['id_supplier.required'] = 'vendor harus diisi';
            $messages['id_supplier.not_in'] = 'vendor harus diisi';
            $messages['nomor.required'] = 'nomor harus diisi';
        } else {
            $rules['id_barang'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['id_warna'] = 'required|not_in:0';
            $rules['id_mesin'] = 'required|not_in:0';
            $rules['id_motif'] = 'required|not_in:0';
            $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';

            $messages['id_barang.required'] = 'barang harus diisi';
            $messages['id_barang.not_in'] = 'barang harus diisi';
            $messages['id_gudang.required'] = 'gudang harus diisi';
            $messages['id_gudang.not_in'] = 'gudang harus diisi';
            $messages['id_mesin.required'] = 'mesin harus diisi';
            $messages['id_mesin.not_in'] = 'mesin harus diisi';
            $messages['id_warna.required'] = 'warna harus diisi';
            $messages['id_warna.not_in'] = 'warna harus diisi';
            $messages['id_motif.required'] = 'motif harus diisi';
            $messages['id_motif.not_in'] = 'motif harus diisi';
            $messages['volume_1.required'] = 'volume harus diisi';
            $messages['volume_1.numeric'] = 'volume hanya berupa angka';
            $messages['volume_1.not_in'] = 'volume tidak boleh 0';
            $messages['volume_1.gt'] = 'volume harus lebih besar dari 0';
        }

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
    public function hapus($id, $mode)
    {
        DB::beginTransaction();
        try {
            if ($mode == 'parent') {
                Dudulan::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $logId = DudulanDetail::find($id)->id_log_stok_penerimaan;
                DudulanDetail::find($id)->delete();
                LogStokPenerimaan::find($logId)->delete();
                logHistory(self::$modelDetail, 'delete');
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getData(Request $request)
    {
        return getDataJasaLuar($request, 'dudulan');
    }
    function getSupplier(Request $request)
    {
        $term = $request->input('q');
        $data = Supplier::where('name', 'like', '%' . $term . '%')->get();
        return $data;
    }
    function getBarang(Request $request)
    {
        $term = $request->input('term');
        $tipe = $request->tipe;
        $id = $request->id ?? 0;
        $gudang = $request->id_gudang ?? 0;
        if ($tipe == 'input') {
            $condition = [
                'code' => 'BGIG',
                'id_satuan_1' => 4,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('log_stok', 'log_stok_penerimaan', $condition, $term);
        } else {
            $conditon = [
                'code_terima' => ['BGD', 'BGDH'],
                'code_kirim' => 'BGIG',
                // 'id_dudulan' => $id ?? 0,
                'proses' => 'dudulan',
                'id_spk' => $id,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('detail', 'tbl_dudulan_detail', $conditon, $term);
        }
        return $data;
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        if ($request->tipe == 'input') {
            $atribut = [
                'code' => 'BGIG',
                'search' => $term
            ];
            $data = getGudang($atribut);
        } else {
            $atribut = [
                'table' => 'tbl_dudulan_detail',
                'parent' => 'dudulan',
                'id_parent' => $request->id,
                'search' => $term

            ];
            $data = getGudang($atribut);
        }
        return $data;
    }
    public function getStokBarang(Request $request)
    {
        return getStokJasaLuar($request, 'dudulan');
    }
}
