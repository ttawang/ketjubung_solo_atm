<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Beam;
use App\Models\Grade;
use App\Models\Gudang;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Models\Mesin;
use App\Models\Motif;
use App\Models\ProductionCode;
use App\Models\Supplier;
use App\Models\TenunDetail;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PersediaanController extends Controller
{
    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Inventory', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Persediaan Barang', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('', 'persediaan', $data['breadcumbs'], true, false, true, true);
        return view('contents.inventory.persediaan.index', $data);
    }
    public function view($mode)
    {
        $data['jenis'] = ProductionCode::all();
        $data['barang'] = $this->getBarang($mode);
        $data['warna'] = $this->getWarna($mode);
        $data['motif'] = $this->getMotif($mode);
        $data['grade'] = Kualitas::all();
        $data['kualitas'] = MappingKualitas::all();
        $data['gudang'] = $this->getGudang($mode);
        if ($mode == 'stok') {
            return view('contents.inventory.persediaan.stok', $data);
        } else if ($mode == 'beam') {
            $data['beam'] = $this->getBeam($mode);
            $data['mesin'] = $this->getMesin($mode);
            return view('contents.inventory.persediaan.stok-beam', $data);
        } else {
            $data['supplier'] = $this->getSupplier();
            return view('contents.inventory.persediaan.stok-wip', $data);
        }
    }
    public function table(Request $request)
    {
        if ($request->mode == 'stok') {
            $jenis = ($request->jenis == 'semua') ? null : $request->jenis;
            $barang = ($request->barang == 'semua') ? null : $request->barang;
            $warna = ($request->warna == 'semua') ? null : $request->warna;
            $motif = ($request->motif == 'semua') ? null : $request->motif;
            $gudang = ($request->gudang == 'semua') ? null : $request->gudang;
            $grade = ($request->grade == 'semua') ? null : $request->grade;
            $kualitas = ($request->kualitas == 'semua') ? null : $request->kualitas;
            $subCode = DB::table('production_code')->select('code', 'alias', 'urutan')->orderBy('urutan', 'ASC');
            $codesRequiringIdBeam = ['BBTL', 'BBTLR', 'BBTLT', 'BBTS', 'BBTSR', 'BBTST', 'BS', 'BL', 'BZ'];
            $sql = "SELECT
                pc.nama nama_proses,
                gudang.name nama_gudang,
                mesin.name nama_mesin,
                warna.alias nama_warna,
                motif.alias nama_motif,
                barang.name nama_barang,
                no_beam.name no_beam,
                no_kikw.name no_kikw,
                no_kiks.name no_kiks,
                grade.grade nama_grade,
                kualitas.kode nama_kualitas,
                data.*
                FROM
                (
                SELECT
                    log.code,
                    log.id_gudang,
                    log.id_beam,
                    log.id_songket,
                    log.tanggal_potong,
                    log.id_barang,
                    log.id_warna,
                    log.id_motif,
                    log.id_grade,
                    log.id_kualitas,
                    log.id_mesin,
                    log.is_sizing,
                    CASE
                        WHEN id_satuan_1 = 1 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 1 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_cones,
                    CASE
                        WHEN id_satuan_1 = 2 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 2 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_kg,
                    CASE
                        WHEN id_satuan_1 = 3 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 3 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_beam,
                    CASE
                        WHEN id_satuan_1 = 4 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 4 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_pcs,
                    CASE
                        WHEN id_satuan_1 = 5 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 5 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_gram,
                    CASE
                        WHEN id_satuan_1 = 6 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                        WHEN id_satuan_2 = 6 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                    END AS stok_meter
                    FROM log_stok_penerimaan AS log
                    WHERE log.deleted_at IS NULL
                    GROUP BY
                    log.code,
                    log.id_gudang,
                    log.id_beam,
                    log.id_songket,
                    log.tanggal_potong,
                    log.id_barang,
                    log.id_warna,
                    log.id_motif,
                    log.id_grade,
                    log.id_kualitas,
                    log.id_mesin,
                    log.is_sizing,
                    log.id_satuan_1,
                    log.id_satuan_2
                ) AS data
                LEFT JOIN production_code AS pc ON pc.code = data.code
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN tbl_gudang AS gudang ON gudang.id = data.id_gudang
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                LEFT JOIN tbl_kualitas AS grade ON grade.id = data.id_grade
                LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.id = data.id_kualitas
                LEFT JOIN tbl_beam AS lusi ON lusi.id = data.id_beam
                LEFT JOIN tbl_beam AS songket ON songket.id = data.id_songket
                LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = lusi.id_nomor_kikw
                LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
                LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = lusi.id_nomor_beam
            ";
            $data = DB::table(DB::raw("({$sql}) as data"))
                ->where(function ($q) {
                    $q->where('stok_cones', '!=', 0)
                        ->orWhere('stok_kg', '!=', 0)
                        ->orWhere('stok_beam', '!=', 0)
                        ->orWhere('stok_pcs', '!=', 0)
                        ->orWhere('stok_meter', '!=', 0);
                })
                ->when($jenis, function ($q) use ($jenis) {
                    $q->where('code', $jenis);
                })
                ->when($barang, function ($q) use ($barang) {
                    $q->where('id_barang', $barang);
                })
                ->when($warna, function ($q) use ($warna) {
                    $q->where('id_warna', $warna);
                })
                ->when($motif, function ($q) use ($motif) {
                    $q->where('id_motif', $motif);
                })
                ->when($gudang, function ($q) use ($gudang) {
                    $q->where('id_gudang', $gudang);
                })
                ->when($grade, function ($q) use ($grade) {
                    $q->where('id_grade', $grade);
                })
                ->when($kualitas, function ($q) use ($kualitas) {
                    $q->where('id_kualitas', $kualitas);
                })
                ;
            // dd($data->toSql());
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal_potong', function ($i) {
                    return Date::format($i->tanggal_potong, 98);
                })
                ->make(true);
            /* $data = LogStokPenerimaan::selectRaw("
                CASE
                    WHEN id_satuan_1 = 1 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 1 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_cones,
                CASE
                    WHEN id_satuan_1 = 2 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 2 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_kg,
                CASE
                    WHEN id_satuan_1 = 3 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 3 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_beam,
                CASE
                    WHEN id_satuan_1 = 4 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 4 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_pcs,
                CASE
                    WHEN id_satuan_1 = 5 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 5 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_gram,
                CASE
                    WHEN id_satuan_1 = 6 THEN SUM(COALESCE(volume_masuk_1,0)::decimal) - SUM(COALESCE(volume_keluar_1,0)::decimal)
                    WHEN id_satuan_2 = 6 THEN SUM(COALESCE(volume_masuk_2,0)::decimal) - SUM(COALESCE(volume_keluar_2,0)::decimal)
                END AS stok_meter,
                CASE
                    WHEN sub_code.code = 'BL' AND is_sizing = 'YA' THEN CONCAT(sub_code.alias, ' Kanjian')
                    ELSE sub_code.alias
                END AS code_alias,
                id_mesin,
                CASE
                    WHEN sub_code.code IN ('BBTL', 'BBTLR', 'BBTLT', 'BBTS', 'BBTSR', 'BBTST','BS','BL','BZ') THEN id_beam
                    ELSE null
                END AS id_beam,
                sub_code.code as code,
                id_songket,
                tanggal_potong,
                id_barang,
                id_warna,
                id_motif,
                id_gudang,
                id_kualitas,
                id_grade
                ")
                ->leftjoinsub($subCode, 'sub_code', function ($join) {
                    return $join->on('log_stok_penerimaan.code', '=', 'sub_code.code');
                })
                ->orderBy('sub_code.urutan', 'asc')
                ->when($barang, function ($q) {
                    return $q->selectRaw('id_barang')->groupBy('id_barang')->orderBy('id_barang', 'asc');
                })->when($warna, function ($q) {
                    return $q->selectRaw('id_warna')->groupBy('id_warna')->orderBy('id_warna', 'asc');
                })->when($motif, function ($q) {
                    return $q->selectRaw('id_motif')->groupBy('id_motif')->orderBy('id_motif', 'asc');
                })->when($gudang, function ($q) {
                    return $q->selectRaw('id_gudang')->groupBy('id_gudang')->orderBy('id_gudang', 'asc');
                })->when($grade, function ($q) {
                    return $q->selectRaw('id_grade')->groupBy('id_grade')->orderBy('id_grade', 'asc');
                })->when($kualitas, function ($q) {
                    return $q->selectRaw('id_kualitas')->groupBy('id_kualitas')->orderBy('id_kualitas', 'asc');
                })
                ->when($kualitas, function ($q) use ($kualitas) {
                    if ($kualitas != 'semua') {
                        return $q->where('id_kualitas', $kualitas);
                    }
                })->when($grade, function ($q) use ($grade) {
                    if ($grade != 'semua') {
                        return $q->where('id_grade', $grade);
                    }
                })->when($gudang, function ($q) use ($gudang) {
                    if ($gudang != 'semua') {
                        return $q->where('id_gudang', $gudang);
                    }
                })->when($motif, function ($q) use ($motif) {
                    if ($motif != 'semua') {
                        return $q->where('id_motif', $motif);
                    }
                })->when($warna, function ($q) use ($warna) {
                    if ($warna != 'semua') {
                        return $q->where('id_warna', $warna);
                    }
                })->when($barang, function ($q) use ($barang) {
                    if ($barang != 'semua') {
                        return $q->where('id_barang', $barang);
                    }
                })->when($jenis, function ($q) use ($jenis) {
                    if ($jenis != 'semua') {
                        return $q->where('sub_code.code', $jenis);
                    }
                })
                // ->whereNotIn('sub_code.code',['BL','BBTL'])
                // ->groupBy('id_satuan_1', 'id_satuan_2', 'sub_code.code', 'sub_code.alias', 'is_sizing', 'urutan', 'id_mesin')
                ->groupBy('id_satuan_1', 'id_satuan_2', 'sub_code.code', 'sub_code.alias', 'is_sizing', 'urutan', 'id_mesin', 'id_beam', 'id_songket', 'tanggal_potong', 'id_barang', 'id_warna', 'id_motif', 'id_grade', 'id_kualitas', 'id_gudang')
                ->havingRaw('
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 6 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 6 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0
                    OR
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 5 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 5 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0
                    OR
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 4 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 4 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0
                    OR
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 3 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 3 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0
                    OR
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 2 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 2 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0::decimal
                    OR
                    SUM(
                        CASE
                            WHEN id_satuan_1 = 1 THEN COALESCE(volume_masuk_1,0)::decimal - COALESCE(volume_keluar_1,0)::decimal
                            WHEN id_satuan_2 = 1 THEN COALESCE(volume_masuk_2,0)::decimal - COALESCE(volume_keluar_2,0)::decimal
                            ELSE 0
                        END
                    ) != 0
                ');
            
                // dd($data->toSql());
                        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('jenis', function ($i) {
                            return $i->code_alias;
                        })
                        ->addColumn('mesin', function ($i) {
                            $mesin = '';
                            if ($i->id_mesin) {
                                $mesin = $i->relMesin->name ?? '';
                            }
                            return $mesin;
                        })
                        ->addColumn('barang', function ($i) {
                            $barang = '';
                            if ($i->id_barang) {
                                $barang = $i->relBarang->name ?? '';
                            }
                            return $barang;
                        })
                        ->addColumn('kikw', function ($i) {
                            $kikw = '';
                            if ($i->id_beam) {
                                $kikw = $i->relBeam->no_kikw ?? '';
                            }
                            return $kikw;
                        })
                        ->addColumn('kiks', function ($i) {
                            $kiks = '';
                            if ($i->id_songket) {
                                $kiks = $i->relSongket->no_kikw ?? '';
                            }
                            return $kiks;
                        })
                        ->addColumn('tanggal_potong', function ($i) {
                            if ($i->tanggal_potong) {
                                return Date::format($i->tanggal_potong, 98);
                            } else {
                                return '';
                            }
                        })
                        ->addColumn('warna', function ($i) {
                            $warna = '';
                            if ($i->id_warna) {
                                $warna = $i->relWarna->alias ?? '';
                            }
                            return $warna;
                        })
                        ->addColumn('motif', function ($i) {
                            $motif = '';
                            if ($i->id_motif) {
                                $motif = $i->relMotif->alias ?? '';
                            }
                            return $motif;
                        })
                        ->addColumn('grade', function ($i) {
                            $grade = '';
                            if ($i->id_grade) {
                                $grade = $i->relGrade->grade ?? '';
                            }
                            return $grade;
                        })
                        ->addColumn('kualitas', function ($i) {
                            $kualitas = '';
                            if ($i->id_kualitas) {
                                $kualitas = $i->relKualitas->kode ?? '';
                            }
                            return $kualitas;
                        })
                        ->addColumn('gudang', function ($i) {
                            $gudang = '';
                            if ($i->id_gudang) {
                                $gudang = $i->relGudang->name ?? '';
                            }
                            return $gudang;
                        })
                        ->addColumn('stok_ball', function ($i) {
                            $stok = 0;
                            if ($i->stok_ball) {
                                $stok = $i->stok_ball;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_cones', function ($i) {
                            $stok = 0;
                            if ($i->stok_cones) {
                                $stok = $i->stok_cones;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_kg', function ($i) {
                            $stok = 0;
                            if ($i->stok_kg) {
                                $stok = $i->stok_kg;
                            }
                            return toFixed($stok);
                        })
                        ->addColumn('stok_beam', function ($i) {
                            $stok = 0;
                            if ($i->stok_beam) {
                                $stok = $i->stok_beam;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_pcs', function ($i) {
                            $stok = 0;
                            if ($i->stok_pcs) {
                                $stok = $i->stok_pcs;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_palet', function ($i) {
                            $stok = 0;
                            if ($i->stok_palet) {
                                $stok = $i->stok_palet;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_gram', function ($i) {
                            $stok = 0;
                            if ($i->stok_gram) {
                                $stok = $i->stok_gram;
                            }
                            return $stok;
                        })
                        ->addColumn('stok_meter', function ($i) {
                            $stok = 0;
                            if ($i->stok_meter) {
                                $stok = $i->stok_meter;
                            }
                            return $stok;
                        })
                        ->make(true);
                 */
        } else if ($request->mode == 'beam') {
            $beam = ($request->beam == 'semua') ? null : $request->beam;
            $mesin = ($request->mesin == 'semua') ? null : $request->mesin;
            $barang = ($request->barang == 'semua') ? null : $request->barang;
            $warna = ($request->warna == 'semua') ? null : $request->warna;
            $motif = ($request->motif == 'semua') ? null : $request->motif;
            $sql = "
                SELECT
                data.id_beam,
                beam.id_nomor_beam,
                no_beam.name no_beam,
                beam.id_nomor_kikw,
                no_kikw.name no_kikw,
                data.id_mesin,
                mesin.name nama_mesin,
                data.id_barang,
                barang.name nama_barang,
                data.id_gudang,
                gudang.name nama_gudang,
                data.id_warna,
                warna.alias nama_warna,
                data.id_motif,
                motif.alias nama_motif,
                tenun.jumlah_beam jml,
                COALESCE((SELECT SUM(volume_1) FROM tbl_tenun_detail WHERE id_beam = data.id_beam AND deleted_at IS NULL AND code = 'BG' GROUP BY id_beam),0) jml_potong,
                tenun.jumlah_beam - COALESCE((SELECT SUM(volume_1) FROM tbl_tenun_detail WHERE id_beam = data.id_beam AND deleted_at IS NULL AND code = 'BG' GROUP BY id_beam),0) jml_sisa
                FROM
                tbl_tenun_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                LEFT JOIN tbl_gudang AS gudang ON gudang.id = data.id_gudang
                LEFT JOIN tbl_tenun AS tenun ON tenun.id = data.id_tenun
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                WHERE data.code = 'BBTL' AND beam.finish = 0 AND data.deleted_at IS NULL
            ";
            $data = DB::table(DB::raw("({$sql}) as data"))
                ->when($beam, function ($q) use ($beam) {
                    return $q->where('id_beam', $beam);
                })
                ->when($barang, function ($q) use ($barang) {
                    return $q->where('id_barang', $barang);
                })
                ->when($mesin, function ($q) use ($mesin) {
                    return $q->where('id_mesin', $mesin);
                })
                ->when($warna, function ($q) use ($warna) {
                    return $q->where('id_warna', $warna);
                })
                ->when($motif, function ($q) use ($motif) {
                    return $q->where('id_motif', $motif);
                })->orderBy('id_beam', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        } else if ($request->mode == 'jasa_luar') {
            $proses = ($request->proses == 'semua') ? null : $request->proses;
            $barang = ($request->barang == 'semua') ? null : $request->barang;
            $warna = ($request->warna == 'semua') ? null : $request->warna;
            $motif = ($request->motif == 'semua') ? null : $request->motif;
            $gudang = ($request->gudang == 'semua') ? null : $request->gudang;
            $supplier = ($request->supplier == 'semua') ? null : $request->supplier;

            $sql = "
                SELECT
                data.proses,
                data.sort,
                data.id_spk,
                data.nomor,
                data.tanggal,
                data.id_beam,
                no_beam.name no_beam,
                no_kikw.name no_kikw,
                data.id_supplier,
                supplier.name nama_supplier,
                data.id_mesin,
                mesin.name nama_mesin,
                data.id_gudang,
                gudang.name nama_gudang,
                data.id_barang,
                barang.name nama_barang,
                data.id_warna,
                warna.alias nama_warna,
                data.id_motif,
                motif.alias nama_motif,
                data.id_grade,
                grade.grade nama_grade,
                data.id_kualitas,
                kualitas.kode nama_kualitas,
                data.kirim_1,
                data.terima_1,
                data.hilang_1,
                data.sisa_1,
                data.id_satuan_1,
                satuan_1.name nama_satuan_1,
                data.kirim_2,
                data.terima_2,
                data.hilang_2,
                data.sisa_2,
                data.id_satuan_2,
                satuan_2.name nama_satuan_2,
                data.code
                FROM
                (
                SELECT
                'Doubling' proses,
                1 sort,
                data.id_doubling id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                NULL::bigint id_beam,
                NULL::bigint id_mesin,
                data.id_gudang,
                data.id_barang,
                NULL::bigint id_warna,
                NULL::bigint id_motif,
                NULL::bigint id_grade,
                NULL::bigint id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                0 hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                'PB' code
                FROM tbl_doubling_detail AS data
                LEFT JOIN tbl_doubling AS spk ON spk.id = data.id_doubling
                LEFT JOIN tbl_doubling_detail AS terima ON terima.id_parent_detail = data.id AND terima.status = 'TERIMA' AND terima.deleted_at IS NULL
                WHERE data.id_parent_detail IS NULL AND data.deleted_at IS NULL
                
                UNION
                
                SELECT
                'Dyeing Jasa Luar' proses,
                2 sort,
                data.id_dyeing_jasa_luar id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                NULL::bigint id_beam,
                NULL::bigint id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                NULL::bigint id_motif,
                NULL::bigint id_grade,
                NULL::bigint id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_2,0) terima_1,
                0 hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_2,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                'BHD' code
                FROM tbl_dyeing_jasa_luar_detail AS data
                LEFT JOIN tbl_dyeing_jasa_luar AS spk ON spk.id = data.id_dyeing_jasa_luar
                LEFT JOIN tbl_dyeing_jasa_luar_detail AS terima ON terima.id_parent_detail = data.id AND terima.status = 'TERIMA' AND terima.deleted_at IS NULL
                WHERE data.id_parent_detail IS NULL AND data.deleted_at IS NULL
                
                UNION
                
                SELECT
                'Dudulan' proses,
                3 sort,
                data.id_dudulan id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                data.id_beam,
                data.id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                COALESCE(hilang.volume_1,0) hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) - COALESCE(hilang.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                data.code
                FROM tbl_dudulan_detail AS data
                LEFT JOIN tbl_dudulan AS spk ON spk.id = data.id_dudulan
                LEFT JOIN tbl_dudulan_detail AS terima ON terima.id_parent = data.id AND terima.code = 'BGD' AND terima.deleted_at IS NULL
                LEFT JOIN tbl_dudulan_detail AS hilang ON hilang.id_parent = data.id AND hilang.code = 'BGDH' AND terima.deleted_at IS NULL
                WHERE data.id_parent IS NULL AND data.deleted_at IS NULL
                
                UNION
                
                SELECT
                'P1' proses,
                4 sort,
                data.id_p1 id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                data.id_beam,
                data.id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                COALESCE(hilang.volume_1,0) hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) - COALESCE(hilang.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                data.code
                FROM tbl_p1_detail AS data
                LEFT JOIN tbl_p1 AS spk ON spk.id = data.id_p1
                LEFT JOIN tbl_p1_detail AS terima ON terima.id_parent = data.id AND terima.code = 'P1' AND terima.deleted_at IS NULL
                LEFT JOIN tbl_p1_detail AS hilang ON hilang.id_parent = data.id AND hilang.code = 'P1H' AND terima.deleted_at IS NULL
                WHERE data.id_parent IS NULL AND data.deleted_at IS NULL
                
                
                UNION
                
                SELECT
                'Finishing Cabut' proses,
                5 sort,
                data.id_finishing_cabut id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                data.id_beam,
                data.id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                COALESCE(hilang.volume_1,0) hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) - COALESCE(hilang.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                data.code
                FROM tbl_finishing_cabut_detail AS data
                LEFT JOIN tbl_finishing_cabut AS spk ON spk.id = data.id_finishing_cabut
                LEFT JOIN tbl_finishing_cabut_detail AS terima ON terima.id_parent = data.id AND terima.code = 'FC' AND terima.deleted_at IS NULL
                LEFT JOIN tbl_finishing_cabut_detail AS hilang ON hilang.id_parent = data.id AND hilang.code = 'FCH' AND terima.deleted_at IS NULL
                WHERE data.id_parent IS NULL AND data.deleted_at IS NULL
                
                UNION
                
                SELECT
                'P2' proses,
                6 sort,
                data.id_p2 id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                data.id_beam,
                data.id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                COALESCE(hilang.volume_1,0) hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) - COALESCE(hilang.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                data.code
                FROM tbl_p2_detail AS data
                LEFT JOIN tbl_p2 AS spk ON spk.id = data.id_p2
                LEFT JOIN tbl_p2_detail AS terima ON terima.id_parent = data.id AND terima.code = 'P2' AND terima.deleted_at IS NULL
                LEFT JOIN tbl_p2_detail AS hilang ON hilang.id_parent = data.id AND hilang.code = 'P2H' AND terima.deleted_at IS NULL
                WHERE data.id_parent IS NULL AND data.deleted_at IS NULL
                
                UNION
                
                SELECT
                'Jahit P2' proses,
                7 sort,
                data.id_jahit_p2 id_spk,
                spk.nomor,
                spk.id_supplier,
                data.tanggal,
                data.id_beam,
                data.id_mesin,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                COALESCE(data.volume_1,0) kirim_1,
                COALESCE(terima.volume_1,0) terima_1,
                COALESCE(hilang.volume_1,0) hilang_1,
                COALESCE(data.volume_1,0) - COALESCE(terima.volume_1,0) - COALESCE(hilang.volume_1,0) sisa_1,
                data.id_satuan_1,
                NULL kirim_2,
                NULL terima_2,
                NULL hilang_2,
                NULL sisa_2,
                data.id_satuan_2,
                data.code
                FROM tbl_jahit_p2_detail AS data
                LEFT JOIN tbl_jahit_p2 AS spk ON spk.id = data.id_jahit_p2
                LEFT JOIN tbl_jahit_p2_detail AS terima ON terima.id_parent = data.id AND terima.code = 'JP2' AND terima.deleted_at IS NULL
                LEFT JOIN tbl_jahit_p2_detail AS hilang ON hilang.id_parent = data.id AND hilang.code = 'JP2H' AND terima.deleted_at IS NULL
                WHERE data.id_parent IS NULL AND data.deleted_at IS NULL
                ) AS data
                LEFT JOIN tbl_supplier AS supplier ON supplier.id = data.id_supplier
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN tbl_gudang AS gudang ON gudang.id = data.id_gudang
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                LEFT JOIN tbl_kualitas AS grade ON grade.id = data.id_grade
                LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.id = data.id_kualitas
                LEFT JOIN tbl_satuan AS satuan_1 ON satuan_1.id = data.id_satuan_1
                LEFT JOIN tbl_satuan AS satuan_2 ON satuan_2.id = data.id_satuan_2
            ";
            $data = DB::table(DB::raw("({$sql}) as data"))
                ->where('sisa_1', '!=', 0)
                ->when($proses, function ($q) use ($proses) {
                    return $q->where('sort', $proses);
                })->when($barang, function ($q) use ($barang) {
                    return $q->where('id_barang', $barang);
                })->when($warna, function ($q) use ($warna) {
                    return $q->where('id_warna', $warna);
                })->when($motif, function ($q) use ($motif) {
                    return $q->where('id_motif', $motif);
                })->when($supplier, function ($q) use ($supplier) {
                    return $q->where('id_supplier', $supplier);
                })->orderBy('sort', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kirim_1', function ($i) {
                    return $i->kirim_1 . ' ' . $i->nama_satuan_1;
                })
                ->addColumn('terima_1', function ($i) {
                    return $i->terima_1 . ' ' . $i->nama_satuan_1;
                })
                ->addColumn('hilang_1', function ($i) {
                    return $i->hilang_1 . ' ' . $i->nama_satuan_1;
                })
                ->addColumn('sisa_1', function ($i) {
                    return $i->sisa_1 . ' ' . $i->nama_satuan_1;
                })
                ->make(true);
        }
    }
    function getBeam()
    {
        $data = TenunDetail::where([['code', 'BBTL']])
            ->where(function ($q) {
                $q->whereHas('relBeam', function ($q) {
                    $q->where('finish', 0);
                });
            })->get();
        return $data;
    }
    function getMesin($mode)
    {
        if ($mode == 'stok') {
            $data = Mesin::all();
        } else if ($mode == 'beam') {
            $temp = TenunDetail::where('code', 'BBTL')
                ->where(function ($q) {
                    $q->whereHas('relBeam', function ($q) {
                        $q->where('finish', 0);
                    });
                })->pluck('id_mesin')->toArray();
            $data = Mesin::whereIn('id', $temp)->get();
        } else {
            $data = Mesin::all();
        }
        return $data;
    }
    function getBarang($mode)
    {
        if ($mode == 'stok') {
            $data = Barang::all();
        } else if ($mode == 'beam') {
            $temp = TenunDetail::where('code', 'BBTL')
                ->where(function ($q) {
                    $q->whereHas('relBeam', function ($q) {
                        $q->where('finish', 0);
                    });
                })->pluck('id_barang')->toArray();
            $data = Barang::whereIn('id', $temp)->get();
        } else {
            $data = Barang::whereIn('id_tipe', [1, 7])->orderBy('id_tipe', 'desc')->get();
        }
        return $data;
    }
    function getGudang($mode)
    {
        if ($mode == 'stok') {
            $data = Gudang::all();
        } else if ($mode == 'beam') {
            $temp = TenunDetail::where('code', 'BBTL')
                ->where(function ($q) {
                    $q->whereHas('relBeam', function ($q) {
                        $q->where('finish', 0);
                    });
                })->pluck('id_gudang')->toArray();
            $data = Gudang::whereIn('id', $temp)->get();
        } else {
            $data = Gudang::all();
        }
        return $data;
    }
    function getWarna($mode)
    {
        if ($mode == 'stok') {
            $data = Warna::all();
        } else if ($mode == 'beam') {
            $temp = TenunDetail::where('code', 'BBTL')
                ->where(function ($q) {
                    $q->whereHas('relBeam', function ($q) {
                        $q->where('finish', 0);
                    });
                })->pluck('id_warna')->toArray();
            $data = Warna::whereIn('id', $temp)->get();
        } else {
            $data = Warna::all();
        }
        return $data;
    }
    function getMotif($mode)
    {
        if ($mode == 'stok') {
            $data = Motif::all();
        } else if ($mode == 'beam') {
            $temp = $temp = TenunDetail::where('code', 'BBTL')
                ->where(function ($q) {
                    $q->whereHas('relBeam', function ($q) {
                        $q->where('finish', 0);
                    });
                })->pluck('id_motif')->toArray();
            $data = Motif::whereIn('id', $temp)->get();
        } else {
            $data = Motif::all();
        }
        return $data;
    }
    function getSupplier()
    {
        $data = Supplier::all();
        return $data;
    }
}
