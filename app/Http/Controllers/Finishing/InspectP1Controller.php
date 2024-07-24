<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\InspectP1;
use App\Models\InspectP1Detail;
use App\Models\InspectP1Kualitas;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Models\P1;
use App\Models\P1Detail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;
use Yajra\DataTables\DataTables;

class InspectP1Controller extends Controller
{
    private static $model = 'InspectP1';
    private static $modelDetail = 'InspectP1Detail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Inspect P1', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'inspect p1', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.inspect_p1.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.finishing.inspect_p1.parent', $data);
    }
    public function table(Request $request)
    {
        $spk = ($request->spk == 'null') ? null : $request->spk;
        $tanggal = $request->tanggal ?? null;
        $sub = DB::table('tbl_finishing_cabut_detail')->whereNull('deleted_at')
            ->selectRaw("tanggal, id_gudang, id_barang, id_warna, id_motif,
                COALESCE(id_beam, 0) as id_beam,
                COALESCE(id_songket, 0) as id_songket,
                COALESCE(tanggal_potong, '1997-10-23') as tanggal_potong,
                COALESCE(id_mesin, 0) as id_mesin, COUNT(*) as jumlah_finishing_cabut")->groupByRaw("id_gudang, id_barang, id_warna, id_motif, id_beam, id_songket, tanggal_potong, id_mesin, tanggal");
        $subJasaLuar = DB::table('tbl_p1_detail')->whereNull('deleted_at')->selectRaw('id_inspect_retur, COUNT(*) as count_retur')->groupByRaw('id_inspect_retur');
        $temp = InspectP1Detail::when($spk, function ($q) use ($spk) {
            return $q->where('id_p1', $spk);
        })->when($tanggal, function ($q) use ($tanggal) {
            return $q->where('tbl_inspect_p1_detail.tanggal', $tanggal);
        })
            ->leftJoinSub($subJasaLuar, 'sub2', function ($query) {
                return $query->on('tbl_inspect_p1_detail.id', 'sub2.id_inspect_retur');
            })
            ->leftJoinSub($sub, 'sub', function ($query) {
                return $query->on('tbl_inspect_p1_detail.id_gudang', 'sub.id_gudang')
                    ->on('tbl_inspect_p1_detail.id_barang', 'sub.id_barang')
                    ->on('tbl_inspect_p1_detail.id_warna', 'sub.id_warna')
                    ->on('tbl_inspect_p1_detail.id_motif', 'sub.id_motif')
                    ->on(DB::raw('coalesce(tbl_inspect_p1_detail.id_beam, 0)'), 'sub.id_beam')
                    ->on(DB::raw('coalesce(tbl_inspect_p1_detail.id_songket, 0)'), 'sub.id_songket')
                    ->on(DB::raw("coalesce(tbl_inspect_p1_detail.tanggal_potong, '1997-10-23')"), 'sub.tanggal_potong')
                    ->on(DB::raw('coalesce(tbl_inspect_p1_detail.id_mesin, 0)'), 'sub.id_mesin')
                    ->on('tbl_inspect_p1_detail.tanggal', 'sub.tanggal');
            })->selectRaw('tbl_inspect_p1_detail.*, sub.jumlah_finishing_cabut, sub2.count_retur')
            ->orderBy('created_at', 'desc');
        return DataTables::of($temp)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('tanggal_potong', function ($i) {
                return Date::format($i->tanggal_potong, 98);
            })
            ->addColumn('spk', function ($i) {
                return $i->relP1->nomor;
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
            ->addColumn('kualitas', function ($i) {
                $kualitas = '';
                $temp = InspectP1Kualitas::where('id_inspect_p1_detail', $i->id)->selectRaw('id_kualitas')->get();
                if ($temp->count() > 0) {
                    foreach ($temp as $i) {
                        $kualitas .= $i->relKualitas->kode . ', ';
                    }
                    $kualitas = rtrim($kualitas, ', ');
                }
                return $kualitas;
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
                    'model' => 'InspectP1Detail'
                ];
                // if ($i->id_beam != null) {
                //     $action = actionBtn($i->id, false, $i->jumlah_finishing_cabut == 0, $i->jumlah_finishing_cabut == 0, $validasi);
                // } else {
                //     $action = actionBtn($i->id, false, true, true, $validasi);
                // }
                $action = actionBtn($i->id, false, true, true, $validasi);

                $countRetur = $i->count_retur;
                if ($countRetur > 0) {
                    $customBtnEdit = '<a href="javascript:void(0);"
                        class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                        onclick="retur($(this), true);" data-model-inspect="InspectP1Detail" data-model-jasa-luar="P1Detail" data-table-parent="p1" data-route="inspect_p1/get-barang" 
                        data-id-inspecting="' . $i->id . '" 
                        data-volume="' . $i->volume_1 . '" 
                        data-id-logstok-inspect-keluar="' . $i->id_log_stok_penerimaan_keluar . '"
                        data-id-logstok-inspect-masuk="' . $i->id_log_stok_penerimaan_masuk . '"
                        data-id-logstok-jasa-luar="' . $i->relReturInspect()->value('id_log_stok_penerimaan') . '"
                        ><i class="icon md-edit" aria-hidden="true"></i>
                    </a>';
                    $customBtn = ($countRetur > 1) ? '' : $customBtnEdit . '<a href="javascript:void(0);"
                        class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                        onclick="hapus($(this));" data-id="' . $i->id . '">
                        <i class="icon md-delete" aria-hidden="true"></i>
                    </a>';
                    $action = actionBtn($i->id, false, false, false, $validasi, $customBtn);
                }

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
                    $dataLogOut = unsetMultiKeys(['id_p1', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'P1';

                    $dataLogIn = unsetMultiKeys(['id_p1', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'IP1';

                    $data['code'] = 'IP1';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);
                    $dataId = InspectP1Detail::create($data)->id;
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_p1_detail'] = $dataId;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectP1Kualitas::create($dataKualitas);
                        }
                    }
                    logHistory(self::$modelDetail, 'create');
                } else {
                    $inspect = InspectP1Detail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_p1', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'P1';

                    $dataLogIn = unsetMultiKeys(['id_p1', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'IP1';

                    $data['code'] = 'IP1';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);
                    InspectP1Kualitas::where('id_inspect_p1_detail', $id)->forceDelete();
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_p1_detail'] = $id;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectP1Kualitas::create($dataKualitas);
                        }
                    }
                    InspectP1Detail::find($id)->update($data);
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

            $logIdOut = InspectP1Detail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = InspectP1Detail::find($id)->id_log_stok_penerimaan_masuk;
            InspectP1Detail::find($id)->delete();
            InspectP1Kualitas::where('id_inspect_p1_detail', $id)->delete();
            LogStokPenerimaan::find($logIdOut)->delete();
            LogStokPenerimaan::find($logIdIn)->delete();

            $dataP1Detail = P1Detail::where('id_inspect_retur', $id);
            if ($dataP1Detail->count() > 0) {
                LogStokPenerimaan::whereIn('id', $dataP1Detail->pluck('id_log_stok_penerimaan'))->delete();
                $dataP1Detail->delete();
            }

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
        /* if ($request->input('id_grade') != 1) {
            $rules['id_kualitas'] = 'required|not_in:0';
            $messages['id_kualitas.required'] = 'kualitas harus diisi';
            $messages['id_kualitas.not_in'] = 'kualitas harus diisi';
        } */
        // $rules['id_mesin'] = 'required|not_in:0';
        $rules['id_barang'] = 'required|not_in:0';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['id_warna'] = 'required|not_in:0';
        $rules['id_gudang'] = 'required|not_in:0';
        $rules['id_motif'] = 'required|not_in:0';
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
        $data = P1::selectRaw('id, nomor')->where('nomor', 'like', '%' . $term . '%')->orderBy('id', 'asc')->paginate(5);

        return $data;
    }
    function getData(Request $request)
    {
        $data = getDataInspecting($request, 'inspect_p1');
        return $data;
    }
    function getBarang(Request $request)
    {
        return getBarangInspecting($request, 'inspect_p1');
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        $atribut = [
            'code' => 'P1',
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
        return getStokInspecting($request, 'inspect_p1');
    }
}
