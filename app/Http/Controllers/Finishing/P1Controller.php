<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Models\P1;
use App\Models\P1Detail;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class P1Controller extends Controller
{
    private static $model = 'P1';
    private static $modelDetail = 'P1Detail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'P1', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'p1', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.p1.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.finishing.p1.parent');
        } else {
            $data['data'] = P1::find($id);
            if (!$tipe) {
                return view('contents.production.finishing.p1.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.finishing.p1.input', $data);
                } else if ($tipe == 'output') {
                    $data['grade'] = $this->getGrade();
                    return view('contents.production.finishing.p1.output', $data);
                } else if ($tipe == 'hilang') {
                    $data['grade'] = $this->getGrade();
                    return view('contents.production.finishing.p1.hilang', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $temp = P1::withSum(['relP1Detail as total_kirim' => function ($query) {
                $query->where('code', 'JS');
            }], 'volume_1')->withSum(['relP1Detail as total_terima' => function ($query) {
                $query->where('code', 'P1');
            }], 'volume_1')->withSum(['relP1Detail as total_hilang' => function ($query) {
                $query->where('code', 'P1H');
            }], 'volume_1')->orderBy('created_at', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('vendor', function ($i) {
                    return $i->relSupplier->name;
                })
                ->addColumn('action', function ($i) {
                    $temp = $i->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                        'model' => 'P1'
                    ];
                    $detail = P1Detail::where('id_p1', $i->id)->count();
                    $cetak =
                        '<a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic" 
                            data-id="' . $i->id . '" data-proses="p1" onclick="cetak($(this));">
                            <i class="icon md-print" aria-hidden="true"></i>
                        </a>';
                    if ($detail > 0) {
                        $action = actionBtn($i->id, true, true, false, $validasi, $cetak);
                    } else {
                        $action = actionBtn($i->id, true, true, true, $validasi, $cetak);
                    }
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            $sub = DB::table('tbl_inspect_p1_detail')->whereNull('deleted_at')->where('id_p1', $id)
                ->selectRaw("tanggal, id_gudang, id_barang, id_warna, id_motif, COALESCE(id_beam, 0) as id_beam,COALESCE(id_songket, 0) as id_songket, COALESCE(tanggal_potong, '1997-10-23') as tanggal_potong, COALESCE(id_mesin, 0) as id_mesin, COUNT(*) as jumlah_inspecting_p1")->groupByRaw('id_gudang, id_barang, id_warna, id_motif, id_beam, id_songket, tanggal_potong, id_mesin, tanggal');
            $temp = P1Detail::leftJoinSub($sub, 'sub', function ($query) {
                return $query->on('tbl_p1_detail.id_gudang', 'sub.id_gudang')
                    ->on('tbl_p1_detail.id_barang', 'sub.id_barang')
                    ->on('tbl_p1_detail.id_warna', 'sub.id_warna')
                    ->on('tbl_p1_detail.id_motif', 'sub.id_motif')
                    ->on(DB::raw('coalesce(tbl_p1_detail.id_beam, 0)'), 'sub.id_beam')
                    ->on(DB::raw('coalesce(tbl_p1_detail.id_songket, 0)'), 'sub.id_songket')
                    ->on(DB::raw("coalesce(tbl_p1_detail.tanggal_potong, '1997-10-23')"), 'sub.tanggal_potong')
                    ->on(DB::raw('coalesce(tbl_p1_detail.id_mesin, 0)'), 'sub.id_mesin')
                    ->on('tbl_p1_detail.tanggal', 'sub.tanggal');
            })->selectRaw('tbl_p1_detail.*, sub.jumlah_inspecting_p1')
                ->where([['id_p1', $id]])
                ->when($tipe, function ($q) use ($tipe) {
                    if ($tipe == 'input') {
                        return $q->where('code', 'JS');
                    } else if ($tipe == 'output') {
                        return $q->where('code', 'P1');
                    } else {
                        return $q->where('code', 'P1H');
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
                    $temp = P1::find($i->id_p1)->validated_at;
                    $validasi = [
                        'status' => false,
                        'data' => $temp,
                        'model' => 'P1'
                    ];
                    if ($i->code == 'JS') {
                        $temp = P1Detail::where('id_parent', $i->id)->count();
                        if ($temp > 0) {
                            $action = '<span class="badge badge-outline badge-success">Diterima</span>';
                        } else {
                            $action = actionBtn($i->id, false, true, true, $validasi);
                        }

                        if ($i->id_inspect_retur != null) $action = '<span class="badge badge-outline badge-danger">Retur</span>';
                    } else {
                        $action = actionBtn($i->id, false, $i->jumlah_inspecting_p1 == 0, $i->jumlah_inspecting_p1 == 0, $validasi);
                    }

                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        }
    }
    public function hapus($id, $mode)
    {
        DB::beginTransaction();
        try {
            if ($mode == 'parent') {
                P1::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $logId = P1Detail::find($id)->id_log_stok_penerimaan;
                P1Detail::find($id)->delete();
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
        return getDataJasaLuar($request, 'p1');
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
                'code' => 'JS',
                'id_satuan_1' => 4,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('log_stok', 'log_stok_penerimaan', $condition, $term);
        } else {
            $conditon = [
                'code_terima' => ['P1', 'P1H'],
                'code_kirim' => 'JS',
                'proses' => 'p1',
                'id_spk' => $id,
                'id_gudang' => $gudang ?? 0
            ];
            $data = getBarangJasaLuar('detail', 'tbl_p1_detail', $conditon, $term);
        }
        return $data;
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        if ($request->tipe == 'input') {
            $atribut = [
                'code' => 'JS',
                'search' => $term
            ];
            $data = getGudang($atribut);
        } else {
            $atribut = [
                'table' => 'tbl_p1_detail',
                'parent' => 'p1',
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
        return getStokJasaLuar($request, 'p1');
    }
    public function terimaSemua(Request $request)
    {
    }
}
