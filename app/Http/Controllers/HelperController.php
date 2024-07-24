<?php

namespace App\Http\Controllers;

use App\Exports\StokopnameExport;
use App\Helpers\Define;
use App\Imports\Import;
use App\Models\AbsensiPekerja;
use App\Models\Barang;
use App\Models\Beam;
use App\Models\DistribusiPakanDetail;
use App\Models\DoublingDetail;
use App\Models\Dyeing;
use App\Models\DyeingDetail;
use App\Models\DyeingGresikDetail;
use App\Models\DyeingGresikWarna;
use App\Models\DyeingGreyDetail;
use App\Models\DyeingJasaLuarDetail;
use App\Models\Gudang;
use App\Models\LogStokPenerimaan;
use App\Models\LogStokPenerimaanDyeing;
use App\Models\MappingMenu;
use App\Models\DyeingWarna;
use App\Models\Group;
use App\Models\Menu;
use App\Models\PenerimaanBarangDetail;
use App\Models\PenerimaanBarangDetailDyeing;
use App\Models\PengirimanBarangDetail;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\Tipe;
use App\Models\TipePengiriman;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MappingKualitas;
use App\Models\Kualitas;
use App\Models\MappingPekerjaMesin;
use App\Models\Mesin;
use App\Models\MesinHistory;
use App\Models\Motif;
use App\Models\NomorBeam;
use App\Models\NomorKikw;
use App\Models\OperasionalDyeingDetail;
use App\Models\PakanDetail;
use App\Models\Pekerja;
use App\Models\PengirimanBarang;
use App\Models\ProductionCode;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Stokopname;
use App\Models\TenunDetail;
use App\Models\WarpingDetail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;

class HelperController extends Controller
{
    public function detailFormDatabase($id)
    {
        $extraFix = request('extrafix') . '.' ?? '';
        $data = getModel(request('model'))::where('id', $id)->first();
        $response['render'] = view('contents.database.' . request('prefix') . '.' . request('suffix') . '.' . $extraFix . 'detail', compact('id', 'data'))->render();
        return $response;
    }

    public function detailForm($id)
    {
        $model = request('model');
        $data  = getModel($model)::where('id', $id)->first();
        if ($model == 'PengirimanBarang') {
            if ($data->id_tipe_pengiriman === 7) {
                $data  = getModel($model)::where('id', $id)->withSum(['relPengirimanDetail as total' => function ($q) {
                    $q->where('status', 'ASAL');
                }], 'volume_1')->first();
            }
        }
        $conditions = request('conditions') ? unsetMultiKeys(['originalTitle', 'toggle'], request('conditions')) : [];
        $validatedAt = $data->validated_at ?? null;

        if (request('subModel') && $validatedAt == null) {
            $data = getModel(request('subModel'))::selectRaw('CONCAT(id_barang, id_motif) as id, id_barang, id_motif, SUM(volume_1) as volume')
                ->where($conditions)->groupBy('id_barang', 'id_motif')->first();
        }

        $tools = checkValidatedButtons($data->validate_at != null, ['tambahDetail', 'refreshDetail'], true);
        $response['data'] = $data;
        if (request('customView') && request('customView') != '') {
            $response['render'] = view(request('customView'), compact('id', 'data', 'tools', 'model'))->render();
        } else {
            $response['render'] = view('contents.production.' . request('prefix') . '.' . request('suffix') . '.' . 'detail', compact('id', 'data', 'tools', 'model'))->render();
        }
        return $response;
    }

    public function warnaForm($id)
    {
        if (request('form') == 'dyeing') {
            $data = DyeingDetail::where('id', $id)->first();
            $warna = DyeingWarna::where('id_dyeing_detail', $id)->orderBy('id', 'DESC')->get();
            return view('contents.production.dyeing.detail-warna', compact('data', 'warna', 'id'))->render();
        } else {
            $data = DyeingGresikDetail::where('id', $id)->first();
            $warna = DyeingGresikWarna::where('id_dyeing_gresik_detail', $id)->orderBy('id', 'DESC')->get();
            return view('contents.production.dyeing_gresik.detail-warna', compact('data', 'warna', 'id'))->render();
        }
    }

