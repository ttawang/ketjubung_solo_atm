<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\PengirimanBarang;
use App\Models\PengirimanBarangDetail;
use App\Models\TipePengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengirimanBarangController extends Controller
{
    private static $model = 'PengirimanBarang';
    private static $modelDetail = 'PengirimanBarangDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $input['isPengiriman'] = true;
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pengiriman Barang', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('', 'pengiriman barang', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.production.pengiriman_barang.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = strtolower($request['search']['value']);
        $isAdminOrValidator = Auth::user()->is('Administrator') || Auth::user()->is('Validator') || Auth::user()->is('user informasi');
        $isStateKirim = $request['column'] == 'columnKirim';
        $idPengirimanBarang = $request['idPengirimanBarang'] ?? '';

        if ($isStateKirim) {
            $sub = DB::table('tbl_pengiriman_barang_detail')->selectRaw("id_pengiriman_barang, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_pengiriman_barang');
            $constructor = PengirimanBarang::withSum(['relPengirimanDetail as total_pcs' => function ($query) {
                $query->where('status', 'ASAL');
            }], 'volume_1')
                ->leftJoinSub($sub, 'sub', function ($query) {
                    return $query->on('tbl_pengiriman_barang.id', 'sub.id_pengiriman_barang');
                })
                ->when($search, function ($query, $value) {
                    return $query->whereRaw("LOWER(nomor) LIKE '%$value%'");
                })->when(!$isAdminOrValidator, function ($query) {
                    return $query->where('created_by', Auth::id());
                })->selectRaw('tbl_pengiriman_barang.*, sub.count_detail')->orderBy('tanggal', 'DESC');
            $attributes = [];
        } else {
            $roles_id = Auth::user()->roles_id;
            $input['usedAction'] = 'NOUSED';
            $input['btnExtras'] = ['<button type="button" onclick="acceptForm(%id, true);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-eye mr-2"></i>
            </button><button type="button" onclick="acceptForm(%id);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-check mr-2"></i>
            </button>'];
            $unionConstruction = PengirimanBarangDetail::when($search, function ($query, $param) {
                return $query->whereHas('relPengirimanBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(nomor) LIKE '%$param%'");
                });
            })
                ->selectRaw('id, id_pengiriman_barang, id_barang, id_gudang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, volume_1 as volume_1, volume_2 as volume_2, id_beam, id_songket, tanggal_potong, id_mesin, tipe_pra_tenun, accepted_at, id_parent_detail, id_log_stok, catatan')
                ->when($idPengirimanBarang, function ($query, $value) {
                    return $query->where('id_pengiriman_barang', $value);
                })
                ->where('status', 'TUJUAN')
                ->whereNotNull('id_parent_detail')
                ->whereNotNull('accepted_at')
                ->when($roles_id != 1 && $roles_id != 8, function ($query) use ($roles_id) {
                    return $query->whereHas('throughTipePengiriman', function ($query) use ($roles_id) {
                        return $query->where('roles_id_tujuan', $roles_id);
                    });
                });

            $constructor = PengirimanBarangDetail::when($search, function ($query, $param) {
                return $query->whereHas('relPengirimanBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(nomor) LIKE '%$param%'");
                });
            })
                ->selectRaw('id, id_pengiriman_barang, id_barang, NULL::INTEGER as id_gudang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, volume_1 as volume_1, volume_2 as volume_2, id_beam, id_songket, tanggal_potong, id_mesin, tipe_pra_tenun, accepted_at, id_parent_detail, NULL::INTEGER as id_log_stok, catatan')
                ->when($idPengirimanBarang, function ($query, $value) {
                    return $query->where('id_pengiriman_barang', $value);
                })
                ->whereHas('relPengirimanBarang', function ($query) {
                    $query->where(function ($query) {
                        $query->where("id", "<=", 965)->whereNotIn("id_tipe_pengiriman", [7]);
                    })->orWhere(function ($query) {
                        $query->where("id", ">", 965);
                    });
                })
                ->where('status', 'ASAL')
                ->whereNull('id_parent_detail')
                ->whereNull('accepted_at')
                ->when($roles_id != 1 && $roles_id != 8, function ($query) use ($roles_id) {
                    return $query->whereHas('throughTipePengiriman', function ($query) use ($roles_id) {
                        return $query->where('roles_id_tujuan', $roles_id);
                    });
                })
                ->unionAll($unionConstruction)
                ->orderByRaw('accepted_at DESC, id_mesin DESC');
            $attributes = ['relGudang', 'relNomorPengiriman'];
        }

        return Define::fetch2($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['extraData'] = ['state' => $request['state']];
        $search = $request['search']['value'];
        $constructor = PengirimanBarangDetail::withCount('relDetailTujuan')
            ->where('id_pengiriman_barang', $id)
            ->where('status', $request['status'])
            ->orderBy('created_at', 'DESC');
        if (!empty($search)) {
            $constructor->where(function ($q) use ($search) {
                $q->whereHas('relBeam.relNomorKikw', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orwhereHas('relBeam.relNomorBeam', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orwhereHas('relMotif', function ($q) use ($search) {
                    $q->where('alias', 'like', '%' . $search . '%');
                })->orwhereHas('relWarna', function ($q) use ($search) {
                    $q->where('alias', 'like', '%' . $search . '%');
                })->orwhereHas('relBarang', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        $attributes = ['relSatuan', 'relGudang'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = PengirimanBarang::where('id', $idParent)->first();
            $attr['idLogStok']  = '';
            $attr['state']      = $request['state'];
            $attr['idGudang']   = ($request['state'] == 'input') ? $data->id_gudang_asal : $data->id_gudang_tujuan;
            $attr['id_tipe']    = $data->id_tipe_pengiriman;
            $attr['code_tipe']  = generateCodePengiriman($data->id_tipe_pengiriman, ($request['state'] == 'input'), '');
            $response['render'] = view('contents.production.pengiriman_barang.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.pengiriman_barang.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = PengirimanBarangDetail::where('id', $id)->first();
            $idTipe = $data->relPengirimanBarang()->value('id_tipe_pengiriman');
            $data['tanggal'] = $data->relPengirimanBarang()->value('tanggal');
            $response['selected'] = [
                'select_gudang_2' => [
                    'id' => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],

            ];

            $arrayBarang = [
                'select_barang' => [
                    'id'             => $data->id,
                    'text'           => $data->nama_barang,
                    'id_barang'      => $data->id_barang,
                    'id_warna'       => $data->id_warna,
                    'nama_warna'     => $data->nama_warna,
                    'id_motif'       => $data->id_motif,
                    'nama_motif'     => $data->nama_motif,
                    'volume_1'       => $data->volume_1,
                    'volume_2'       => $data->volume_2,
                    'id_satuan_1'    => $data->id_satuan_1,
                    'nama_satuan_1'  => $data->nama_satuan_1,
                    'id_satuan_2'    => $data->id_satuan_2 ?? '',
                    'nama_satuan_2'  => $data->nama_satuan_2,
                    'id_gudang'      => $data->id_gudang,
                    'is_sizing'      => $data->is_sizing == 'null' ? NULL : $data->is_sizing,
                    'id_beam'        => $data->id_beam,
                    'id_songket'        => $data->id_songket,
                    'tanggal_potong'        => $data->tanggal_potong,
                    'no_beam'        => $data->no_beam,
                    'id_mesin'       => $data->id_mesin,
                    'nama_mesin'     => $data->nama_mesin,
                    'no_kikw'        => $data->no_kikw,
                    'no_kiks'        => $data->no_kiks,
                    'tipe_pra_tenun' => $data->tipe_pra_tenun,
                    'id_grade'       => $data->id_grade,
                    'nama_grade'     => $data->relGrade()->value('grade') . ' - ' . $data->relGrade()->value('alias'),
                    'id_kualitas'    => $data->id_kualitas,
                    'nama_kualitas'  => $data->relKualitas()->value('kode') . ' - ' . $data->relKualitas()->value('name'),
                ]
            ];

            $response['selected'] = array_merge($response['selected'], $arrayBarang);

            if ($request['state'] == 'output') {
                $arraySatuan1 = [
                    'select_satuan_1' => [
                        'id'   => $data->id_satuan_1,
                        'text' => $data->relSatuan1()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arraySatuan1);
            }

            if ($data->id_satuan_2 != null) {
                $arraySatuan2 = [
                    'select_satuan_2' => [
                        'id'   => $data->id_satuan_2,
                        'text' => $data->relSatuan2()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arraySatuan2);
            }

            if ($data->id_warna != null) {
                $arrayWarna = [
                    'select_warna' => [
                        'id'   => $data->id_warna,
                        'text' => $data->relWarna()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayWarna);
            }

            $idParent = $data->id_pengiriman_barang;
            $attr['idLogStok']  = $data->id_log_stok;
            $attr['state']      = $request['state'];
            $attr['idGudang']   = ($request['state'] == 'input') ? $data->relPengirimanBarang()->value('id_gudang_asal') : $data->relPengirimanBarang()->value('id_gudang_tujuan');
            $attr['id_tipe']    = $idTipe;
            $attr['code_tipe']  = generateCodePengiriman($attr['id_tipe'], ($request['state'] == 'input'), '');
            $response['render'] = view('contents.production.pengiriman_barang.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = PengirimanBarang::where('id', $id)->first();
            $response['selected'] = [];
            if ($data->id_tipe_pengiriman != null) {
                $arrayTipePengiriman = [
                    'select_tipe_pengiriman' => [
                        'id'   => $data->id_tipe_pengiriman,
                        'text' => $data->relTipePengiriman()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayTipePengiriman);
            }
            $response['render'] = view('contents.production.pengiriman_barang.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;

        DB::beginTransaction();
        if ($isDetail) {
            try {
                $input['volume_1'] = floatValue($input['volume_1']);
                if (isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
                if (isset($input['id_mesin'])) $logStokPenerimaan['id_mesin'] = $input['id_mesin'];
                if ($request->state == 'input') {
                    $input['status'] = 'ASAL';
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                    if (isset($input['volume_2'])) {
                        $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                        $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    }
                } else {
                    $input['status'] = 'TUJUAN';
                    if ($request['id_tipe'] == 2) $logStokPenerimaan['id_mesin'] = null;
                    $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                    if (isset($input['volume_2'])) {
                        $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                        $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    }
                }
                $logStokPenerimaan['id_gudang']   = $input['id_gudang'];
                // $logStokPenerimaan['code']        = generateCodePengiriman($request['id_tipe'], $request->state, $request['current_code']);
                $logStokPenerimaan['code']        = $request['current_code'];
                $logStokPenerimaan['tanggal']     = $input['tanggal'];
                $logStokPenerimaan['id_barang']   = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1'] = $input['id_satuan_1'];
                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];

                if (isset($input['id_beam'])) {
                    $logStokPenerimaan['id_beam']  = $input['id_beam'];
                    if ($request['id_tipe'] != 7 && $request['id_tipe'] != 8) {
                        $dataBeam = Beam::where('id', $input['id_beam'])->first();
                        $logStokPenerimaan['tipe_pra_tenun'] = $input['tipe_pra_tenun'] ?? null;
                        $logStokPenerimaan['is_sizing']      = $dataBeam->is_sizing;
                    }
                }
                if (isset($input['id_songket'])) {
                    $logStokPenerimaan['id_songket']  = $input['id_songket'];
                }
                if (isset($input['tanggal_potong'])) {
                    $logStokPenerimaan['tanggal_potong']  = $input['tanggal_potong'];
                }

                $input['id_log_stok'] = LogStokPenerimaan::create($logStokPenerimaan)->id;
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        } else {
            if (isset($input['id_tipe_pengiriman'])) {
                $gudang = TipePengiriman::where('id', $input['id_tipe_pengiriman'])->first();
                $input['id_gudang_asal'] = $gudang->id_gudang_asal;
                $input['id_gudang_tujuan'] = $gudang->id_gudang_tujuan;

                if ($input['id_tipe_pengiriman'] == 4 || $input['id_tipe_pengiriman'] == 17) return Define::store($input, $usingModel, [], true);
            }
        }

        return Define::store($input, $usingModel);
    }

    public function update($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $input['volume_1'] = floatValue($input['volume_1']);
                if (isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
                if (isset($input['id_mesin'])) $logStokPenerimaan['id_mesin'] = $input['id_mesin'];
                if ($request->state == 'input') {
                    $input['status'] = 'ASAL';
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                    if (isset($input['volume_2'])) {
                        $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                        $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    }
                } else {
                    $input['status'] = 'TUJUAN';
                    $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                    if ($request['id_tipe'] == 2) $logStokPenerimaan['id_mesin'] = null;
                    if (isset($input['volume_2'])) {
                        $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                        $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    }
                }

                $logStokPenerimaan['id_gudang']   = $input['id_gudang'];
                $logStokPenerimaan['tanggal']     = $input['tanggal'];
                $logStokPenerimaan['id_barang']   = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1'] = $input['id_satuan_1'];
                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];

                if (isset($input['id_beam'])) {
                    $logStokPenerimaan['id_beam']  = $input['id_beam'];
                    if ($request['id_tipe'] != 7 && $request['id_tipe'] != 8) {
                        $dataBeam = Beam::where('id', $input['id_beam'])->first();
                        $logStokPenerimaan['tipe_pra_tenun'] = $input['tipe_pra_tenun'] ?? null;
                        $logStokPenerimaan['is_sizing']      = $dataBeam->is_sizing;
                    }
                }
                if (isset($input['id_songket'])) {
                    $logStokPenerimaan['id_songket']  = $input['id_songket'];
                }
                if (isset($input['tanggal_potong'])) {
                    $logStokPenerimaan['tanggal_potong']  = $input['tanggal_potong'];
                }

                LogStokPenerimaan::where('id', $request['id_log_stok'])->update($logStokPenerimaan);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        } else {
            if (!isset($input['id_tipe_pengiriman'])) {
                $input['id_tipe_pengiriman'] = null;
                $input['id_gudang_asal']     = 1;
                $input['id_gudang_tujuan']   = 1;
            } else {
                $gudang = TipePengiriman::where('id', $input['id_tipe_pengiriman'])->first();
                $input['id_gudang_asal'] = $gudang->id_gudang_asal;
                $input['id_gudang_tujuan'] = $gudang->id_gudang_tujuan;
            }
            if (!isset($input['txt_tipe_pengiriman'])) $input['txt_tipe_pengiriman'] = null;
        }

        return Define::update($input, $usingModel, $id);
    }

    public function destroy($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        if ($isDetail) {
            $detailData = PengirimanBarangDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
