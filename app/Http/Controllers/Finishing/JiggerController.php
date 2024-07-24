<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\ChemicalFinishing;
use App\Models\DryingDetail;
use App\Models\Jigger;
use App\Models\JiggerDetail;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class JiggerController extends Controller
{
    private static $model = 'Jigger';
    private static $modelDetail = 'JiggerDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Jiggger & Cuci Sarung', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'jigger', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.jigger.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.finishing.jigger.parent', $data);
    }
    public function table(Request $request)
    {
        $tanggal = $request->tanggal ?? null;
        $temp = JiggerDetail::when($tanggal, function ($q) use ($tanggal) {
            return $q->where('tanggal', $tanggal);
        })->orderBy('created_at', 'desc');
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
                return $i->relWarna->alias ?? '';
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
                    'model' => 'JiggerDetail'
                ];
                /*$cem = ChemicalFinishing::where([['id_detail', $i->id], ['code', 'CJ']])->count();
                $color =  ($cem > 0) ? 'warning' : 'default';
                $chemical = '<a href="javascript:void(0);"
                                class="btn btn-sm btn-icon btn-pure btn-' . $color . ' on-default waves-effect waves-classic"
                                data-toggle="tooltip" data-original-title="Chemical" onclick="chemical($(this));" data-id="' . $i->id . '">
                                <i class="icon md-toys" aria-hidden="true"></i>
                            </a>';*/
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
                    $dataLogOut['code'] = $request['code'];

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'JCS';

                    $data['code'] = 'JCS';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;
                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    JiggerDetail::create($data);
                    logHistory(self::$modelDetail, 'create');

                    /* langsung ke drying */
                    if ($request['code'] = 'IFC') {
                        $dataDrying = $data;
                        $dataDryingLogIn = $dataLogIn;
                        $dataDryingLogOut = $dataLogOut;
                        $dataDrying['code'] = $dataDryingLogIn['code'] = 'DR';
                        $dataDryingLogOut['code'] = 'JCS';

                        $dryingLogIdOut = LogStokPenerimaan::create($dataDryingLogOut)->id;
                        $dataDrying['id_log_stok_penerimaan_keluar'] = $dryingLogIdOut;

                        $dryingLogIdIn = LogStokPenerimaan::create($dataDryingLogIn)->id;
                        $dataDrying['id_log_stok_penerimaan_masuk'] = $dryingLogIdIn;

                        DryingDetail::create($dataDrying);
                        logHistory('DryingDetail', 'create');
                    }
                } else {
                    $inspect = JiggerDetail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = $request['code'];

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'JCS';

                    $data['code'] = 'JCS';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    JiggerDetail::find($id)->update($data);
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

            $logIdOut = JiggerDetail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = JiggerDetail::find($id)->id_log_stok_penerimaan_masuk;
            JiggerDetail::find($id)->delete();
            ChemicalFinishing::where('id_detail', $id)->delete();
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
        // $rules['id_warna'] = 'required|not_in:0';
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
    function getData(Request $request)
    {
        $data = getDataJasaDalam($request, 'jigger');
        return $data;
    }
    function getBarang(Request $request)
    {
        $term = $request->input('term');
        $gudang = $request->id_gudang ?? 0;
        $condition = [
            'code' => ['IFC', 'BGLP'],
            'id_satuan_1' => 4,
            'id_gudang' => $gudang ?? 0
        ];
        return getBarangJasaLuar('log_stok', 'log_stok_penerimaan', $condition, $term);
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        $atribut = [
            'code' => 'IFC',
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
        $data = MappingKualitas::selectRaw('id, kode, name')->where('id_kualitas', $request->grade)->get();
        return $data;
    }
    public function getStokBarang(Request $request)
    {
        // dd($request->all());
        return getStokJasaLuar($request, 'jigger');
    }
}
