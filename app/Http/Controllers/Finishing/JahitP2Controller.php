<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\JahitP2;
use App\Models\JahitP2Detail;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class JahitP2Controller extends Controller
{
    private static $model = 'JahitP2';
    private static $modelDetail = 'JahitP2Detail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Jahit P2', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'jahit p2', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.jahit_p2.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.finishing.jahit_p2.parent');
        } else {
            $data['data'] = JahitP2::find($id);
            if (!$tipe) {
                return view('contents.production.finishing.jahit_p2.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.finishing.jahit_p2.input', $data);
                } else if ($tipe == 'output') {
                    $data['grade'] = $this->getGrade();
                    return view('contents.production.finishing.jahit_p2.output', $data);
                } else if ($tipe == 'hilang') {
                    $data['grade'] = $this->getGrade();
                    return view('contents.production.finishing.jahit_p2.hilang', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            /*withSum(['relJahitP2Detail as total_kirim' => function ($query){
                $query->where('code', 'IP2');
            }], 'volume_1')->withSum(['relJahitP2Detail as total_terima' => function ($query){
                $query->where('code', 'JP2');
            }], 'volume_1')->withSum(['relJahitP2Detail as total_hilang' => function ($query){
                $query->where('code', 'JP2H');
            }], 'volume_1')*/
            $temp = JahitP2::withSum(['relJahitP2Detail as total_kirim' => function ($query) {
                $query->where('code', 'IP2');
            }], 'volume_1')->withSum(['relJahitP2Detail as total_terima' => function ($query) {
                $query->where('code', 'JP2');
            }], 'volume_1')->orderBy('created_at', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('vendor', function ($i) {
                    return $i->relSupplier->name;
                })
                ->addColumn('total', function ($i) {
                    return $i->total_kirim . '/' . ($i->total_terima ?? 0);
                })
                ->addColumn('action', function ($i) {
                    $temp = $i->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                        'model' => 'JahitP2'
                    ];
                    $detail = JahitP2Detail::where('id_jahit_p2', $i->id)->count();
                    if ($detail > 0) {
                        $action = actionBtn($i->id, true, true, false, $validasi);
                    } else {
                        $action = actionBtn($i->id, true, true, true, $validasi);
                    }
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            $temp = JahitP2Detail::where([['id_jahit_p2', $id]])
                ->when($tipe, function ($q) use ($tipe) {
                    if ($tipe == 'input') {
                        return $q->where('code', 'IP2');
                    } else if ($tipe == 'output') {
                        return $q->where('code', 'JP2');
                    } else {
                        return $q->where('code', 'JP2H');
                    }
                })->orderBy('created_at', 'desc');;
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('tanggal_potong', function ($i) {
                    return Date::format($i->tanggal_potong, 98);
                })
                ->addColumn('mesin', function ($i) {
                    $mesin = $i->id_mesin ? $i->relMesin->name : '';
                    return $mesin;
                })
                ->addColumn('no_kikw', function ($i) {
                    $nomor = $i->id_beam ? $i->relBeam->no_kikw : '';
                    return $nomor;
                })
                ->addColumn('no_kiks', function ($i) {
                    $nomor = '';
                    if ($i->id_songket) {
                        $temp = DB::table('tbl_beam as beam')->leftJoin('tbl_nomor_kikw as no_kiks', 'no_kiks.id', 'beam.id_nomor_kikw')
                            ->selectRaw('beam.id, beam.id_nomor_kikw, beam.id_nomor_beam, no_kiks.name no_kiks')
                            ->where('beam.id', $i->id_songket)->first();
                        $nomor = $temp->no_kiks;
                    }
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
                    $temp = JahitP2::find($i->id_jahit_p2)->validated_at;
                    $validasi = [
                        'status' => false,
                        'data' => $temp,
                        'model' => 'JahitP2'
                    ];
                    if ($i->code == 'IP2') {
                        $temp = JahitP2Detail::where('id_parent', $i->id)->count();
                        if ($temp > 0) {
                            $action = '<span class="badge badge-outline badge-success">Diterima</span>';
                        } else {
                            $action = actionBtn($i->id, false, true, true, $validasi);
                        }
                    } else {
                        $action = actionBtn($i->id, false, true, true, $validasi);
                    }

                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
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
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'id_jahit_p2', 'volume_1', 'nomor']);

            $rule = $this->cekRequest($request, $mode);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        JahitP2::create($data);
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'IP2';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $id_log_stok_penerimaan = LogStokPenerimaan::create($dataLog)->id;
                            $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
                            JahitP2Detail::create($data);
                        } else {
                            if ($tipe == 'output') {
                                $data['code'] = 'JP2';
                            }
                            if ($tipe == 'hilang') {
                                $data['code'] = 'JP2H';
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_parent'], $dataLog);
                            $id_log_stok_penerimaan = LogStokPenerimaan::create($dataLog)->id;
                            $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
                            JahitP2Detail::create($data);
                        }

                        logHistory(self::$modelDetail, 'create');
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        JahitP2::find($id)->update($data);
                        logHistory(self::$model, 'update');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'IP2';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $detail = JahitP2Detail::find($id);
                            LogStokPenerimaan::find($detail->id_log_stok_penerimaan)->update($dataLog);
                            $detail->update($data);
                        } else {
                            if ($tipe == 'output') {
                                $data['code'] = 'JP2';
                            }
                            if ($tipe == 'hilang') {
                                $data['code'] = 'JP2H';
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['code'] = $data['code'];
                            $dataLog = unsetMultiKeys(['id_parent'], $dataLog);
                            $detail = JahitP2Detail::find($id);
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

            $rules['id_grade'] = 'required|not_in:0';
            $messages['id_grade.required'] = 'grade harus diisi';
            $messages['id_grade.not_in'] = 'grade harus diisi';

            $rules['id_barang'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['id_warna'] = 'required|not_in:0';
            // $rules['id_mesin'] = 'required|not_in:0';
            $rules['id_motif'] = 'required|not_in:0';
            // $rules['id_beam'] = 'required|not_in:0';
            $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';

            $messages['id_barang.required'] = 'barang harus diisi';
            $messages['id_barang.not_in'] = 'barang harus diisi';
            $messages['id_gudang.required'] = 'gudang harus diisi';
            $messages['id_gudang.not_in'] = 'gudang harus diisi';
            // $messages['id_mesin.required'] = 'mesin harus diisi';
            // $messages['id_mesin.not_in'] = 'mesin harus diisi';
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
                JahitP2::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $logId = JahitP2Detail::find($id)->id_log_stok_penerimaan;
                JahitP2Detail::find($id)->delete();
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
        return getDataJasaLuar($request, 'jahit_p2');
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
                'code' => 'IP2',
                'id_satuan_1' => 4,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('log_stok', 'log_stok_penerimaan', $condition, $term);
        } else {
            $conditon = [
                'code_terima' => ['JP2', 'JP2H'],
                'code_kirim' => 'IP2',
                'proses' => 'jahit_p2',
                'id_spk' => $id,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('detail', 'tbl_jahit_p2_detail', $conditon, $term);
        }
        return $data;
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        if ($request->tipe == 'input') {
            $atribut = [
                'code' => 'IP2',
                'search' => $term
            ];
            $data = getGudang($atribut);
        } else {
            $atribut = [
                'table' => 'tbl_jahit_p2_detail',
                'parent' => 'jahit_p2',
                'id_parent' => $request->id,
                'search' => $term

            ];
            $data = getGudang($atribut);
        }
        return $data;
    }
    function getGrade()
    {
        $data = Kualitas::whereIn('id', [1, 3])->get();
        return $data;
    }
    public function getKualitas(Request $request)
    {
        $data = MappingKualitas::selectRaw('id, kode, name')->where('id_kualitas', $request->grade)->get();
        return $data;
    }
    public function getStokBarang(Request $request)
    {
        return getStokJasaLuar($request, 'jahit_p2');
    }
}