    public function detailFormView($id = null)
    {
        $model = request('model');
        $root = request('root') ?? 'production.';
        if ($id != null) {
            $data  = getModel($model)::where('id', $id)->first();
            $tools = checkValidatedButtons($data->validated_at != null, ['tambahDetail', 'refreshDetail'], true);
            if ($model == 'PenomoranBeamRetur') {
                $response['selected'] = [
                    'select_gudang' => [
                        'id'        => $data->relPenomoranBeamReturDetail->id_gudang,
                        'text'      => $data->relPenomoranBeamReturDetail->relGudang()->value('name'),
                    ],
                    'select_barang' => [
                        'id'             => $data->relPenomoranBeamReturDetail->id,
                        'text'           => $data->relPenomoranBeamReturDetail->nama_barang,
                        'id_barang'      => $data->relPenomoranBeamReturDetail->id_barang,
                        'nama_barang'    => $data->relPenomoranBeamReturDetail->nama_barang,
                        'id_warna'       => $data->relPenomoranBeamReturDetail->id_warna,
                        'nama_warna'     => $data->relPenomoranBeamReturDetail->relWarna()->value('name'),
                        'id_motif'       => $data->relPenomoranBeamReturDetail->id_motif,
                        'nama_motif'     => $data->relPenomoranBeamReturDetail->relMotif()->value('name'),
                        'stok_utama'     => $data->relPenomoranBeamReturDetail->volume_1,
                        'stok_pilihan'   => $data->relPenomoranBeamReturDetail->volume_2,
                        'id_satuan_1'    => $data->relPenomoranBeamReturDetail->id_satuan_1,
                        'id_satuan_2'    => $data->relPenomoranBeamReturDetail->id_satuan_2 ?? '',
                        'id_gudang'      => $data->relPenomoranBeamReturDetail->id_gudang,
                        'code'           => $data->relPenomoranBeamReturDetail->code,
                        'id_beam'        => $data->relPenomoranBeamReturDetail->id_beam,
                        'no_beam'        => $data->relPenomoranBeamReturDetail->throughNomorBeam()->value('name'),
                        'no_kikw'        => $data->relPenomoranBeamReturDetail->throughNomorKikw()->value('name'),
                        'tipe_pra_tenun' => $data->relPenomoranBeamReturDetail->relBeam()->value('tipe_pra_tenun'),
                        'is_sizing'      => $data->relPenomoranBeamReturDetail->relBeam()->value('is_sizing') ?? 'TIDAK',
                        'id_mesin'       => $data->relPenomoranBeamReturDetail->id_mesin,
                        'nama_mesin'     => $data->relPenomoranBeamReturDetail->relMesin()->value('name')
                    ],
                    'select_motif' => [
                        'id'   => $data->id_motif,
                        'text' => $data->relMotif()->value('alias')
                    ],
                    'select_mesin' => [
                        'id'   => $data->id_mesin,
                        'text' => $data->relMesin()->value('name')
                    ],
                    'select_tipe_pra_tenun' => [
                        'id'   => $data->tipe_pra_tenun,
                        'text' => $data->tipe_pra_tenun
                    ]
                ];
            } else if ($model == 'SaldoAwal') {
                $response['selected'] = [
                    'select_proses' => [
                        'id' => $data->code,
                        'text' => SaldoAwalCodeText($data->code),
                        'id_gudang' => $data->id_gudang
                    ],
                    'select_gudang' => [
                        'id' => $data->id_gudang,
                        'text' => $data->relGudang()->value('name')
                    ],
                    'select_barang' => [
                        'id' => $data->id_barang,
                        'text' => $data->relBarang()->value('name')
                    ],
                    'select_satuan_1' => [
                        'id' => $data->id_satuan_1,
                        'text' => $data->relSatuan1()->value('name')
                    ]
                ];

                if ($data->id_satuan_2 != null) {
                    $arrayWarna = [
                        'select_satuan_2' => [
                            'id'   => $data->id_satuan_2,
                            'text' => $data->relSatuan2()->value('name')
                        ]
                    ];
                    $response['selected'] = array_merge($response['selected'], $arrayWarna);
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

                if ($data->id_motif != null) {
                    $arrayMotif = [
                        'select_motif' => [
                            'id'   => $data->id_motif,
                            'text' => $data->relMotif()->value('name')
                        ]
                    ];
                    $response['selected'] = array_merge($response['selected'], $arrayMotif);
                }

                if ($data->id_beam != null) {
                    $arrayBeam = [
                        'select_nomor_beam' => [
                            'id'   => $data->relBeam()->value('id_nomor_beam'),
                            'text' => $data->throughNomorBeam()->value('name')
                        ],
                        'select_mesin' => [
                            'id'   => $data->relMesinHistoryLatest()->value('id_mesin'),
                            'text' => $data->relBeam()->first()->mesin
                        ],
                        'select_tipe_pra_tenun' => [
                            'id'   => $data->relBeam()->value('tipe_pra_tenun'),
                            'text' => $data->relBeam()->value('tipe_pra_tenun')
                        ],
                    ];
                    $response['selected'] = array_merge($response['selected'], $arrayBeam);
                }
            }
            $response['data'] = $data;
            $response['render'] = view('contents.' . $root . '' . request('prefix') . '.' . request('suffix') . '.' . 'form', compact('id', 'data', 'tools', 'model'))->render();
        } else {
            $data = [];
            $response['data'] = $data;
            $response['render'] = view('contents.' . $root . '' . request('prefix') . '.' . request('suffix') . '.' . 'form', compact('id', 'data', 'model'))->render();
        }
        return $response;
    }

    public function getEmptySelect(Request $request)
    {
        $data['data'] = [];
        $data['pagination'] = ['more' => false];
        return $data;
    }

    public function getCode(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = ProductionCode::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
                ->orwhereRaw("LOWER(alias) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'alias']);
    }

    public function getRole(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Role::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getGudang(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idGudangSelected = strtolower($request['idGudangSelected']) ?? '';
        $model = $request['model'] ?? '';
        $constructor = Gudang::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when($idGudangSelected, function ($query, $value) {
            return $query->where('id', $value);
        })->when($model, function ($query, $value) {
            return $query->has("relLogStokPenerimaan");
        })
            ->orderBy('id', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'name']);
    }

    public function getSupplier(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Supplier::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getMesin(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $jenis = $request['jenis'] ?? '';
        $idBeam = $request['idBeam'] ?? '';
        $constructor = Mesin::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when($jenis, function ($query, $value) {
            return $query->where('jenis', $value);
        })->when($idBeam, function ($query, $idBeam) {
            return $query->whereNotIn('id', function ($query) use ($idBeam) {
                return $query->select('id_mesin')->where('id_beam', $idBeam)->whereNull('deleted_at')->from('tbl_mesin_history');
            });
        })->orderBy('id', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getBarang(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idSelected = (isset($request['idSelected'])) ? explode(',', $request['idSelected']) : [];
        $filterTipe = (isset($request['filterTipe'])) ? explode(',', $request['filterTipe']) : [];
        $idPenerimaan = $request['idPenerimaan'] ?? '';
        $idPenerimaanChemical = $request['idPenerimaanChemical'] ?? '';
        $idResep = $request['idResep'] ?? '';
        $constructor = Barang::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when(!empty($idSelected), function ($query, $value) use ($idSelected) {
            return $query->whereIn('id', $idSelected);
        })->when(!empty($filterTipe), function ($query, $value) use ($filterTipe) {
            return $query->whereIn('id_tipe', $filterTipe);
        })->when($idPenerimaan, function ($query, $value) {
            return $query->whereDoesntHave('relPenerimaanBarangDetail', function ($query) use ($value) {
                return $query->where('id_penerimaan_barang', $value);
            });
        })->when($idPenerimaanChemical, function ($query, $value) {
            return $query->whereDoesntHave('relPenerimaanChemicalDetail', function ($query) use ($value) {
                return $query->where('id_penerimaan_chemical', $value);
            });
        })->when($idResep, function ($query, $value) {
            return $query->whereDoesntHave('relResepDetail', function ($query) use ($value) {
                return $query->where('id_resep', $value);
            });
        })
            ->orderBy('id_tipe', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'name'], ['id_tipe']);
    }

    public function getDoubling(Request $request)
    {
        $param      = strtolower($request['param']) ?? '';
        $idDoubling = $request['idDoubling'] ?? '';

        $constructor = DoublingDetail::when($param, function ($query, $param) {
            return $query->whereHas('relBarang', function ($query) use ($param) {
                return $query->whereRaw("LOWER(name) LIKE '%$param%'");
            });
        })->when($idDoubling, function ($query, $value) {
            return $query
                ->where('id_doubling', $value)
                ->where('status', 'KIRIM')
                ->whereNotIn('id', function ($query) {
                    return $query->select('id_parent_detail')->where('status', 'TERIMA')->whereNull('deleted_at')->from('tbl_doubling_detail');
                });
        });
        return Define::fetchSelect2($request, $constructor, ['id', 'nama_barang'], ['id_barang']);
    }

    public function getBarangDyeing(Request $request)
    {
        $param        = strtolower($request['param']) ?? '';
        $idDyeing     = $request['idDyeing'];
        $filterStatus = $request['filterDyeing'] ?? 'SOFTCONE';
        $extra        = ['id_dyeing_detail', 'id_gudang', 'id_mesin', 'id_barang', 'stok_utama', 'stok_pilihan', 'id_warna', 'nama_warna', 'id_satuan_1', 'nama_satuan_1', 'id_satuan_2', 'nama_satuan_2'];
        $select       = 'id as id_dyeing_detail, tanggal, id_gudang, id_mesin, id_barang, id_satuan_1, id_satuan_2, id_warna, volume_1 as stok_utama, volume_2 as stok_pilihan, status, key';
        $groupBy      = 'id, id_gudang, id_mesin, id_barang, id_satuan_1, id_satuan_2, id_warna';
        // $having       = 'SUM(COALESCE(tbl_dyeing_detail.volume_1, 0) - COALESCE(sq.volume_1, 0)) > 0 OR SUM(COALESCE(tbl_dyeing_detail.volume_2, 0) - COALESCE(sq.volume_2, 0)) > 0';

        // $subQuery = DyeingDetail::selectRaw('id_parent as id, SUM(COALESCE(volume_1, 0)) as volume_1, SUM(COALESCE(volume_2, 0)) as volume_2')->whereNotNull('id_parent')->groupBy('id_parent');

        $filterCurrStatus = 'SOFTCONE';
        if ($filterStatus == 'SOFTCONE') {
            $filterCurrStatus = 'DYEOVEN';
        } else if ($filterStatus == 'DYEOVEN') {
            $filterCurrStatus = 'OVERCONE';
        } else if ($filterStatus == 'OVERCONE') {
            $filterCurrStatus = 'RETURN';
        }

        $constructor = DyeingDetail::when($param, function ($query, $param) {
            return $query->where(function ($query) use ($param) {
                $query->whereHas('relBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relWarna', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'")
                        ->orwhereRaw("LOWER(alias) LIKE '%$param%'");
                });
            });
        })
            // ->leftJoinSub($subQuery, 'sq', function ($join) {
            //     return $join->on('tbl_dyeing_detail.id', '=', 'sq.id');
            // })
            ->whereNotIn('id', function ($query) use ($filterCurrStatus) {
                return $query->select('id_parent')
                    ->where('status', $filterCurrStatus)
                    ->whereNull('deleted_at')
                    ->from('tbl_dyeing_detail');
            })
            ->where('id_dyeing', $idDyeing)
            ->where('status', $filterStatus)
            ->selectRaw($select)
            // ->havingRaw($having)
            ->groupByRaw($groupBy);
        return Define::fetchSelect2($request, $constructor, ['uniqueKey', 'jenis_benang'], $extra);
    }

    public function getDyeingJasaLuar(Request $request)
    {
        $param            = strtolower($request['param']) ?? '';
        $idDyeingJasaLuar = $request['idDyeingJasaLuar'] ?? '';
        $constructor = DyeingJasaLuarDetail::when($param, function ($query, $param) {
            return $query->whereHas('relBarang', function ($query) use ($param) {
                return $query->whereRaw("LOWER(name) LIKE '%$param%'");
            });
        })->when($idDyeingJasaLuar, function ($query, $value) {
            return $query
                ->where('id_dyeing_jasa_luar', $value)
                ->where('status', 'KIRIM');
        });
        return Define::fetchSelect2($request, $constructor, ['id', 'nama_barang'], ['id_barang']);
    }

    public function getDyeingGrey(Request $request)
    {
        $param            = strtolower($request['param']) ?? '';
        $idDyeingGrey     = $request['idDyeingGrey'] ?? '';
        $constructor = DyeingGreyDetail::when($param, function ($query, $param) {
            return $query->whereHas('relBarang', function ($query) use ($param) {
                return $query->whereRaw("LOWER(name) LIKE '%$param%'");
            });
        })->when($idDyeingGrey, function ($query, $value) {
            return $query
                ->where('id_dyeing_grey', $value)
                ->where('status', 'KIRIM');
        });
        return Define::fetchSelect2($request, $constructor, ['id', 'nama_barang'], ['id_barang']);
    }

    public function getDyeingGresik(Request $request)
    {
        $param            = strtolower($request['param']) ?? '';
        $idDyeingGresik = $request['idDyeingGresik'] ?? '';

        $constructor = DyeingGresikDetail::when($param, function ($query, $param) {
            return $query->whereHas('relBarang', function ($query) use ($param) {
                return $query->whereRaw("LOWER(name) LIKE '%$param%'");
            });
        })->when($idDyeingGresik, function ($query, $value) {
            return $query
                ->where('id_dyeing_gresik', $value)
                ->where('code', 'BBGD')
                ->whereNotIn('id', function ($query) {
                    return $query->select('id_parent_detail')->where('code', 'BDG')->whereNull('deleted_at')->from('tbl_dyeing_gresik_detail');
                });
        });
        return Define::fetchSelect2($request, $constructor, ['id', 'nama_barang'], ['id_barang', 'volume_1', 'volume_2']);
    }

    public function getBarangPengiriman(Request $request)
    {
        $param        = strtolower($request['param']) ?? '';
        $idPengiriman = $request['idPengiriman'] ?? '';
        // $idGudang     = $request['idGudang'] ?? '';
        $extra        = ['id_gudang', 'id_barang', 'is_sizing', 'tipe_pra_tenun', 'volume_1', 'volume_2', 'id_warna', 'nama_warna', 'id_motif', 'nama_motif', 'id_grade', 'nama_grade', 'id_kualitas', 'nama_kualitas', 'id_beam', 'id_mesin', 'no_beam', 'nama_mesin', 'no_kikw', 'id_satuan_1', 'nama_satuan_1', 'id_satuan_2', 'nama_satuan_2'];
        $select       = 'id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, SUM(volume_1) as volume_1, SUM(volume_2) as volume_2, id_beam, id_mesin, tipe_pra_tenun';
        $groupBy      = 'id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, id_beam, id_mesin, tipe_pra_tenun';

        $strWarnaMotifBarang = [];
        if ($idPengiriman != '') {
            PengirimanBarangDetail::where('id_pengiriman_barang', $idPengiriman)
                ->where('status', 'TUJUAN')
                ->each(function ($item) use (&$strWarnaMotifBarang) {
                    $idWarna      = $item->id_warna ?? 'NULL::INTEGER';
                    $idMotif      = $item->id_motif ?? 'NULL::INTEGER';
                    $idGrade      = $item->id_grade ?? 'NULL::INTEGER';
                    $idKualitas   = $item->id_kualitas ?? 'NULL::INTEGER';
                    $idBeam       = $item->id_beam ?? 'NULL::INTEGER';
                    $idMesin      = $item->id_mesin ?? 'NULL::INTEGER';
                    $tipePraTenun = $item->tipe_pra_tenun ?? 'NULL';
                    $strWarnaMotifBarang[] = "(id_barang != {$item->id_barang} OR id_warna != {$idWarna} OR id_motif != {$idMotif} OR id_grade != {$idGrade} OR id_kualitas != {$idKualitas} OR id_beam != {$idBeam} OR id_mesin != {$idMesin} OR tipe_pra_tenun != '{$tipePraTenun}')";
                });
        }

        $strWarnaMotifBarang = implode(' AND ', $strWarnaMotifBarang);

        $constructor = PengirimanBarangDetail::when($param, function ($query, $param) {
            return $query->where(function ($query) use ($param) {
                $query->whereHas('relBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relWarna', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'")
                        ->orwhereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relMotif', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relGrade', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(grade) LIKE '%$param%'")
                        ->orwhereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relKualitas', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(kode) LIKE '%$param%'")
                        ->orwhereRaw("LOWER(name) LIKE '%$param%'");
                });
            });
        })->when($idPengiriman, function ($query, $value) use ($strWarnaMotifBarang) {
            return $query
                ->where('id_pengiriman_barang', $value)
                ->where('status', 'ASAL')
                ->when($strWarnaMotifBarang != '', function ($query) use ($strWarnaMotifBarang) {
                    return $query->whereRaw($strWarnaMotifBarang);
                });
        })
            ->selectRaw($select)
            ->groupByRaw($groupBy);
        return Define::fetchSelect2($request, $constructor, ['uniqueKey', 'nama_barang'], $extra);
    }

    public function getNotaPengiriman(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = PengirimanBarang::whereHas('relPengirimanDetail', function ($query) {
            return $query->where('status', 'TUJUAN');
        });
        return Define::fetchSelect2($request, $constructor, ['id', 'nomor']);
    }

    public function getBarangTenun(Request $request)
    {
        $param           = strtolower($request['param']) ?? '';
        $idTenun         = $request['idTenun'] ?? '';
        $filterCode      = $request['filterCode'] ?? '';
        $flag            = $request['flag'] ?? '';
        $filterIsSongket = strtolower($request['filterIsSongket']) ?? '';
        $filterBeam      = $request['filterBeam'] ?? '';
        $extra   = ['id_gudang', 'id_barang', 'volume_1', 'volume_2', 'id_warna', 'id_motif', 'id_beam', 'id_satuan_1', 'nama_satuan_1', 'id_satuan_2', 'nama_satuan_2', 'code'];
        $select  = 'id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, SUM(volume_1) as volume_1, SUM(volume_2) as volume_2, id_beam, code';
        $groupBy = 'id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_beam, code';
        $having  = '';

        $display = ['uniqueKey', 'nama_barang'];
        $strWarnaMotifBarang = [];
        $subQueryLusi = $subQuerySongket = '';
        if ($idTenun != '') {
            if ($flag == 'output') {
                $extra   = ['stok'];
                $select  = 'tbl_tenun_detail.id as id, tbl_tenun_detail.id_barang, tbl_tenun_detail.id_satuan_1, tbl_tenun_detail.id_satuan_2, tbl_tenun_detail.id_warna, tbl_tenun_detail.id_motif, SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sq.volume_1, 0)) as stok, tbl_tenun_detail.id_beam, tbl_tenun_detail.code, tbl_tenun_detail.id_songket_detail';
                $groupBy = 'tbl_tenun_detail.id, tbl_tenun_detail.id_barang, tbl_tenun_detail.id_satuan_1, tbl_tenun_detail.id_satuan_2, tbl_tenun_detail.id_warna, tbl_tenun_detail.id_motif, tbl_tenun_detail.id_beam, tbl_tenun_detail.code';
                $having  = 'SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sq.volume_1, 0)) > 0';
                $subQuerySongket = DB::table('tbl_tenun_detail')->selectRaw('id_songket_detail as id, SUM(COALESCE(volume_1, 0)) as volume_1, SUM(COALESCE(volume_2, 0)) as volume_2')
                    ->whereNotNull('id_songket_detail')
                    ->whereNull('deleted_at')
                    ->groupBy('id_songket_detail');
                $display = ['id', 'nama_barang'];
            } else if ($flag == 'turun') {
                if ($filterCode == 'BBTLT' || $filterCode == 'BBTST') {
                    $having  = 'CASE 
                        WHEN code = \'BBTL\' THEN SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sql.volume_1, 0))
                        WHEN code = \'BBTS\' THEN SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sqs.volume_1, 0)) 
                    ELSE
                        SUM(tbl_tenun_detail.volume_2)
                    END > 0';
                    $subQueryLusi = DB::table('tbl_tenun_detail')->selectRaw('id_lusi_detail as id, SUM(COALESCE(volume_1, 0)) as volume_1')
                        ->whereNotNull('id_lusi_detail')
                        ->whereNull('deleted_at')
                        ->groupBy('id_lusi_detail');
                    $subQuerySongket = DB::table('tbl_tenun_detail')->selectRaw('id_songket_detail as id, SUM(COALESCE(volume_1, 0)) as volume_1')
                        ->whereNotNull('id_songket_detail')
                        ->whereNull('deleted_at')
                        ->groupBy('id_songket_detail');
                    $select  = 'id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, SUM(tbl_tenun_detail.volume_1) as volume_1, 
                    CASE 
                        WHEN code = \'BBTL\' THEN SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sql.volume_1, 0))
                        WHEN code = \'BBTS\' THEN SUM(COALESCE(tbl_tenun_detail.volume_2, 0) - COALESCE(sqs.volume_1, 0)) 
                    ELSE
                        SUM(tbl_tenun_detail.volume_2)
                    END as volume_2, id_beam, code';
                } else {
                    TenunDetail::where('id_tenun', $idTenun)
                        ->where('code', $filterCode)
                        ->each(function ($item) use (&$strWarnaMotifBarang) {
                            $idWarna      = $item->id_warna ?? 'NULL::INTEGER';
                            $idMotif      = $item->id_motif ?? 'NULL::INTEGER';
                            $idBeam       = $item->id_beam ?? 'NULL::INTEGER';
                            $strWarnaMotifBarang[] = "(id_barang != {$item->id_barang} OR id_warna != {$idWarna} OR id_motif != {$idMotif} OR id_beam != {$idBeam})";
                        });
                }

                $filterCode = substr($filterCode, 0, -1);
            }
        }

        $strWarnaMotifBarang = implode(' AND ', $strWarnaMotifBarang);

        $constructor = TenunDetail::whereHas('relBarang', function ($query) use ($param) {
            return $query->when($param, function ($query, $value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->when($filterCode, function ($query, $value) {
                return $query->where('code', $value);
            })
            ->when($idTenun, function ($query, $idTenun) use ($filterBeam, $filterIsSongket, $flag, $subQueryLusi, $subQuerySongket, $filterCode, $strWarnaMotifBarang) {
                return $query->where('id_tenun', $idTenun)
                    ->when($flag == 'output', function ($query) use ($subQuerySongket) {
                        $query
                            ->doesntHave('relLusiTurun')
                            ->doesntHave('relSongketTurun')
                            ->leftJoinSub($subQuerySongket, 'sq', function ($join) {
                                return $join->on('tbl_tenun_detail.id', 'sq.id');
                            });
                    })->when($flag == 'turun', function ($query) use ($idTenun, $filterBeam, $filterIsSongket, $subQueryLusi, $subQuerySongket, $filterCode, $strWarnaMotifBarang) {
                        return $query
                            ->when($filterCode != 'BBTS' && $filterCode != 'BBTL', function ($query) use ($strWarnaMotifBarang) {
                                return $query->when($strWarnaMotifBarang != '', function ($query) use ($strWarnaMotifBarang) {
                                    return $query->whereRaw($strWarnaMotifBarang);
                                });
                            }, function ($query) use ($subQueryLusi, $subQuerySongket, $filterBeam, $filterCode) {
                                $query->leftJoinSub($subQueryLusi, 'sql', function ($join) {
                                    return $join->on('tbl_tenun_detail.id', 'sql.id');
                                })->leftJoinSub($subQuerySongket, 'sqs', function ($join) {
                                    return $join->on('tbl_tenun_detail.id', 'sqs.id');
                                })->when($filterBeam, function ($query, $value) {
                                    return $query->where('id_beam', $value);
                                })->when($filterCode == 'BBTL', function ($query) {
                                    return $query->doesntHave('relLusiTurun');
                                })->when($filterCode == 'BBTS', function ($query) {
                                    // return $query->doesntHave('relSongketTurun');
                                    return $query->whereNotIn('id_beam', function ($query) {
                                        return $query->select('id_beam')->where('code', 'BBTST')->whereNull('deleted_at')->from('tbl_tenun_detail');
                                    });
                                });
                            });
                    });
            })
            ->selectRaw($select)
            ->groupByRaw($groupBy)
            ->when($having, function ($query, $having) {
                $query->havingRaw($having);
            });
        return Define::fetchSelect2($request, $constructor, $display, $extra);
    }


    public function getBarangWithStok(Request $request)
    {
        $param             = str_replace('\'', '', strtolower($request['param'])) ?? '';
        $idBarangSelected  = (isset($request['idBarangSelected'])) ? explode(',', $request['idBarangSelected']) : [];
        $idGudang          = $request['idGudang'] ?? '';
        $filterTipe        = $request['filter'] ?? '';
        $filterIsBeam      = $request['filterIsBeam'] ?? '';
        $filterIdMesin     = strtolower($request['filterIdMesin']) ?? '';
        $filterBeam        = $request['filterBeam'] ?? '';
        $filterSatuan      = $request['filterSatuan'] ?? '';
        $filterWarna       = $request['filterWarna'] ?? '';
        $filterOwner       = (isset($request['filterOwner'])) ? explode(',', $request['filterOwner']) : [];
        $isShowEmptyStok   = $request['showEmptyStok'] ?? '';
        $filterMotifKhusus = $request['filterMotifKhusus'] ?? '';
        // $filterDyeing   = $request['filterDyeing'] ?? '';
        $filterCode = (isset($request['filterCode'])) ? explode(',', $request['filterCode']) : [];

        // CASE WHEN id_satuan_1 = 2 THEN ROUND(SUM(COALESCE(volume_masuk_1, 0) - COALESCE(volume_keluar_1, 0))::numeric, 2) ELSE SUM(COALESCE(volume_masuk_1, 0) - COALESCE(volume_keluar_1, 0)) END as stok_utama, 
        // CASE WHEN id_satuan_2 = 2 THEN ROUND(SUM(COALESCE(volume_masuk_2, 0) - COALESCE(volume_keluar_2, 0))::numeric, 2) ELSE SUM(COALESCE(volume_masuk_2, 0) - COALESCE(volume_keluar_2, 0)) END as stok_pilihan';
        $extra  = ['id_gudang', 'id_barang', 'nama_barang', 'is_sizing', 'id_beam', 'id_songket', 'tanggal_potong', 'id_mesin', 'nama_mesin', 'no_beam', 'no_kikw', 'no_kiks', 'tipe_pra_tenun', 'code', 'id_warna', 'id_grade', 'nama_grade', 'id_kualitas', 'nama_kualitas', 'nama_warna', 'id_motif', 'nama_motif', 'id_satuan_1', 'nama_satuan_1', 'id_satuan_2', 'nama_satuan_2', 'stok_utama', 'stok_pilihan'];
        $select = 'id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, is_sizing, id_beam, id_songket, tanggal_potong, tipe_pra_tenun, id_mesin,
        SUM(COALESCE(volume_masuk_1, 0)::decimal - COALESCE(volume_keluar_1, 0)::decimal) as stok_utama, SUM(COALESCE(volume_masuk_2, 0)::decimal - COALESCE(volume_keluar_2, 0)::decimal) as stok_pilihan';
        $groupBy = 'id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, is_sizing, id_beam, id_songket, tanggal_potong, id_mesin, tipe_pra_tenun';
        $having  = 'CASE 
            WHEN id_satuan_2 IS NOT NULL THEN SUM(COALESCE(volume_masuk_1, 0)::decimal - COALESCE(volume_keluar_1, 0)::decimal) > 0 AND SUM(COALESCE(volume_masuk_2, 0)::decimal - COALESCE(volume_keluar_2, 0)::decimal) > 0
            ELSE SUM(COALESCE(volume_masuk_1, 0)::decimal - COALESCE(volume_keluar_1, 0)::decimal) > 0
        END';

        $checkParam = str_contains($param, '|');
        // $param = ($param == '') ? [] : array_filter(explode(' | ', $param));
        $constructor = LogStokPenerimaan::when($param && !$checkParam, function ($query) use ($param) {
            return $query->where(function ($query) use ($param) {
                $query->whereHas('relBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(REPLACE(name, $$'$$, '')) LIKE '%" . $param . "%'");
                })->orwhereHas('throughNomorKikw', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relWarna', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relMotif', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relMesin', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relGrade', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(grade) LIKE '%$param%'");
                })->orwhereHas('relKualitas', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(kode) LIKE '%$param%'");
                });
            });
        })->when($idBarangSelected, function ($query, $value) use ($idBarangSelected) {
            return $query->whereHas('relBarang', function ($query) use ($idBarangSelected) {
                return $query->whereIn('id', $idBarangSelected);
            });
        })->when($filterOwner, function ($query, $owner) {
            return $query->whereHas('relBarang', function ($query) use ($owner) {
                return $query->where('owner', $owner);
            });
        })->when($filterTipe, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->where('id_tipe', $value);
            });
        })->when($idGudang, function ($query, $value) {
            return $query->where('id_gudang', $value);
        })->when(!empty($filterCode), function ($query) use ($filterCode, $filterMotifKhusus) {
            return $query->whereIn('code', $filterCode);
        })->when($filterSatuan, function ($query, $value) {
            return $query->where('id_satuan_1', $value);
        })->when($filterWarna, function ($query, $value) {
            return $query->when($value == 'YA', function ($query) {
                return $query->whereNotNull('id_warna');
            }, function ($query) {
                return $query->whereNull('id_warna');
            });
        })->when($filterBeam, function ($query, $value) {
            return $query->where('id_beam', $value);
        })->when($filterIsBeam, function ($query, $value) {
            return $query->whereNotNull('id_motif');
        })->when($filterIdMesin, function ($query, $value) {
            return $query->where('id_mesin', $value);
        })->when($filterMotifKhusus, function ($query, $value) use ($filterCode) {
            $motifKhusus = implode(',', motifKhususAsset());
            // $code = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($filterCode), ENT_NOQUOTES));
            return $query->whereRaw('CASE WHEN code = $$BGIG$$ THEN id_motif IN (' . $motifKhusus . ') ELSE id_motif IS NOT NULL END');
        })
            ->selectRaw($select)
            ->groupByRaw($groupBy)
            ->when(!$isShowEmptyStok, function ($query) use ($having) {
                return $query->havingRaw($having);
            });
        return Define::fetchSelect2($request, $constructor, ['uniqueKey', 'nama_barang'], $extra, $checkParam);
    }

    public function getBeam(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $flag = $request['flag'] ?? '';
        $idDistribusiPakan = $request['idDistribusiPakan'] ?? '';
        $extra  = ['no_beam', 'no_kikw', 'is_sizing', 'tipe_pra_tenun', 'relMesinHistoryLatest', 'id_tenun'];
        $constructor = Beam::with(['relMesinHistoryLatest', 'relMesinHistoryLatest.relMesin'])
            ->when($param, function ($query, $param) {
                return $query->where(function ($query) use ($param) {
                    $query->whereHas('relNomorBeam', function ($query) use ($param) {
                        return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                    })->orwhereHas('relNomorKikw', function ($query) use ($param) {
                        return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                    })->orwhereHas('relMesinHistoryLatest.relMesin', function ($query) use ($param) {
                        return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                    })->orwhereraw("LOWER(tipe_beam) LIKE '%$param%'");
                });
            })
            ->when($flag, function ($query, $flag) use ($idDistribusiPakan) {
                return $query->when($flag == 'cucuk', function ($query) {
                    return $query
                        ->where(function ($query) {
                            $query->whereHas('relLogStokPenerimaanBL', function ($query) {
                                return $query->whereNotNull('id_warna')->whereNotNull('id_motif');
                            });
                        })
                        ->where('finish', 0)
                        ->whereNull('tipe_pra_tenun')
                        ->whereNotNull('id_nomor_kikw')
                        ->has('relMesinHistory')
                        ->whereNotIn('id', function ($query) {
                            return $query->select('id_beam')->whereNull('deleted_at')->from('tbl_cucuk');
                        });
                })
                    ->when($flag == 'tyeing', function ($query) {
                        return $query
                            ->where(function ($query) {
                                $query->whereHas('relLogStokPenerimaanBL', function ($query) {
                                    return $query->whereNotNull('id_warna')->whereNotNull('id_motif');
                                });
                            })
                            ->where('finish', 0)
                            ->whereNull('tipe_pra_tenun')
                            ->whereNotNull('id_nomor_kikw')
                            ->has('relMesinHistory')
                            ->whereNotIn('id', function ($query) {
                                return $query->select('id_beam')->whereNull('deleted_at')->from('tbl_tyeing');
                            });
                    })->when($flag == 'tenun', function ($query) {
                        return $query
                            ->where('finish', 0)
                            ->where('tipe_beam', 'LUSI')
                            ->whereHas('relLogStokPenerimaan', function ($query) {
                                return $query->whereIn('code', ['BBTL', 'BBTS', 'BBTLR']);
                            })
                            ->whereNotIn('id', function ($query) {
                                return $query->select('id_beam')->whereNull('deleted_at')->from('tbl_tenun');
                            });
                    })->when($flag == 'distribusi_pakan', function ($query) use ($idDistribusiPakan) {
                        // return $query
                        //     ->where('tipe_beam', 'LUSI')
                        //     ->whereHas('relLogStokPenerimaan', function ($query) {
                        //         return $query->whereIn('code', ['BBTL', 'BBTS']);
                        //     });
                        return $query
                            ->where('finish', 0)
                            ->has('relTenun')
                            ->when($idDistribusiPakan, function ($query, $idDistribusiPakan) {
                                return $query->whereNotIn('id', function ($query) use ($idDistribusiPakan) {
                                    return $query->select('id_beam')->where('id_distribusi_pakan', $idDistribusiPakan)->whereNull('deleted_at')->from('tbl_distribusi_pakan_detail');
                                });
                            });
                    });
            })->orderBy('tipe_beam', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'nomor'], $extra);
    }

    public function getNomorBeam(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $isFinish = $request['finish'];
        $constructor = NomorBeam::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })
            ->when($isFinish, function ($query, $value) {
                return $query->where(function ($query) use ($value) {
                    $query->whereHas('relBeam', function ($query) use ($value) {
                        return $query->where('finish', $value);
                    })->ordoesntHave('relBeam');
                });
            })
            // ->whereNotIn('id', function ($query) {
            //     return $query->select('id_nomor_beam')->where('finish', 0)->whereNull('deleted_at')->from('tbl_beam');
            // })
            ->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getNomorKikw(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = NomorKikw::when($param, function ($query, $value) {
            return $query->where('name', $value);
        })->get();
        return response($constructor, 200);
    }

    public function getBeamSongket(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idTenun = strtolower($request['idTenun']) ?? '';
        $constructor = Beam::when($param, function ($query, $param) {
            return $query->whereHas('relNomorBeam', function ($query) use ($param) {
                return $query->whereRaw("LOWER(name) LIKE '%$param%'");
            });
        })
            ->whereHas('relTenunDetail', function ($query) use ($idTenun) {
                return $query
                    ->where('code', 'BBTS')
                    ->when($idTenun, function ($query, $idTenun) {
                        return $query->where('id_tenun', $idTenun);
                    });
            })->where('finish', 0);
        return Define::fetchSelect2($request, $constructor, ['id', 'nomor']);
    }

    public function getSatuan(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idGudangTable = $request['idGudangTable'] ?? '';
        $tipeSatuan = $request['tipeSatuan'] ?? 'utama';
        $filterId = (isset($request['filterId'])) ? explode(',', $request['filterId']) : [];
        $constructor = Satuan::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })
            ->when(!empty($filterId), function ($query) use ($filterId) {
                return $query->whereIn('id', $filterId);
            })
            ->when($idGudangTable, function ($query, $value) use ($tipeSatuan) {
                return $query
                    ->when($value == 2, function ($query) use ($tipeSatuan) { //RULE WAREHOUSE DYEING
                        return $query->when($tipeSatuan == 'utama', function ($query) {
                            return $query->where('id', 1);
                        }, function ($query) {
                            return $query->where('id', 2);
                        });
                    });

                /*->when($value == 3, function ($query) use ($tipeSatuan) { //RULE WAREHOUSE WEAVING
                    return $query->when($tipeSatuan == 'utama', function ($query) {
                        return $query->whereIn('id', [1, 3, 4, 5]);
                    }, function ($query) {
                        return $query->whereIn('id', [2, 4]);
                    });
                });*/
            })
            ->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getResep(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idBarang = $request['idBarang'] ?? '';
        $idWarna = $request['idWarna'] ?? '';
        $constructor = Resep::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when($idBarang, function ($query, $value) {
            return $query->where('id_barang', $value);
        })->when($idWarna, function ($query, $value) {
            return $query->where('id_warna', $value);
        })->has('relResepDetail')->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'name'], ['resep_detail']);
    }

    public function getWarna(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $filterJenis = $request['filterJenis'] ?? '';
        $constructor = Warna::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when($filterJenis, function ($query, $value) {
            return $query->where('jenis', $value);
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getTipe(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Tipe::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }


    public function getMotif(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Motif::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(alias) LIKE '%$value%'");
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'alias']);
    }

    public function getGrade(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Kualitas::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(grade) LIKE '%$value%'");
        })->orderBy('grade', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'grade']);
    }

    public function getKualitas(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idGrade = $request['idGrade'] ?? '9999';
        $constructor = MappingKualitas::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when($idGrade, function ($query, $value) {
            return $query->where('id_kualitas', $value);
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'nama_kualitas']);
    }

    public function getGroup(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $constructor = Group::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor);
    }

    public function getPekerja(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $idParent = strtolower($request['idParent']) ?? '';
        $idGroup = strtolower($request['idGroup']) ?? '';
        $flag = strtolower($request['flag']) ?? '';
        $extra = ['id_group', 'nama_group'];
        $constructor = Pekerja::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
                ->orwhereRaw("LOWER(no_hp) LIKE '%$value%'");
        })
            ->when($flag == 'mesin', function ($query) use ($idParent) {
                return $query
                    ->when($idParent, function ($query, $idParent) {
                        return $query->whereNotIn('id_group', function ($query) use ($idParent) {
                            return $query->select('tbl_pekerja.id_group')
                                ->where('tbl_mapping_pekerja_mesin.id_mesin', $idParent)
                                ->whereNull('tbl_mapping_pekerja_mesin.deleted_at')
                                ->from('tbl_mapping_pekerja_mesin')
                                ->join('tbl_pekerja', 'tbl_pekerja.id', 'tbl_mapping_pekerja_mesin.id_pekerja');
                        });
                    });
            })
            ->when($flag == 'group', function ($query) use ($idParent) {
                return $query->whereNotIn('id', function ($query) {
                    return $query->select('id_pekerja')->whereNull('deleted_at')->from('tbl_group_detail');
                });
            })
            ->when($flag == 'cucuk', function ($query) use ($idParent) {
                return $query
                    ->when($idParent, function ($query, $idParent) {
                        return $query->whereNotIn('id', function ($query) use ($idParent) {
                            return $query->select('id_pekerja')->where('id_cucuk', $idParent)->whereNull('deleted_at')->from('tbl_cucuk_detail');
                        });
                    });
            })->when($flag == 'tyeing', function ($query) use ($idParent) {
                return $query
                    ->when($idParent, function ($query, $idParent) {
                        return $query->whereNotIn('id', function ($query) use ($idParent) {
                            return $query->select('id_pekerja')->where('id_tyeing', $idParent)->whereNull('deleted_at')->from('tbl_tyeing_detail');
                        });
                    });
            })->when($flag == 'tenun', function ($query) use ($idParent, $idGroup) {
                return $query
                    ->when($idGroup, function ($query, $value) {
                        return $query->whereHas('relGroupDetail', function ($query) use ($value) {
                            return $query->where('id_group', $value);
                        });
                    })
                    ->when($idParent, function ($query, $idParent) {
                        return $query->whereNotIn('id', function ($query) use ($idParent) {
                            return $query->select('id_pekerja')->where('id_tenun', $idParent)->whereNull('deleted_at')->from('tbl_tenun_detail');
                        });
                    });
            })
            ->orderBy('name', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'name'], $extra);
    }

    public function getTipePengiriman(Request $request)
    {
        $param = strtolower($request['param']) ?? '';
        $isAdminOrValidator = Auth::user()->is('Administrator') || Auth::user()->is('Validator');
        $constructor = TipePengiriman::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->when(!$isAdminOrValidator, function ($query) {
            return $query->where('roles_id', Auth::user()->roles_id);
        })->where('is_aktif', 'YA');
        return Define::fetchSelect2($request, $constructor);
    }

    public function MappingMenuForm($id)
    {
        try {
            $dataMenu = MappingMenu::where('roles_id', $id)->pluck('menus_id')->toArray();
            $templateMenu = getMenus($dataMenu);
            return view('contents.management.roles._form-menu', compact('templateMenu', 'id'));
        } catch (\Throwable $th) {
            print_r($th->getMessage() . ' ');
            return response('Form is Not Successfully Rendered!', 400);
        }
    }

    public function checkedMappingMenu(Request $request)
    {
        $isChecked = $request['is_checked'] == 'true';
        $input['roles_id'] = $request['id_group'];
        $input['menus_id'] = $request['value'];

        ($isChecked) ? MappingMenu::create($input) : MappingMenu::where($input)->delete();

        Menu::where('parent_id', $request['value'])->each(function ($item) use ($request, $isChecked) {
            $input['roles_id'] = $request['id_group'];
            $input['menus_id'] = $item['id'];
            ($isChecked) ? MappingMenu::create($input) : MappingMenu::where($input)->delete();
        });
        return response('Data Successfully Updated!', 200);
    }

    public function storeResepWarna(Request $request)
    {
        $input = $request->all()['input'];
        $model = getModel($request['model']);
        DB::beginTransaction();
        try {
            $dyeingWarna = [];
            $userId = Auth::id();
            ResepDetail::where('id_resep', $request['id_resep'])->each(function ($item, $key) use ($input, &$dyeingWarna, $userId) {
                $volume = ($item['id_satuan'] == 5) ? convertGramToKg($item['volume']) : $item['volume'];
                $checkStokPewarna = checkStokBarang(['id_gudang' => 2, 'id_barang' => $item['id_barang'], 'id_satuan_1' => 2, 'code' => 'DW']);
                if ($checkStokPewarna <= 0 && $checkStokPewarna < $volume) throw new Exception("Stok {$item['barang']} tidak cukup!", 1);

                $logStokPenerimaanDyeing['id_gudang']        = 2;
                $logStokPenerimaanDyeing['code']             = 'DW';
                $logStokPenerimaanDyeing['tanggal']          = $input['tanggal'];
                $logStokPenerimaanDyeing['id_barang']        = $item['id_barang'];
                $logStokPenerimaanDyeing['id_satuan_1']      = 2;
                $logStokPenerimaanDyeing['volume_keluar_1']  = $volume;

                $dyeingWarna[$key]['id_log_stok']      = LogStokPenerimaan::create($logStokPenerimaanDyeing)->id;
                $dyeingWarna[$key]['tanggal']          = $input['tanggal'];
                $dyeingWarna[$key]['id_barang']        = $item['id_barang'];
                $dyeingWarna[$key]['id_satuan']        = $item['id_satuan'];
                $dyeingWarna[$key]['id_warna']         = $input['id_warna'];
                $dyeingWarna[$key]['volume']           = $item['volume'];
                $dyeingWarna[$key]['created_by']       = $userId;
                $dyeingWarna[$key]['created_at']       = now();
                isset($input['id_dyeing_detail']) ? $dyeingWarna[$key]['id_dyeing_detail'] = $input['id_dyeing_detail'] : $dyeingWarna[$key]['id_dyeing_gresik_detail'] = $input['id_dyeing_gresik_detail'];
            });
            $model::insert($dyeingWarna);
            activity()->log("Menambah Resep Pewarna di Dyeing");
            DB::commit();
            $response['id'] = isset($input['id_dyeing_detail']) ? $input['id_dyeing_detail'] : $input['id_dyeing_gresik_detail'];
            $response['message'] = 'Bahan Warna Berhasil Ditambahkan!';
            return response($response, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function storeResepWarnaOD(Request $request)
    {
        $input = $request->all()['input'];
        DB::beginTransaction();
        try {
            $operasionalDyeing = [];
            $userId = Auth::id();
            ResepDetail::where('id_resep', $request['id_resep'])->each(function ($item, $key) use ($input, &$operasionalDyeing, $userId) {
                $volume = ($item['id_satuan'] == 5) ? convertGramToKg($item['volume']) : $item['volume'];
                $checkStokPewarna = checkStokBarang(['id_gudang' => 2, 'id_barang' => $item['id_barang'], 'id_satuan_1' => 2, 'code' => 'DW']);
                if ($checkStokPewarna <= 0 && $checkStokPewarna < $volume) throw new Exception("Stok {$item['barang']} tidak cukup!", 1);

                $logStokPenerimaanDyeing['id_gudang']        = 2;
                $logStokPenerimaanDyeing['code']             = 'DW';
                $logStokPenerimaanDyeing['tanggal']          = $input['tanggal'];
                $logStokPenerimaanDyeing['id_barang']        = $item['id_barang'];
                $logStokPenerimaanDyeing['id_satuan_1']      = 2;
                $logStokPenerimaanDyeing['volume_keluar_1']  = $volume;

                $operasionalDyeing[$key]['id_log_stok']      = LogStokPenerimaan::create($logStokPenerimaanDyeing)->id;
                $operasionalDyeing[$key]['id_operasional_dyeing'] = $input['id_operasional_dyeing'];
                $operasionalDyeing[$key]['tanggal']          = $input['tanggal'];
                $operasionalDyeing[$key]['id_gudang']        = 2;
                $operasionalDyeing[$key]['id_barang']        = $item['id_barang'];
                $operasionalDyeing[$key]['id_satuan']        = $item['id_satuan'];
                $operasionalDyeing[$key]['volume']           = $item['volume'];
                $operasionalDyeing[$key]['created_by']       = $userId;
            });
            OperasionalDyeingDetail::insert($operasionalDyeing);
            activity()->log("Menambah Resep Pewarna di Operasional Dyeing");
            DB::commit();
            return response('Chemical Oprasional Dyeing Berhasil Ditambahkan!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function storeWarna(Request $request, $id = "")
    {
        $input = $request->all()['input'];
        $model = getModel($request['model']);
        DB::beginTransaction();
        try {
            $volume = ($input['id_satuan'] == 5) ? convertGramToKg($input['volume']) : $input['volume'];
            $checkStokPewarna = checkStokBarang(['id_gudang' => 2, 'id_barang' => $input['id_barang'], 'id_satuan_1' => 2, 'code' => 'DW']);
            if ($checkStokPewarna <= 0 && $checkStokPewarna < $volume) throw new Exception("Stok tidak cukup!", 1);

            $logStokPenerimaanDyeing['id_gudang']        = 2;
            $logStokPenerimaanDyeing['code']             = 'DW';
            $logStokPenerimaanDyeing['tanggal']          = $input['tanggal'];
            $logStokPenerimaanDyeing['id_barang']        = $input['id_barang'];
            $logStokPenerimaanDyeing['id_satuan_1']      = 2;
            $logStokPenerimaanDyeing['volume_keluar_1']  = $volume;
            if ($id == '') {
                $input['id_log_stok'] = LogStokPenerimaan::create($logStokPenerimaanDyeing)->id;
                // $checkWarna = DyeingWarna::where('id_barang', $input['id_barang'])->where('id_dyeing_detail', $input['id_dyeing_detail'])->count();
                // if ($checkWarna > 1) throw new Exception("Simpan Gagal, Warna Sudah Ditambahkan", 1);
                $model::create($input);
            } else {
                $input['updated_by'] = Auth::id();
                LogStokPenerimaan::where('id', $request['id_log_stok'])->update($logStokPenerimaanDyeing);
                $model::where('id', $id)->update($input);
            }
            activity()->log("Menambah Pewarna di Dyeing");
            DB::commit();
            $response['id'] = isset($input['id_dyeing_detail']) ? $input['id_dyeing_detail'] : $input['id_dyeing_gresik_detail'];
            $response['message'] = 'Bahan Warna Berhasil Ditambahkan!';
            return response($response, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function deleteWarna($id, Request $request)
    {
        $model = getModel($request['model']);
        LogStokPenerimaan::where('id', $request['id_log_stok'])->delete();
        $model::where('id', $id)->delete();
        activity()->log("Menghapus Pewarna di Dyeing");
        $response['id'] = isset($request['id_dyeing_detail']) ? $request['id_dyeing_detail'] : $request['id_dyeing_gresik_detail'];
        $response['message'] = 'Data Berhasil Dihapus!';
        return response($response, 200);
    }

    public function deleteAll(Request $request)
    {
        $id = $request['id'];
        $model = request('model');
        DB::beginTransaction();
        try {
            if ($model == 'PenerimaanBarang') {
                $dataDetail = PenerimaanBarangDetail::where('id_penerimaan_barang', $id);
                $dataDetail->each(function ($item) {
                    getModel(getModelWarehouse($item->id_gudang))::where('id', $item->id_log_stok_penerimaan)->delete();
                });
                $dataDetail->delete();
            } else if ($model == 'PenerimaanBarangDyeing') {
                $idLogStokPenerimaan = PenerimaanBarangDetailDyeing::where('id_penerimaan_barang_dyeing', $id)->pluck('id_log_stok_penerimaan');
                $idLogStokPenerimaanDyeing = PenerimaanBarangDetailDyeing::where('id_penerimaan_barang_dyeing', $id)->pluck('id_log_stok_penerimaan_dyeing');
                LogStokPenerimaan::whereIn('id', $idLogStokPenerimaan)->delete();
                LogStokPenerimaanDyeing::whereIn('id', $idLogStokPenerimaanDyeing)->delete();
            } else if ($model == 'Dyeing') {
                $idLogStokPenerimaanDyeingMasuk = DyeingDetail::where('id_dyeing', $id)->pluck('id_penerimaan_barang_dyeing_masuk');
                $idLogStokPenerimaanDyeingKeluar = DyeingDetail::where('id_dyeing', $id)->pluck('id_penerimaan_barang_dyeing_keluar');
                LogStokPenerimaanDyeing::whereIn('id', $idLogStokPenerimaanDyeingMasuk)->delete();
                LogStokPenerimaanDyeing::whereIn('id', $idLogStokPenerimaanDyeingKeluar)->delete();
            }
            getModel($model)::where('id', $id)->delete();
            DB::commit();
            return response('Data is Successfully Deleted!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function checkedBeam(Request $request)
    {
        if ($request['tipeBeam'] == 'lusi') {
            $idBeam = TenunDetail::where('id_tenun', $request['idTenun'])->where('code', 'BBTS')->pluck('id_beam');
            $idBeam[] = $request['idBeam'];
            if ($request['rollback'] == 'false') {
                Beam::whereIn('id', $idBeam)->update(['finish' => 1]);
            } else {
                Beam::whereIn('id', $idBeam)->update(['finish' => 0]);
            }
        } else {
            $isRollback = $request['rollback'] == 'true' ? 0 : 1;
            Beam::whereIn('id', $request['idBeam'])->update(['finish' => $isRollback]);
        }
        activity()->log("Finishing Beam");
        $response['id'] = $request['idTenun'];
        $response['message'] = 'Data is Successfully Updated!';
        return response($response, 200);
    }

    public function validateForm(Request $request)
    {
        $isRollback = $request['state'] == 'rollback';
        $validateDate = $isRollback ? null : now();
        getModel($request['model'])::where('id', $request['id'])->update(['validated_at' => $validateDate]);
        if ($request['model'] == 'Tenun') {
            TenunDetail::where('id_tenun', $request['id'])->where('code', '!=', 'BG')->update(['validated_at' => $validateDate]);
        }
        activity()->log("Validasi Form {$request['model']}");
        $message = $isRollback ? 'Data is Successfully Invalidated!' : 'Data is Successfully Validated!';
        return response(['id' => $request['id'], 'message' => $message], 200);
    }

    public function applyMesin(Request $request)
    {
        $input = $request->all()['input'];
        MesinHistory::create($input);
        activity()->log("Mengganti Mesin Tenun");
        return response('Mesin berhasil di ganti!', 200);
    }

    public function getPekerjaMesin($idMesin)
    {
        $data = [];
        MappingPekerjaMesin::where('id_mesin', $idMesin)->each(function ($item, $key) use (&$data) {
            $data['select_pekerja'][$key] = [
                'id' => $item['id_pekerja'],
                'text' => $item['relPekerja']['name']
            ];
        });
        return response($data, 200);
    }

    public function storePekerjaMesin(Request $request)
    {
        $data = [];
        $input = $request->all()['input'];
        MappingPekerjaMesin::where('id_mesin', $input['id_mesin'])->delete();
        foreach ($input['id_pekerja'] as $key => $value) {
            // $checkPekerja = MappingPekerjaMesin::where('id_pekerja', $value)->count();
            // if ($checkPekerja > 0) return response('Pekerja telah di daftarkan di mesin lain!', 401);
            $data[$key]['id_pekerja'] = $value;
            $data[$key]['id_mesin'] = $input['id_mesin'];
            $data[$key]['created_at'] = now();
            $data[$key]['created_by'] = Auth::id();
        }
        MappingPekerjaMesin::insert($data);
        return response('Data Successfully Saved!', 200);
    }

    public function storeLembur(Request $request)
    {
        DB::beginTransaction();
        try {
            $userID = Auth::id();
            $input = $request->all()['input'];
            $data = [];
            foreach ($input['id_mesin'] as $key => $idMesin) {
                $data[$key] = [
                    'tanggal'    => $input['tanggal'],
                    'id_pekerja' => $input['id_pekerja'],
                    'id_group'   => $input['id_group'],
                    'id_mesin'   => $idMesin,
                    'shift'      => $input['shift'],
                    'lembur'     => 'YA',
                    'created_at' => now(),
                    'created_by' => $userID
                ];
            }

            AbsensiPekerja::insert($data);
            DB::commit();
            return response('Data Successfully Saved!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function acceptFormView(Request $request, $id)
    {
        $data = PengirimanBarangDetail::where('id', $id)->first();
        $idTipe = $data->relPengirimanBarang()->value('id_tipe_pengiriman');
        $response['selected'] = [
            'select_gudang' => [
                'id' => $data->status == 'TUJUAN' ? $data->id_gudang : $data->relPengirimanBarang()->value('id_gudang_tujuan'),
                'text' => $data->status == 'TUJUAN' ? $data->relGudang()->value('name') : $data->throughGudangTujuan()->value('name')
            ],
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
            ],
            'select_satuan_1' => [
                'id'   => $data->id_satuan_1,
                'text' => $data->relSatuan1()->value('name')
            ]
        ];

        if ($data->id_satuan_2 != null) {
            $arraySatuan2 = [
                'select_satuan_2' => [
                    'id'   => $data->id_satuan_2,
                    'text' => $data->relSatuan2()->value('name')
                ]
            ];
            $response['selected'] = array_merge($response['selected'], $arraySatuan2);
        }

        $idParent = $data->id_pengiriman_barang;
        $currentCode = generateCodePengiriman($idTipe, 'input', '');
        $code = generateCodePengiriman($idTipe, 'output', $currentCode);
        $response['render'] = view('contents.production.pengiriman_barang.form-accept', compact('id', 'data', 'idParent', 'code', 'idTipe'))->render();
        return $response;
    }

    public function accept(Request $request)
    {
        DB::beginTransaction();
        try {
            $idUser = Auth::id();
            $input = $request->all()['input'];

            if ($request['id_tipe'] == 13 || $request['id_tipe'] == 14) $input = unsetMultiKeys(['id_beam', 'tipe_pra_tenun', 'is_sizing', 'id_mesin'], $input);

            $input['volume_1'] = floatValue($input['volume_1']);
            if (isset($input['id_satuan_2']) && isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
            $input['updated_by'] = $idUser;
            $acceptDate = now();
            PengirimanBarangDetail::where('id', $input['id_parent_detail'])->update(['accepted_at' => $acceptDate]);

            $inputLogStok = unsetMultiKeys(['updated_by', 'volume_1', 'volume_2', 'id_pengiriman_barang', 'id_parent_detail', 'catatan'], $input);

            if (isset($input['id_beam'])) {
                $inputLogStok['id_beam']  = $input['id_beam'];
                if ($request['id_tipe'] != 7 && $request['id_tipe'] != 8) {
                    $dataBeam = Beam::where('id', $input['id_beam'])->first();
                    $inputLogStok['tipe_pra_tenun'] = $input['tipe_pra_tenun'] ?? null;
                    $inputLogStok['is_sizing']      = $dataBeam->is_sizing;
                }
            }
            if (isset($input['id_songket'])) {
                $inputLogStok['id_songket']  = $input['id_songket'];
            }
            if (isset($input['tanggal_potong'])) {
                $inputLogStok['tanggal_potong']  = $input['tanggal_potong'];
            }

            $inputLogStok['volume_masuk_1'] = $input['volume_1'];
            if (isset($input['id_satuan_2']) && isset($input['volume_2'])) $inputLogStok['volume_masuk_2'] = $input['volume_2'];

            if ($request['id_tipe'] == 2) $inputLogStok['id_mesin'] = null; //HASIL DYEING TIDAK ADA MESIN
            if ($request['id_tipe'] == 19) { //RETUR HASIL DYEING TIDAK ADA WARNA
                $inputLogStok['id_warna']       = null;
                $inputLogStok['id_satuan_1']    = 2;
                $inputLogStok['volume_masuk_1'] = $input['volume_2'];
                $inputLogStok['id_satuan_2']    = null;
                $inputLogStok['volume_masuk_2'] = null;
            }

            $idLogStok = LogStokPenerimaan::create($inputLogStok)->id;

            $inputDetail = unsetMultiKeys(['updated_by', 'code'], $input);

            if ($request['id_tipe'] == 2) $inputDetail['id_mesin'] = null; //HASIL DYEING TIDAK ADA MESIN
            if ($request['id_tipe'] == 19) { //RETUR HASIL DYEING TIDAK ADA WARNA
                $inputDetail['id_warna']    = null;
                $inputDetail['id_satuan_1'] = 2;
                $inputDetail['volume_1']    = $input['volume_2'];
                $inputDetail['id_satuan_2'] = null;
                $inputDetail['volume_2']    = null;
            }

            $inputDetail['id_log_stok'] = $idLogStok;
            $inputDetail['status'] = 'TUJUAN';
            $inputDetail['created_by'] = $idUser;
            $inputDetail['accepted_at'] = $acceptDate;
            // dd($inputDetail);
            PengirimanBarangDetail::create($inputDetail);
            DB::commit();
            return response('Data Successfully Accepted!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function acceptAll(Request $request)
    {
        DB::beginTransaction();
        try {
            $idUser = Auth::id();
            $input = [];
            $acceptDate = now();
            $data = PengirimanBarangDetail::whereIn('id', $request['id'])->get();
            foreach ($data as $key => $value) {

                $idTipe = $value->relPengirimanBarang()->value('id_tipe_pengiriman');

                $inputLogStok['tanggal']        = $value->tanggal;
                $inputLogStok['id_gudang']      = $value->relPengirimanBarang()->value('id_gudang_tujuan');
                $inputLogStok['id_barang']      = $value->id_barang;
                $inputLogStok['id_warna']       = $value->id_warna;
                $inputLogStok['id_motif']       = $value->id_motif;
                $inputLogStok['id_grade']       = $value->id_grade;
                $inputLogStok['id_kualitas']    = $value->id_kualitas;
                $inputLogStok['id_mesin']       = $value->id_mesin;
                $inputLogStok['id_satuan_1']    = $value->id_satuan_1;
                $inputLogStok['volume_masuk_1'] = $value->volume_1;
                $inputLogStok['id_satuan_2']    = $value->id_satuan_2;
                $inputLogStok['volume_masuk_2'] = $value->volume_2;
                $inputLogStok['code']           = generateCodePengiriman($idTipe, 'output', '');
                $inputLogStok['id_songket']     = $value->id_songket;
                $inputLogStok['tanggal_potong']     = $value->tanggal_potong;

                if ($value->id_beam != null) {
                    $inputLogStok['id_beam']  = $value->id_beam;
                    if ($idTipe != 7 && $idTipe != 8) {
                        $dataBeam = Beam::where('id', $value->id_beam)->first();
                        $inputLogStok['tipe_pra_tenun'] = $dataBeam->tipe_pra_tenun;
                        $inputLogStok['is_sizing']      = $dataBeam->is_sizing;
                    }
                }

                if ($idTipe == 2) $inputLogStok['id_mesin'] = null;
                if ($idTipe == 19) { //RETUR HASIL DYEING TIDAK ADA WARNA
                    $inputLogStok['id_warna']       = null;
                    $inputLogStok['id_satuan_1']    = 2;
                    $inputLogStok['volume_masuk_1'] = $value->volume_2;
                    $inputLogStok['id_satuan_2']    = null;
                    $inputLogStok['volume_masuk_2'] = null;
                }

                if ($idTipe == 13 || $idTipe == 14) $inputLogStok = unsetMultiKeys(['id_beam', 'tipe_pra_tenun', 'is_sizing', 'id_mesin'], $inputLogStok);

                $input[$key] = unsetMultiKeys(['volume_masuk_1', 'volume_masuk_2', 'code', 'is_sizing'], $inputLogStok);

                $input[$key]['volume_1'] = floatValue($value->volume_1);
                if ($value->id_satuan_2 != null) $input[$key]['volume_2'] = floatValue($value->volume_2);

                if ($idTipe == 19) { //RETUR HASIL DYEING TIDAK ADA WARNA
                    $input[$key]['volume_1'] = $value->volume_2;
                    $input[$key]['volume_2'] = null;
                } else {
                    $input[$key]['volume_2'] = $value->volume_2;
                }

                $input[$key]['id_log_stok'] = LogStokPenerimaan::create($inputLogStok)->id;
                $input[$key]['id_pengiriman_barang'] = $value->id_pengiriman_barang;
                $input[$key]['id_parent_detail'] = $value->id;
                $input[$key]['status'] = 'TUJUAN';
                $input[$key]['created_by'] = $idUser;
                $input[$key]['accepted_at'] = $acceptDate;
            }
            PengirimanBarangDetail::whereIn('id', $request['id'])->update(['accepted_at' => $acceptDate, 'updated_by' => $idUser]);
            PengirimanBarangDetail::insert($input);
            DB::commit();
            return response('Data Successfully Accepted!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function reject(Request $request)
    {
        DB::beginTransaction();
        try {
            PengirimanBarangDetail::where('id', $request['idParentDetail'])->update(['accepted_at' => null]);

            $dataPengirimanDetail = PengirimanBarangDetail::where('id', $request['id']);

            $idPakanDetail = $dataPengirimanDetail->first()->id_pakan_detail;
            if ($idPakanDetail != null && $idPakanDetail != 0) {
                $dataPakan = PakanDetail::where('id', $idPakanDetail);
                $dataPakanDetail = PakanDetail::where('id_parent_detail', $idPakanDetail);
                $idLogStokPakan = $dataPakan->first()->id_log_stok_penerimaan;
                $idLogStokPakanDetail = $dataPakanDetail->first()->id_log_stok_penerimaan;

                $dataPakan->forceDelete();
                $dataPakanDetail->forceDelete();
                LogStokPenerimaan::where('id', $idLogStokPakan)->forceDelete();
                LogStokPenerimaan::where('id', $idLogStokPakanDetail)->forceDelete();
            }

            PengirimanBarangDetail::where('id', $request['id'])->forceDelete();
            LogStokPenerimaan::where('id', $request['idLogStok'])->forceDelete();
            DB::commit();
            return response('Data Successfully Rejected!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function sendAll(Request $request)
    {
        DB::beginTransaction();
        try {
            $idUser = Auth::id();
            $input = [];
            $code = generateCodePengiriman($request['id_tipe'], 'input');
            $data = DB::table('tbl_warping_detail')->whereNull('deleted_at')->whereNull('returned_at')->where(['code' => $code, 'tanggal' => $request['tanggal']])->get();

            foreach ($data as $key => $value) {

                $inputLogStok['tanggal']         = $request['tanggal'];
                $inputLogStok['id_gudang']       = $value->id_gudang;
                $inputLogStok['id_barang']       = $value->id_barang;
                $inputLogStok['id_warna']        = $value->id_warna;
                $inputLogStok['id_satuan_1']     = $value->id_satuan_1;
                $inputLogStok['volume_keluar_1'] = $value->volume_1;
                $inputLogStok['id_satuan_2']     = $value->id_satuan_2;
                $inputLogStok['volume_keluar_2'] = $value->volume_2;
                $inputLogStok['code']            = $code;

                $input[$key] = unsetMultiKeys(['volume_keluar_1', 'volume_keluar_2', 'code'], $inputLogStok);

                $input[$key]['volume_1'] = floatValue($value->volume_1);
                $input[$key]['volume_2'] = floatValue($value->volume_2);

                $input[$key]['id_log_stok'] = LogStokPenerimaan::create($inputLogStok)->id;
                $input[$key]['id_pengiriman_barang'] = $request['id_pengiriman_barang'];
                $input[$key]['status'] = 'ASAL';
                $input[$key]['created_at'] = now();
                $input[$key]['created_by'] = $idUser;

                WarpingDetail::where('id', $value->id)->update(['returned_at' => $request['tanggal']]);
            }

            if (!empty($input)) PengirimanBarangDetail::insert($input);

            DB::commit();
            return response('Data Successfully Send!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function cancelSendAll(Request $request)
    {
        DB::beginTransaction();
        try {
            $code = generateCodePengiriman($request['id_tipe'], 'input');
            $dataPengiriman = DB::table('tbl_pengiriman_barang_detail')->where('id', $request['id'])->first();
            WarpingDetail::where([
                'code'      => $code,
                'tanggal'   => $dataPengiriman->tanggal,
                'id_barang' => $dataPengiriman->id_barang,
                'id_warna'  => $dataPengiriman->id_warna,
                'id_gudang' => $dataPengiriman->id_gudang,
            ])->update(['returned_at' => null]);
            DB::commit();
            return response('Data Successfully Deleted!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function selectedBarangPakan($idBeam, $code)
    {
        $dataDistribusiPakan =  DistribusiPakanDetail::select('id_gudang', 'id_barang', 'id_warna', 'id_satuan_1', 'id_satuan_2')->where(['id_beam' => $idBeam, 'code' => $code])->groupBy('id_gudang', 'id_barang', 'id_warna', 'id_satuan_1', 'id_satuan_2')->get()
            ->mapWithKeys(function ($item, $key) use ($code) {
                $filter = ['id_gudang' => $item->id_gudang, 'id_barang' => $item->id_barang, 'id_warna' => $item->id_warna, 'id_satuan_1' => $item->id_satuan_1, 'id_satuan_2' => $item->id_satuan_2, 'code' => $code];
                $getStok = checkStokBarang($filter, false);
                $iteration = $key + 1;
                $data[$key] = [
                    'id'            => $iteration,
                    'text'          => $item->nama_barang,
                    'id_barang'     => $item->id_barang,
                    'nama_barang'   => $item->nama_barang,
                    'id_gudang'     => $item->id_gudang,
                    'id_warna'      => $item->id_warna,
                    'stok_utama'    => isset($getStok->stok_utama) ? $getStok->stok_utama : 0,
                    'id_satuan_1'   => $item->id_satuan_1,
                    'nama_satuan_1' => $item->relSatuan1()->value('name'),
                    'stok_pilihan'  => isset($getStok->stok_pilihan) ? $getStok->stok_pilihan : 0,
                    'id_satuan_2'   => $item->id_satuan_2,
                    'nama_satuan_2' => $item->relSatuan2()->value('name')
                ];
                return $data;
            });
        return response(['select_barang' => $dataDistribusiPakan], 200);
    }

    public function importPenerimaanBarang(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 1;
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 2;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'PB';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'PB';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importDoubling(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 1;
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 2;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'PB';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';
                $logStokMasuk['is_doubling'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'PB';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importBenangGreyDyeing(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 2;
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 2;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'BBD';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BBD';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importDyeingOvercone(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 2;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 1;
                $data['id_satuan_2'] = 2;
                $data['volume_1']    = $item['jml'];
                $data['volume_2']    = $item['jml_2'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'DO';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'DO';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importHasilDyeing(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 1;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_satuan_1'] = 1;
                $data['id_satuan_2'] = 2;
                $data['volume_1']    = $item['jml'];
                $data['volume_2']    = $item['jml_2'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BHD';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BHD';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importBarangWarping(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 3;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_satuan_1'] = 1;
                $data['id_satuan_2'] = 2;
                $data['volume_1']    = $item['jml'];
                $data['volume_2']    = $item['jml_2'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BBW';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BBW';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importBarangLusi(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 3;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'] == '' ? null : $item['id_warna'];
                $data['id_motif']    = $item['id_motif'] == '' ? null : $item['id_motif'];
                $data['id_satuan_1'] = 3;
                $data['id_satuan_2'] = 4;
                $data['volume_1']    = 1;
                $data['volume_2']    = $item['jml'];

                $idNomorKikw = $item['no_kikw'] != '' ? NomorKikw::create(['name' => $item['no_kikw']])->id : null;
                $data['id_beam'] = Beam::create(['id_nomor_beam' => $item['id_nomor_beam'], 'id_nomor_kikw' => $idNomorKikw, 'tipe_beam' => 'LUSI'])->id;

                if ($item['id_mesin'] != '') {
                    MesinHistory::create(['id_mesin' => $item['id_mesin'], 'id_beam' => $data['id_beam']]);
                    $data['id_mesin'] = $item['id_mesin'];
                } else {
                    $data['id_mesin'] = null;
                }

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BL';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BL';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importBarangSongket(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 3;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'] == '' ? null : $item['id_warna'];
                $data['id_motif']    = $item['id_motif'] == '' ? null : $item['id_motif'];
                $data['id_satuan_1'] = 3;
                $data['id_satuan_2'] = 4;
                $data['volume_1']    = 1;
                $data['volume_2']    = $item['jml'];

                $idNomorKikw = null;
                if ($item['no_kikw'] != null) $idNomorKikw = NomorKikw::create(['name' => $item['no_kikw']])->id;
                $data['id_beam'] = Beam::create(['id_nomor_kikw' => $idNomorKikw, 'tipe_beam' => 'SONGKET'])->id;
                if ($item['id_mesin'] != null) MesinHistory::create(['id_mesin' => $item['id_mesin'], 'id_beam' => $data['id_beam']]);

                $data['id_mesin'] = $item['id_mesin'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BS';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BS';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importPakanShuttle(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 7;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BPS';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BPS';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importPakanRappier(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 7;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_satuan_1'] = 1;
                $data['id_satuan_2'] = 2;
                $data['volume_1']    = $item['jml'];
                $data['volume_2']    = $item['jml_2'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BPR';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BPR';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importTenunLusi(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 4;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_satuan_1'] = 3;
                $data['id_satuan_2'] = 4;
                $data['volume_1']    = 1;
                $data['volume_2']    = $item['jml'];

                $idNomorKikw = NomorKikw::create(['name' => $item['no_kikw']])->id;
                $data['id_beam'] = Beam::create(['id_nomor_beam' => $item['id_nomor_beam'], 'id_nomor_kikw' => $idNomorKikw, 'tipe_beam' => 'LUSI'])->id;
                MesinHistory::create(['id_mesin' => $item['id_mesin'], 'id_beam' => $data['id_beam']]);

                $data['id_mesin'] = $item['id_mesin'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BBTL';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';


                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BBTL';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importTenunSongket(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataTenunDetail = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 4;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_satuan_1'] = 3;
                $data['id_satuan_2'] = 4;
                $data['volume_1']    = 1;
                $data['volume_2']    = $item['jml'];

                $idNomorKikw = null;
                if ($item['no_kikw'] != null) $idNomorKikw = NomorKikw::create(['name' => $item['no_kikw']])->id;
                $data['id_beam'] = Beam::create(['id_nomor_kikw' => $idNomorKikw, 'tipe_beam' => 'SONGKET'])->id;
                if ($item['id_mesin'] != null) MesinHistory::create(['id_mesin' => $item['id_mesin'], 'id_beam' => $data['id_beam']]);

                $data['id_mesin'] = $item['id_mesin'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BBTS';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BBTS';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                if ($item['id_tenun'] != '') {
                    $logStokKeluar = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                    $logStokKeluar['code'] = 'BBTS';
                    $logStokKeluar['volume_keluar_1'] = $data['volume_1'];
                    $logStokKeluar['volume_keluar_2'] = $data['volume_2'];

                    $dataTenunDetail[$key] = $data;
                    $dataTenunDetail[$key]['id_tenun'] = $item['id_tenun'];
                    $dataTenunDetail[$key]['code'] = 'BBTS';
                    $dataTenunDetail[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokKeluar);
                    $dataTenunDetail[$key]['created_by'] = 1;
                }
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            if (!empty($dataTenunDetail)) DB::table('tbl_tenun_detail')->insert($dataTenunDetail);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelPakan(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataTenunDetail = [];
            foreach ($dataExcel as $key => $item) {

                $code = $item['code'];

                $volumePcs = $item['pcs'];
                $volumeKg = floatValue($item['kg']);

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = '4';
                $data['id_warna']    = $item['id_warna'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_beam']     = $item['id_beam'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = ($code == 'DPR') ? 1 : 4;
                $data['id_satuan_2'] = ($code == 'DPR') ? 2 : NULL;
                $data['volume_1']    = $volumePcs;
                $data['volume_2']    = ($code == 'DPR') ? $volumeKg : NULL;
                $data['code']        = $code;

                //LOGSTOKSALDOAWAL
                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['is_saldoawal'] = 'YA';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                //LOGSTOKTENUNDETAIL
                $logStokKeluar = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokKeluar['volume_keluar_1'] = $data['volume_1'];
                $logStokKeluar['volume_keluar_2'] = $data['volume_2'];
                $dataTenunDetail[$key] = $data;
                $dataTenunDetail[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokKeluar);
                $dataTenunDetail[$key]['id_tenun'] = $item['id_tenun'];
                $dataTenunDetail[$key]['created_by'] = 1;
            }

            //SALDOAWAL
            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::table('tbl_tenun_detail')->insert($dataTenunDetail);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelInspekting(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_warna']    = $item['id_warna'] == '' ? null : $item['id_warna'];
                $data['id_motif']    = $item['id_motif'] == '' ? null : $item['id_motif'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_beam']     = $item['id_beam'] == '' ? null : $item['id_beam'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                //LOGSTOKTENUNDETAIL
                $logStokMasuk                   = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['id_gudang']      = 5;
                $logStokMasuk['code']           = 'BGIG';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal']   = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BGIG';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            //SALDOAWAL
            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelDudulan(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataDudulan = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = $item['id_gudang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                // $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_beam']     = $item['id_beam'] == '' ? null : $item['id_beam'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];
                $data['code']        = 'BGIG';

                //LOGSTOKSALDOAWAL
                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['is_saldoawal'] = 'YA';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                //LOGSTOKTENUNDETAIL
                $logStokKeluar = unsetMultiKeys(['volume_1'], $data);
                $logStokKeluar['volume_keluar_1'] = $data['volume_1'];

                $dataDudulan[$key] = $data;
                $dataDudulan[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokKeluar);
                $dataDudulan[$key]['id_dudulan'] = $item['id_dudulan'];
                $dataDudulan[$key]['created_by'] = 1;
            }

            //SALDOAWAL
            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            //DUDULANDETAIL
            DB::table('tbl_dudulan_detail')->insert($dataDudulan);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelDudulan2(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = $item['id_gudang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_beam']     = $item['id_beam'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];
                $data['code']        = 'BGD';

                //LOGSTOKSALDOAWAL
                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['is_saldoawal'] = 'YA';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            //SALDOAWAL
            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelInspectDudulan(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 5;
                $data['id_warna']    = $item['id_warna'] == '' ? null : $item['id_warna'];
                $data['id_motif']    = $item['id_motif'] == '' ? null : $item['id_motif'];
                $data['id_beam']     = $item['id_beam'] == '' ? null : $item['id_beam'];
                $data['id_grade']    = 1;
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];
                $data['code']        = 'BGID';

                //LOGSTOKSALDOAWAL
                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['is_saldoawal'] = 'YA';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            //SALDOAWAL
            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelJahitSambung(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];
                $data['code']        = 'JS';

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                /*if ($item['belum_jahit'] != null || $item['belum_jahit'] != '') {
                    $logStokMasukJahitSambung = unsetMultiKeys(['volume_1'], $data);
                    $logStokMasukJahitSambung['is_saldoawal'] = 'YA';
                    $logStokMasukJahitSambung['code'] = 'BGF';
                    $logStokMasukJahitSambung['volume_masuk_1'] = $item['belum_jahit'];

                    $dataBelumJahitSambung[$key] = $data;
                    $dataBelumJahitSambung[$key]['code'] = 'BGF';
                    $dataBelumJahitSambung[$key]['volume_1'] = $item['belum_jahit'];
                    $dataBelumJahitSambung[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasukJahitSambung);
                    $dataBelumJahitSambung[$key]['created_by'] = 1;
                }*/
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);
            // if (!empty($dataBelumJahitSambung)) DB::table('tbl_saldoawal')->insert($dataBelumJahitSambung);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelP1(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataP1 = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'JS';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'JS';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                $logStokP1 = unsetMultiKeys(['volume_1'], $data);
                $logStokP1['code'] = 'JS';
                $logStokP1['volume_keluar_1'] = $data['volume_1'];

                $dataP1[$key] = $data;
                $dataP1[$key]['id_p1'] = $item['id_p1'];
                $dataP1[$key]['code'] = 'JS';
                $dataP1[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokP1);
                $dataP1[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::table('tbl_p1_detail')->insert($dataP1);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelFinishingCabut(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataCabut = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'IP1';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'IP1';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                $logStokP1 = unsetMultiKeys(['volume_1'], $data);
                $logStokP1['code'] = 'IP1';
                $logStokP1['volume_keluar_1'] = $data['volume_1'];

                $dataCabut[$key] = $data;
                $dataCabut[$key]['id_finishing_cabut'] = $item['id_finishing_cabut'];
                $dataCabut[$key]['code'] = 'IP1';
                $dataCabut[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokP1);
                $dataCabut[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::table('tbl_finishing_cabut_detail')->insert($dataCabut);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelDrying(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'DR';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'DR';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelP2(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataP2 = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'DR';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'DR';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                $logStokP2 = unsetMultiKeys(['volume_1'], $data);
                $logStokP2['code'] = 'DR';
                $logStokP2['volume_keluar_1'] = $data['volume_1'];

                $dataP2[$key] = $data;
                $dataP2[$key]['id_p2'] = $item['id_p2'];
                $dataP2[$key]['code'] = 'DR';
                $dataP2[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokP2);
                $dataP2[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::table('tbl_p2_detail')->insert($dataP2);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelInspectP2(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'IP2';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'IP2';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelInpectP1(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_mesin']    = $item['id_mesin'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'IP1';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'IP1';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelJahit(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            $dataP2 = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'IP2';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'IP2';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;

                $logStokP2 = unsetMultiKeys(['volume_1'], $data);
                $logStokP2['code'] = 'IP2';
                $logStokP2['volume_keluar_1'] = $data['volume_1'];

                $dataP2[$key] = $data;
                $dataP2[$key]['id_jahit_p2'] = $item['id_jahit_p2'];
                $dataP2[$key]['code'] = 'IP2';
                $dataP2[$key]['id_log_stok_penerimaan'] = DB::table('log_stok_penerimaan')->insertGetId($logStokP2);
                $dataP2[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::table('tbl_jahit_p2_detail')->insert($dataP2);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelJahitP2(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_warna']    = $item['id_warna'];
                $data['id_motif']    = $item['id_motif'];
                // $data['id_beam']     = $item['id_beam'] == '' ? null : $item['id_beam'];
                // $data['id_mesin']    = $item['id_mesin'] == '' ? null : $item['id_mesin'];
                $data['id_grade']    = $item['id_grade'];
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 4;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'JP2';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'JP2';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelChemicalFinishing(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2023-12-31';
                $data['id_gudang']   = 6;
                $data['id_barang']   = $item['id_barang'];
                $data['id_satuan_1'] = 2;
                $data['volume_1']    = $item['jml'];

                $logStokMasuk = unsetMultiKeys(['volume_1'], $data);
                $logStokMasuk['code'] = 'CF';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'CF';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function importExcelBenangWarnaPakan(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataExcel = Excel::toArray(new Import, $request->file('file_excel'))[0];
            $dataSaldoawal = [];
            foreach ($dataExcel as $key => $item) {

                $data['tanggal']     = '2024-02-29';
                $data['id_gudang']   = 7;
                $data['id_barang']   = $item['id_barang'];
                $data['id_warna']    = $item['id_warna'];
                $data['id_satuan_1'] = 1;
                $data['id_satuan_2'] = 2;
                $data['volume_1']    = $item['jml'] ?? 0;
                $data['volume_2']    = $item['jml_2'];

                $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2'], $data);
                $logStokMasuk['code'] = 'BBWP';
                $logStokMasuk['volume_masuk_1'] = $data['volume_1'];
                $logStokMasuk['volume_masuk_2'] = $data['volume_2'];
                $logStokMasuk['is_saldoawal'] = 'YA';

                $dataSaldoawal[$key] = $data;
                $dataSaldoawal[$key]['code'] = 'BBWP';
                $dataSaldoawal[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStokMasuk);
                $dataSaldoawal[$key]['created_by'] = 1;
            }

            DB::table('tbl_saldoawal')->insert($dataSaldoawal);

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage() . '' . $th->getLine());
        }
    }

    public function exportStokopname()
    {
        $tanggal = request('tanggal') ?? date('d-m-Y');
        $id_gudang = request('id_gudang') ?? 'ALL';
        $code = request('code');

        $prosesTxt = camelCaseConvert(StokopnameCodeText($code));
        $tanggalTxt = str_replace('-', '_', $tanggal);
        return Excel::download(new StokopnameExport($id_gudang, $code, $tanggal), "stokopname_{$prosesTxt}_{$tanggalTxt}.xlsx");
    }

    public function importStokopname(Request $request)
    {
        ini_set('max_execution_time', 180);
        DB::beginTransaction();
        $checkStokBarangFilter = [];
        try {
            $dataExcel = Excel::toArray(new Import(3), $request->file('file_excel'))[0];
            $dataStokopnameDetail = [];
            $code = $tanggal = '';
            foreach ($dataExcel as $key => $item) {

                $keyStokopname = 'stokopname';
                if (!array_key_exists('stokopname', $item)) $keyStokopname = 'stokopname_1';
                // if ($item[$keyStokopname] == null && $item[$keyStokopname] != 0) throw new Exception("Stokopname tidak boleh kosong!", 1);

                $data['id_gudang']      = $item['id_gudang'];
                $data['id_barang']      = $item['id_barang'];
                $data['id_warna']       = $item['id_warna'];
                $data['id_motif']       = $item['id_motif'];
                $data['id_beam']        = $item['id_beam'];
                $data['tipe_pra_tenun'] = $item['tipe_pra_tenun'];
                $data['is_sizing']      = $item['is_sizing'];
                $data['id_mesin']       = $item['id_mesin'];
                $data['id_grade']       = $item['id_grade'];
                $data['id_kualitas']    = $item['id_kualitas'];
                $data['id_satuan_1']    = $item['id_satuan_1'];
                $data['id_satuan_2']    = $item['id_satuan_2'];
                $data['stokopname_1']   = floatValue($item[$keyStokopname] ?? 0);
                $data['code']           = $item['code'];

                $logStok = unsetMultiKeys(['stokopname_1'], $data);
                $logStok['tanggal']       = $item['tanggal'];
                $logStok['is_stokopname'] = 'YA';

                $checkStokBarangFilter = unsetMultiKeys(['stokopname_1'], $data);
                $stok_1                = checkStokBarang($checkStokBarangFilter, false, $item['tanggal'])->stok_utama ?? 0;
                $stokopname_1          = $data['stokopname_1'];
                // $selisih_1             = $stok_1 - $stokopname_1;
                $selisih_1             = normalizeDecimal($stokopname_1, $stok_1, $stok_1 - $stokopname_1);

                $dataStokopnameDetail[$key] = unsetMultiKeys(['code', 'is_sizing', 'tipe_pra_tenun'], $data);
                $dataStokopnameDetail[$key]['stok_1'] = $stok_1;

                if ($stok_1 > $stokopname_1) {
                    $logStok['volume_keluar_1'] = $selisih_1;
                    $dataStokopnameDetail[$key]['selisih_1'] = $selisih_1;
                } else {
                    $logStok['volume_masuk_1'] = abs($selisih_1 * -1);
                    $dataStokopnameDetail[$key]['selisih_1'] = abs($selisih_1 * -1);
                }

                if ($keyStokopname == 'stokopname_1') {
                    $stok_2                = checkStokBarang($checkStokBarangFilter, false, $item['tanggal'])->stok_pilihan ?? 0;
                    $stokopname_2          = floatValue($item['stokopname_2']);
                    // $selisih_2             = $stok_2 - $stokopname_2;
                    $selisih_2             = normalizeDecimal($stokopname_2, $stok_2, $stok_2 - $stokopname_2);

                    $dataStokopnameDetail[$key]['stok_2']       = $stok_2;
                    $dataStokopnameDetail[$key]['stokopname_2'] = $stokopname_2;

                    if ($stok_2 > $stokopname_2) {
                        $logStok['volume_keluar_2'] = $selisih_2;
                        $dataStokopnameDetail[$key]['selisih_2'] = $selisih_2;
                    } else {
                        $logStok['volume_masuk_2'] = abs($selisih_2 * -1);
                        $dataStokopnameDetail[$key]['selisih_2'] = abs($selisih_2 * -1);
                    }
                }

                $dataStokopnameDetail[$key]['catatan']     = $item['catatan'];
                $dataStokopnameDetail[$key]['id_log_stok'] = DB::table('log_stok_penerimaan')->insertGetId($logStok);
                $dataStokopnameDetail[$key]['created_by']  = 1;

                $tanggal = $item['tanggal'];
                $code    = $item['code'];
            }

            $dataStokopname['tanggal'] = $tanggal;
            $dataStokopname['proses'] = StokopnameCodeText($code);
            $dataStokopname['code'] = $code;
            $idStokopname = Stokopname::create($dataStokopname)->id;

            data_fill($dataStokopnameDetail, '*.id_stokopname', $idStokopname);

            DB::table('tbl_stokopname_detail')->insert($dataStokopnameDetail);

            DB::commit();
            return response('Data Successfully Imported!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function getJasaLuar(Reqeuest $request)
    {
        $param = strtolower($request['param']) ?? '';
        $model = $request['modelJasaLuar'] ?? '';

        if ($model == '') return [];

        $constructor = getModel($model)::when($param, function ($query, $value) {
            return $query->whereRaw("LOWER(nomor) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        return Define::fetchSelect2($request, $constructor, ['id', 'nomor']);
    }

    public function getSelectedBarangInspecting(Request $request)
    {
        $model = $request['model'] ?? '';
        $idParent = '';
        if ($model == 'InspectP1Detail') $idParent = 'id_p1';
        if ($model == 'InspectFinishingCabutDetail') $idParent = 'id_finishing_cabut';
        if ($model == 'InspectP2Detail') $idParent = 'id_p2';

        $uniqueKey = rand(1, 1000);
        $item = getModel($model)::where('id', $request['id'])->first();
        $response = [
            'id' => $uniqueKey,
            'text' => $item->nomor . ' | ' . $item->relMesin()->value('name') . ' | ' . $item->throughNomorKikw()->value('name') . ' | ' . $item->relBarang()->value('name') . ' | ' . $item->relWarna()->value('name') . ' | ' . $item->relMotif()->value('alias') . ' | ' . $item->relGrade()->value('grade'),
            'data' => [
                'id_parent'      => $item->$idParent,
                'id_mesin'       => $item->id_mesin,
                'id_barang'      => $item->id_barang,
                'id_warna'       => $item->id_warna,
                'id_motif'       => $item->id_motif,
                'id_beam'        => $item->id_beam,
                'id_grade'       => $item->id_grade,
                'id_grade_awal'  => $item->id_grade,
                'id_gudang'      => $item->id_gudang
            ]
        ];
        return $response;
    }

    public function returInspect(Request $request)
    {
        DB::beginTransaction();
        try {
            $input                   = $request->all()['input'];
            $modelInspect            = getModel($input['model_inspect']);
            $modelJasaLuar           = getModel($input['model_jasa_luar']);
            $data                    = json_decode($request['data'], true);
            $dataFiltered            = unsetMultiKeys(['id_parent', 'id_grade_awal', 'id', 'code_kirim', 'code_jasa_luar', 'code_inspect'], $data);
            $dataFiltered['tanggal'] = $input['tanggal'];

            $codeKirim    = $data['code_kirim'];
            $codeJasaLuar = $data['code_jasa_luar'];
            $codeInspect  = $data['code_inspect'];

            $inspecting                       = $dataFiltered;
            $inspecting[$input['primary_id']] = $data['id_parent'];
            $inspecting['volume_1']           = $input['volume'];
            $inspecting['id_satuan_1']        = 4;
            $inspecting['code']               = $codeInspect;

            $logStokKeluar                    = $dataFiltered;
            $logStokKeluar['volume_keluar_1'] = $input['volume'];
            $logStokKeluar['id_satuan_1']     = 4;
            $logStokKeluar['code']            = $codeJasaLuar;

            if ($data['id'] == '') {
                $inspecting['id_log_stok_penerimaan_keluar'] = LogStokPenerimaan::create($logStokKeluar)->id;
            } else {
                LogStokPenerimaan::where('id', $input['id_log_stok_inspect_keluar'])->update($logStokKeluar);
            }

            $logStokMasuk                   = $dataFiltered;
            $logStokMasuk['volume_masuk_1'] = 0;
            $logStokMasuk['id_satuan_1']    = 4;
            $logStokMasuk['code']           = $codeInspect;

            if ($data['id'] == '') {
                $inspecting['id_log_stok_penerimaan_masuk'] = LogStokPenerimaan::create($logStokMasuk)->id;
            } else {
                LogStokPenerimaan::where('id', $input['id_log_stok_inspect_masuk'])->update($logStokMasuk);
            }

            $jasaLuar                       = $dataFiltered;
            $jasaLuar[$input['primary_id']] = $data['id_parent'];
            $jasaLuar['volume_1']           = $input['volume'];
            $jasaLuar['id_satuan_1']        = 4;
            $jasaLuar['code']               = $codeKirim;

            if ($data['id'] == '') {
                $jasaLuar['id_inspect_retur'] = $modelInspect::create($inspecting)->id;
            } else {
                $modelInspect::where('id', $data['id'])->update($inspecting);
            }

            $logStokKeluarJasaLuar                    = $dataFiltered;
            $logStokKeluarJasaLuar['volume_keluar_1'] = 0;
            $logStokKeluarJasaLuar['id_satuan_1']     = 4;
            $logStokKeluarJasaLuar['code']            = $codeKirim;

            if ($data['id'] == '') {
                $jasaLuar['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logStokKeluarJasaLuar)->id;
            } else {
                LogStokPenerimaan::where('id', $input['id_log_stok_jasa_luar'])->update($logStokKeluarJasaLuar);
            }


            if ($data['id'] == '') {
                $modelJasaLuar::create($jasaLuar);
            } else {
                $modelJasaLuar::where('id_inspect_retur', $data['id'])->update($jasaLuar);
            }
            DB::commit();
            return response('Data Successfully Updated!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage() . ' ' . $th->getLine(), 401);
        }
    }

    public function inputPakanKirim(Request $request)
    {
        DB::beginTransaction();
        try {
            $idPakan = $request->id;
            $tanggal = $request->tanggal;
            $dataPakanDetail = $dataPakanDetailRappier = [];
            PengirimanBarangDetail::whereHas('relPengirimanBarang', function ($query) {
                return $query->where('id_tipe_pengiriman', 21);
            })->where('status', 'TUJUAN')->whereNull('id_pakan_detail')->where('tanggal', $tanggal)
                ->each(function ($item, $key) use ($idPakan, &$dataPakanDetail, &$dataPakanDetailRappier) {
                    $item = $item->toArray();
                    $logStok['tanggal']         = $item['tanggal'];
                    $logStok['id_barang']       = $item['id_barang'];
                    $logStok['id_warna']        = $item['id_warna'];
                    $logStok['id_gudang']       = $item['id_gudang'];
                    $logStok['code']            = 'BBWP';
                    $logStok['volume_keluar_1'] = $item['volume_1'];
                    $logStok['id_satuan_1']     = $item['id_satuan_1'];
                    $logStok['volume_keluar_2'] = $item['volume_2'];
                    $logStok['id_satuan_2']     = $item['id_satuan_2'];
                    $idLogStokPenerimaan = LogStokPenerimaan::create($logStok)->id;

                    $dataPakanDetail[$key]['id_pakan']               = $idPakan;
                    $dataPakanDetail[$key]['tanggal']                = $item['tanggal'];
                    $dataPakanDetail[$key]['id_barang']              = $item['id_barang'];
                    $dataPakanDetail[$key]['id_warna']               = $item['id_warna'];
                    $dataPakanDetail[$key]['id_gudang']              = $item['id_gudang'];
                    $dataPakanDetail[$key]['id_log_stok_penerimaan'] = $idLogStokPenerimaan;
                    $dataPakanDetail[$key]['volume_1']               = $item['volume_1'];
                    $dataPakanDetail[$key]['id_satuan_1']            = $item['id_satuan_1'];
                    $dataPakanDetail[$key]['volume_2']               = $item['volume_2'];
                    $dataPakanDetail[$key]['id_satuan_2']            = $item['id_satuan_2'];
                    $dataPakanDetail[$key]['code']                   = 'BBWP';
                    $dataPakanDetail[$key]['created_by']             = Auth::id();
                    $dataPakanDetail[$key]['created_at']             = now();

                    $idDetailPakan = PakanDetail::insertGetId($dataPakanDetail[$key]);

                    $logStokRappier = unsetMultiKeys(['code', 'id_barang', 'volume_keluar_1', 'volume_keluar_2'], $logStok);
                    $logStokRappier['code'] = 'BPR';

                    $checkIdBarang = fixBenangPakan($item['id_barang']);
                    if ($checkIdBarang['id'] == 0) throw new Exception("Error Pakan {$checkIdBarang['name']}", 1);
                    $logStokRappier['id_barang'] = $checkIdBarang['id'];

                    $logStokRappier['volume_masuk_1'] = $item['volume_1'];
                    $logStokRappier['volume_masuk_2'] = $item['volume_2'];
                    $idLogStokPenerimaanRappier = LogStokPenerimaan::create($logStokRappier)->id;

                    $dataPakanDetailRappier[$key] = unsetMultiKeys(['code', 'id_barang', 'id_log_stok_penerimaan'], $dataPakanDetail[$key]);
                    $dataPakanDetailRappier[$key]['id_parent_detail'] = $idDetailPakan;
                    $dataPakanDetailRappier[$key]['code'] = 'BPR';
                    $dataPakanDetailRappier[$key]['id_barang'] = $logStokRappier['id_barang'];
                    $dataPakanDetailRappier[$key]['id_log_stok_penerimaan'] = $idLogStokPenerimaanRappier;

                    DB::table('tbl_pengiriman_barang_detail')->where('id', $item['id'])->update(['id_pakan_detail' => $idDetailPakan]);
                });

            if (empty($dataPakanDetailRappier)) throw new Exception("Tidak ada pengiriman benang warna pakan di tanggal tersebut!", 1);
            PakanDetail::insert($dataPakanDetailRappier);

            DB::commit();
            return response('Data Pakan dari Pengiriman Berhasil ditambahkan!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function getStokBarang(Request $request)
    {
        return checkStokBarang($request->all(), false);
    }
}
