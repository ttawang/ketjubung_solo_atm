<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\FinishingCabut;
use App\Models\FinishingCabutDetail;
use App\Models\InspectFinishingCabut;
use App\Models\InspectFinishingCabutDetail;
use App\Models\InspectFinishingCabutKualitas;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InspectFinishingCabutController extends Controller
{
    private static $model = 'InspectFinishingCabut';
    private static $modelDetail = 'InspectFinishingCabutDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Inspect Finishing Cabut', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'inspect finishing cabut', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.inspect_finishing_cabut.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.finishing.inspect_finishing_cabut.parent', $data);
    }
    public function table(Request $request)
    {
        $spk = ($request->spk == 'null') ? null : $request->spk;
        $tanggal = $request->tanggal ?? null;
        $sub = DB::table('tbl_jigger_detail')->whereNull('deleted_at')
            ->selectRaw("tanggal, id_gudang, id_barang, id_warna, id_motif, 
                COALESCE(id_beam, 0) as id_beam, 
                COALESCE(id_songket, 0) as id_songket, 
                COALESCE(tanggal_potong, '1997-10-23') as tanggal_potong, 
                COALESCE(id_mesin, 0) as id_mesin, 
                COUNT(*) as jumlah_jigger")
            ->groupByRaw('id_gudang, id_barang, id_warna, id_motif, id_beam, id_songket, tanggal_potong, id_mesin, tanggal');
        $subJasaLuar = DB::table('tbl_finishing_cabut_detail')->whereNull('deleted_at')->selectRaw('id_inspect_retur, COUNT(*) as count_retur')->groupByRaw('id_inspect_retur');
        $temp = InspectFinishingCabutDetail::leftJoinSub($subJasaLuar, 'sub2', function ($query) {
            return $query->on('tbl_inspect_finishing_cabut_detail.id', 'sub2.id_inspect_retur');
        })->when($spk, function ($q) use ($spk) {
            return $q->where('id_finishing_cabut', $spk);
        })->when($tanggal, function ($q) use ($tanggal) {
            return $q->where('tbl_inspect_finishing_cabut_detail.tanggal', $tanggal);
        })->leftJoinSub($sub, 'sub', function ($query) {
            return $query->on('tbl_inspect_finishing_cabut_detail.id_gudang', 'sub.id_gudang')
                ->on('tbl_inspect_finishing_cabut_detail.id_barang', 'sub.id_barang')
                ->on('tbl_inspect_finishing_cabut_detail.id_warna', 'sub.id_warna')
                ->on('tbl_inspect_finishing_cabut_detail.id_motif', 'sub.id_motif')
                ->on(DB::raw('coalesce(tbl_inspect_finishing_cabut_detail.id_beam, 0)'), 'sub.id_beam')
                ->on(DB::raw('coalesce(tbl_inspect_finishing_cabut_detail.id_songket, 0)'), 'sub.id_songket')
                ->on(DB::raw("coalesce(tbl_inspect_finishing_cabut_detail.tanggal_potong, '1997-10-23')"), 'sub.tanggal_potong')
                ->on(DB::raw('coalesce(tbl_inspect_finishing_cabut_detail.id_mesin, 0)'), 'sub.id_mesin')
                ->on('tbl_inspect_finishing_cabut_detail.tanggal', 'sub.tanggal');
        })->selectRaw('tbl_inspect_finishing_cabut_detail.*, sub.jumlah_jigger, sub2.count_retur')
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
                return $i->relFinishingCabut->nomor;
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
                $temp = InspectFinishingCabutKualitas::where('id_inspect_finishing_cabut_detail', $i->id)->selectRaw('id_kualitas')->get();
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
                    'model' => 'InspectFinishingCabutDetail'
                ];

                /* if ($i->id_beam != null) {
                    $action = actionBtn($i->id, false, $i->jumlah_jigger == 0, $i->jumlah_jigger == 0, $validasi);
                } else {
                    $action = actionBtn($i->id, false, true, true, $validasi);
                } */
                $action = actionBtn($i->id, false, true, true, $validasi);

                $countRetur = $i->count_retur;
                if ($countRetur > 0) {
                    $customBtnEdit = '<a href="javascript:void(0);"
                        class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                        onclick="retur($(this), true);" data-model-inspect="InspectFinishingCabutDetail" data-model-jasa-luar="FinishingCabutDetail" data-table-parent="fc" data-route="inspect_finishing_cabut/get-barang" 
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

                    $dataLogOut = unsetMultiKeys(['id_finishing_cabut', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'FC';

                    $dataLogIn = unsetMultiKeys(['id_finishing_cabut', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'IFC';

                    $data['code'] = 'IFC';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);
                    $dataId = InspectFinishingCabutDetail::create($data)->id;
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_finishing_cabut_detail'] = $dataId;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectFinishingCabutKualitas::create($dataKualitas);
                        }
                    }
                    logHistory(self::$modelDetail, 'create');
                } else {
                    $inspect = InspectFinishingCabutDetail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_finishing_cabut', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'FC';

                    $dataLogIn = unsetMultiKeys(['id_finishing_cabut', 'id_grade_awal', 'id_kualitas'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'IFC';

                    $data['code'] = 'IFC';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    $data = unsetMultiKeys(['id_kualitas'], $data);
                    InspectFinishingCabutKualitas::where('id_inspect_finishing_cabut_detail', $id)->forceDelete();
                    if ($request->id_kualitas) {
                        $dataKualitas['id_inspect_finishing_cabut_detail'] = $id;
                        foreach ($request->id_kualitas as $i) {
                            $dataKualitas['id_kualitas'] = $i;
                            InspectFinishingCabutKualitas::create($dataKualitas);
                        }
                    }
                    InspectFinishingCabutDetail::find($id)->update($data);
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

            $logIdOut = InspectFinishingCabutDetail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = InspectFinishingCabutDetail::find($id)->id_log_stok_penerimaan_masuk;
            InspectFinishingCabutDetail::find($id)->delete();
            InspectFinishingCabutKualitas::where('id_inspect_finishing_cabut_detail', $id)->delete();
            LogStokPenerimaan::find($logIdOut)->delete();
            LogStokPenerimaan::find($logIdIn)->delete();

            $dataFCDetail = FinishingCabutDetail::where('id_inspect_retur', $id);
            if ($dataFCDetail->count() > 0) {
                LogStokPenerimaan::whereIn('id', $dataFCDetail->pluck('id_log_stok_penerimaan'))->delete();
                $dataFCDetail->delete();
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
    function getSpk(Request $request)
    {
        $term = $request->input('q');
        $data = FinishingCabut::selectRaw('id, nomor')->where('nomor', 'like', '%' . $term . '%')->orderBy('id', 'asc')->paginate(5);

        return $data;
    }
    function getData(Request $request)
    {
        $data = getDataInspecting($request, 'inspect_finishing_cabut');
        return $data;
    }
    function getBarang(Request $request)
    {
        return getBarangInspecting($request, 'inspect_finishing_cabut');
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        $atribut = [
            'code' => 'FC',
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
        return getStokInspecting($request, 'inspect_finishing_cabut');
    }
}
