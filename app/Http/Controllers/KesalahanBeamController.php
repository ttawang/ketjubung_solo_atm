<?php

namespace App\Http\Controllers;

use App\Helpers\Date;
use App\Models\Barang;
use App\Models\Cucuk;
use App\Models\DudulanDetail;
use App\Models\InspectDudulanDetail;
use App\Models\InspectingGrey;
use App\Models\InspectingGreyDetail;
use App\Models\LogStokPenerimaan;
use App\Models\Mesin;
use App\Models\Motif;
use App\Models\NomorBeam;
use App\Models\NomorKikw;
use App\Models\PengirimanBarangDetail;
use App\Models\SizingDetail;
use App\Models\TenunDetail;
use App\Models\Tyeing;
use App\Models\Warna;
use App\Models\WarpingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Svg\Tag\Rect;
use Yajra\DataTables\Facades\DataTables;

class KesalahanBeamController extends Controller
{
    public function custom()
    {
        $this->findLog2(1);
        $data['breadcumbs'] = [['nama' => 'Developer', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Kesalahan Beam', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('', 'kesalahan beam', $data['breadcumbs'], true, false, true, true);
        return view('contents.custom.kesalahan-beam.index', $data);
    }
    public function getBeam(Request $request)
    {
        $term = $request->input('term');
        $parts = ($term == '') ? [] : explode(" | ", $term);
        $sql = "SELECT
            tenun.id_beam,
            no_beam.name no_beam,
            no_kikw.name no_kikw,
            tenun.id_mesin,
            mesin.name nama_mesin,
            tenun.id_barang,
            barang.name nama_barang,
            tenun.id_warna,
            warna.alias nama_warna,
            tenun.id_motif,
            motif.alias nama_motif,
            tenun.volume_2
            FROM tbl_tenun_detail AS tenun
            LEFT JOIN tbl_beam AS beam ON beam.id = tenun.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = tenun.id_mesin
            LEFT JOIN tbl_barang AS barang ON barang.id = tenun.id_barang
            LEFT JOIN tbl_warna AS warna ON warna.id = tenun.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = tenun.id_motif
            WHERE tenun.deleted_at IS NULL
            AND tenun.code = 'BBTL'
        ";
        $data = DB::table(DB::raw("({$sql}) as data"))
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('no_kikw', $parts)
                        ->orWhereIn('no_beam', $parts)
                        ->orWhereIn('nama_motif', $parts)
                        ->orWhereIn('nama_warna', $parts)
                        ->orWhereIn('nama_barang', $parts)
                        ->orWhereIn('nama_mesin', $parts);
                });
            })
            ->paginate(10);

        return $data;
    }
    public function getData(Request $request)
    {
        // dd($request->all());
        $id = $request->id_beam;
        $sql = "SELECT
            data.*,
            no_kikw.id id_no_kikw,
            no_kikw.name no_kikw,
            no_beam.id id_no_beam,
            no_beam.name no_beam,
            mesin.name nama_mesin,
            barang_lusi.name nama_barang_lusi,
            barang_sarung.name nama_barang_sarung,
            warna.alias nama_warna,
            motif.alias nama_motif
            FROM
            (
                SELECT
                tenun.id_beam,
                MAX(lusi.id_mesin) id_mesin,
                MAX(sarung.id_barang) id_barang_sarung,
                MAX(lusi.id_barang) id_barang_lusi,
                MAX(lusi.id_warna) id_warna,
                MAX(lusi.id_motif) id_motif,
                MAX(lusi.volume_2) jml
                FROM tbl_tenun AS tenun
                LEFT JOIN tbl_tenun_detail AS sarung ON sarung.id_tenun = tenun.id AND sarung.code = 'BG'
                LEFT JOIN tbl_tenun_detail AS lusi ON lusi.id_tenun = tenun.id AND lusi.code = 'BBTL'
                WHERE tenun.id_beam = $id
                AND tenun.deleted_at IS NULL
                GROUP BY tenun.id_beam
            ) AS data
            LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_barang AS barang_lusi ON barang_lusi.id = data.id_barang_lusi
            LEFT JOIN tbl_barang AS barang_sarung ON barang_sarung.id = data.id_barang_sarung
            LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
        ";
        $data = DB::table(DB::raw("({$sql}) as data"))->first();
        return $data;
    }
    public function getSelect(Request $request)
    {
        $term = $request->input('term');
        $tipe = $request->tipe;
        $data = null;
        if ($tipe == 'no_beam') {
            $data = NomorBeam::when($term, function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        if ($tipe == 'mesin') {
            $data = Mesin::when($term, function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        if ($tipe == 'barang_lusi') {
            $data = Barang::where('id_tipe', 3)->when($term, function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        if ($tipe == 'barang_sarung') {
            $data = Barang::where('id_tipe', 7)->when($term, function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        if ($tipe == 'warna') {
            $data = Warna::when($term, function ($q) use ($term) {
                $q - where('alias', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        if ($tipe == 'motif') {
            $data = Motif::when($term, function ($q) use ($term) {
                $q - where('alias', 'like', '%' . $term . '%');
            })->paginate(10);
        }
        return $data;
    }
    public function table(Request $request)
    {
        $id_beam = $request->id_beam ?? 0;
        $sql = "SELECT
            gudang.name nama_gudang,
            barang.name nama_barang,
            warna.alias nama_warna,
            motif.alias nama_motif,
            grade.grade nama_grade,
            mesin.name nama_mesin,
            satuan_1.name nama_satuan_1,
            satuan_2.name nama_satuan_2,
            code.nama nama_code,
            log.*
            FROM log_stok_penerimaan AS log
            LEFT JOIN tbl_gudang AS gudang ON gudang.id = log.id_gudang
            LEFT JOIN tbl_barang AS barang ON barang.id = log.id_barang
            LEFT JOIN tbl_warna AS warna ON warna.id = log.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = log.id_motif
            LEFT JOIN tbl_kualitas AS grade ON grade.id = log.id_grade
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = log.id_mesin
            LEFT JOIN tbl_satuan AS satuan_1 ON satuan_1.id = log.id_satuan_1
            LEFT JOIN tbl_satuan AS satuan_2 ON satuan_2.id = log.id_satuan_2
            LEFT JOIN production_code AS code ON code.code = log.code
            WHERE log.deleted_at IS NULL
            AND log.code NOT IN ('DPS', 'DPST', 'DPR', 'DPRT')
            AND log.id_beam = $id_beam
            ORDER BY log.id
        ";
        $data = DB::table(DB::raw("({$sql}) as data"));
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('gudang', function ($i) {
                return $i->nama_gudang;
            })
            ->addColumn('barang', function ($i) {
                return $i->nama_barang . ' | ' . $i->nama_warna . ' | ' . $i->nama_motif;
            })
            ->addColumn('mesin', function ($i) {
                return $i->nama_mesin;
            })
            ->addColumn('grade', function ($i) {
                return $i->nama_grade;
            })
            ->addColumn('code', function ($i) {
                return $i->code;
            })
            ->addColumn('masuk', function ($i) {
                $temp = ['BL', 'BBTL'];
                if (in_array($i->code, $temp)) {
                    return $i->volume_masuk_2 ?? 0;
                } else {
                    return $i->volume_masuk_1 ?? 0;
                }
            })
            ->addColumn('keluar', function ($i) {
                $temp = ['BL', 'BBTL'];
                if (in_array($i->code, $temp)) {
                    return $i->volume_keluar_2 ?? 0;
                } else {
                    return $i->volume_keluar_1 ?? 0;
                }
            })
            ->addColumn('text_status', function ($i) {
                // $data = $this->findLog($i->code, $i->id);
                $data = $this->findLog2($i->id);
                if ($data['status'] == 'YES') {
                    return '<span class="badge badge-outline badge-success"> ' . $data['status'] . '</span> ' . $data['proses'];
                } else {
                    return '<span class="badge badge-outline badge-danger"> ' . $data['status'] . '</span> ';
                }
            })
            ->addColumn('status', function ($i) {
                // $data = $this->findLog($i->code, $i->id);
                $data = $this->findLog2($i->id);
                return $data['status'];
            })
            ->rawColumns(['text_status'])
            ->make('true');
    }
    function findLog2($id)
    {
        $data = null;
        $logName = ['id_log_stok', 'id_log_stok_penerimaan', 'id_log_stok_penerimaan_keluar', 'id_log_stok_penerimaan_masuk', 'id_log_stok_keluar', 'id_log_stok_masuk'];
        foreach ($logName as $i) {
            $sqlTable = "SELECT table_name
                FROM information_schema.columns
                WHERE column_name = '{$i}'
            ";
            $dataTable = DB::table(DB::raw("({$sqlTable}) as data"))->get();
            foreach ($dataTable as $j) {
                $temp = DB::table("$j->table_name")->where("$i", $id)->first();
                if ($temp) {
                    $exTable = ['tbl_sizing_detail', 'tbl_tyeing', 'tbl_cucuk'];
                    $data['status'] = 'YES';
                    $data['proses'] = ucfirst(str_replace('_', '', str_replace('detail', '', str_replace('tbl', '', $j->table_name))));
                    $data['table'] = (!in_array($j->table_name, $exTable)) ? $j->table_name : null;
                    $data['column_log'] = $i;
                    return $data;
                }
            }
        }
        if (!$data) {
            $data['status'] = 'NO';
            $data['proses'] = null;
            $data['table'] = null;
            $data['column_log'] = null;
        }
        return $data;
    }
    function findLog($code, $id)
    {
        $temp = null;
        $proses = null;
        $table = null;
        if ($code == 'BL') {
            $temp = WarpingDetail::where('id_log_stok_penerimaan', $id)->first();
            $proses = 'Warping';
            $table = 'warping_detail';
            $columnLog = 'id_log_stok_penerimaan';
            if (!$temp) {
                $temp = SizingDetail::where('id_log_stok_penerimaan', $id)->first();
                $proses = 'Sizing Masuk';
                $table = null;
                $columnLog = 'id_log_stok_penerimaan';
            }
            if (!$temp) {
                $temp = Tyeing::where('id_log_stok_keluar', $id)->first();
                $proses = 'Tyeing Keluar';
                $table = null;
                $columnLog = 'id_log_stok_keluar';
            }
            if (!$temp) {
                $temp = Tyeing::where('id_log_stok_masuk', $id)->first();
                $proses = 'Tyeing Masuk';
                $table = null;
                $columnLog = 'id_log_stok_masuk';
            }
            if (!$temp) {
                $temp = Cucuk::where('id_log_stok_keluar', $id)->first();
                $proses = 'Cucuk Keluar';
                $table = null;
                $columnLog = 'id_log_stok_keluar';
            }
            if (!$temp) {
                $temp = Cucuk::where('id_log_stok_masuk', $id)->first();
                $proses = 'Cucuk Masuk';
                $table = null;
                $columnLog = 'id_log_stok_masuk';
            }
            if (!$temp) {
                $temp = PengirimanBarangDetail::where('id_log_stok', $id)->first();
                $proses = 'Pengiriman Keluar';
                $table = 'pengiriman_barang_detail';
                $columnLog = 'id_log_stok';
            }
        }
        if ($code == 'BBTL') {
            $temp = PengirimanBarangDetail::where('id_log_stok', $id)->first();
            $proses = 'Pengiriman Masuk';
            $table = 'pengiriman_barang_detail';
            $columnLog = 'id_log_stok';
            if (!$temp) {
                $temp = TenunDetail::where('id_log_stok_penerimaan', $id)->first();
                $proses = 'Tenun';
                $table = 'tenun_detail';
                $columnLog = 'id_log_stok_penerimaan';
            }
        }
        if ($code == 'BG') {
            $temp = TenunDetail::where('id_log_stok_penerimaan', $id)->first();
            $proses = 'Tenun Potong Sarung';
            $table = 'tenun_detail';
            $columnLog = 'id_log_stok_penerimaan';
            if (!$temp) {
                $temp = PengirimanBarangDetail::where('id_log_stok', $id)->first();
                $proses = 'Pengiriman Keluar';
                $table = 'pengiriman_barang_detail';
                $columnLog = 'id_log_stok';
            }
        }
        if ($code == 'BGIG') {
            $temp = InspectingGreyDetail::where('id_log_stok_penerimaan', $id)->first();
            $proses = 'Inspecting Grey';
            $table = 'inspecting_grey_detail';
            $columnLog = 'id_log_stok_penerimaan';
            if (!$temp) {
                $temp = InspectingGrey::where('id_log_stok_penerimaan_keluar', $id)->first();
                $proses = 'Inspecting Grey Keluar';
                $table = 'inspecting_grey';
                $columnLog = 'id_log_stok_penerimaan_keluar';
            }
            if (!$temp) {
                $temp = InspectingGrey::where('id_log_stok_penerimaan_masuk', $id)->first();
                $proses = 'Inspecting Grey Masuk';
                $table = 'inspecting_grey';
                $columnLog = 'id_log_stok_penerimaan_masuk';
            }
            if (!$temp) {
                $temp = DudulanDetail::where('id_log_stok_penerimaan', $id)->first();
                $proses = 'Dudulan';
                $table = 'dudulan_detail';
                $columnLog = 'id_log_stok_penerimaan';
            }
        }
        if ($code == 'BGD') {
            $temp = DudulanDetail::where('id_log_stok_penerimaan', $id)->first();
            $proses = 'Dudulan';
            $table = 'dudulan_detail';
            $columnLog = 'id_log_stok_penerimaan';
            if (!$temp) {
                $temp = InspectDudulanDetail::where('id_log_stok_penerimaan_keluar', $id)->first();
                $proses = 'Inspect Dudulan';
                $table = 'inspect_dudulan_detail';
                $columnLog = 'id_log_stok_penerimaan_keluar';
            }
        }
        if ($code == 'BGID') {
            $temp = InspectDudulanDetail::where('id_log_stok_penerimaan_masuk', $id)->first();
            $proses = 'Inspect Dudulan';
            $table = 'inspect_dudulan_detail';
            $columnLog = 'id_log_stok_penerimaan_masuk';
        }
        if ($code == 'BBG') {
            $temp = PengirimanBarangDetail::where('id_log_stok', $id)->first();
            $proses = 'Pengiriman Barang';
            $table = 'pengiriman_barang_detail';
            $columnLog = 'id_log_stok';
            if (!$temp) {
                $temp = InspectingGrey::where('id_log_stok_penerimaan_keluar', $id)->first();
                $proses = 'Inspect Grey';
                $table = 'inspecting_grey';
                $columnLog = 'id_log_stok_penerimaan_keluar';
            }
        }

        if ($temp) {
            $data['status'] = 'YES';
            $data['proses'] = $proses;
            $data['table'] = $table;
            $data['column_log'] = $columnLog;
        } else {
            $data['status'] = 'NO';
            $data['proses'] = $proses;
            $data['table'] = null;
            $data['column_log'] = null;
        }

        return $data;
    }
    public function simpan(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $sql = "SELECT
                    gudang.name nama_gudang,
                    barang.name nama_barang,
                    warna.alias nama_warna,
                    motif.alias nama_motif,
                    grade.grade nama_grade,
                    mesin.name nama_mesin,
                    satuan_1.name nama_satuan_1,
                    satuan_2.name nama_satuan_2,
                    code.nama nama_code,
                    log.*
                    FROM log_stok_penerimaan AS log
                    LEFT JOIN tbl_gudang AS gudang ON gudang.id = log.id_gudang
                    LEFT JOIN tbl_barang AS barang ON barang.id = log.id_barang
                    LEFT JOIN tbl_warna AS warna ON warna.id = log.id_warna
                    LEFT JOIN tbl_motif AS motif ON motif.id = log.id_motif
                    LEFT JOIN tbl_kualitas AS grade ON grade.id = log.id_grade
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = log.id_mesin
                    LEFT JOIN tbl_satuan AS satuan_1 ON satuan_1.id = log.id_satuan_1
                    LEFT JOIN tbl_satuan AS satuan_2 ON satuan_2.id = log.id_satuan_2
                    LEFT JOIN production_code AS code ON code.code = log.code
                    WHERE log.deleted_at IS NULL
                    AND log.code NOT IN ('DPS', 'DPST', 'DPR', 'DPRT')
                    AND log.id_beam = $request->id_beam
                    ORDER BY log.id
                ";
                $data = DB::table(DB::raw("({$sql}) as data"))->get();
                if ($request->no_kikw_lama !== $request->no_kikw_baru) {
                    NomorKikw::find($request->id_no_kikw_lama)->update(['name' => $request->no_kikw_baru]);
                }
                if ($request->id_no_beam_lama !== $request->no_beam_baru) {
                    return $this->jsonResponse(false, ['gagal' => ['perubahan no_beam, fungsi belum di berikan']]);
                }
                if ($request->id_mesin_lama !== $request->mesin_baru) {
                    return $this->jsonResponse(false, ['gagal' => ['perubahan mesin, fungsi belum di berikan']]);
                }
                if ($request->id_barang_lusi_lama !== $request->barang_lusi_baru) {
                    $dataBeam = DB::table(DB::raw("({$sql}) as data"))->whereIn('code', ['BL', 'BBTL', 'BBTLR', 'BBTLT'])->get();
                    foreach ($dataBeam as $i) {
                        $temp = $this->findLog2($i->id);
                        if ($temp['table']) {
                            DB::table($temp['table'])->where($temp['column_log'], $i->id)->update(['id_barang' => $request->barang_lusi_baru]);
                        }
                        LogStokPenerimaan::find($i->id)->update(['id_barang' => $request->barang_lusi_baru]);
                    }
                }
                if ($request->id_barang_sarung_lama !== $request->barang_sarung_baru) {
                    $dataSarung = DB::table(DB::raw("({$sql}) as data"))->whereNotIn('code', ['BL', 'BBTL', 'BBTLR', 'BBTLT'])->get();
                    foreach ($dataSarung as $i) {
                        $temp = $this->findLog2($i->id);
                        if ($temp['table']) {
                            DB::table($temp['table'])->where($temp['column_log'], $i->id)->update(['id_barang' => $request->barang_sarung_baru]);
                        }
                        LogStokPenerimaan::find($i->id)->update(['id_barang' => $request->barang_sarung_baru]);
                    }
                }
                if ($request->id_warna_lama !== $request->warna_baru) {
                    foreach ($data as $i) {
                        // $temp = $this->findLog($i->code, $i->id);
                        $temp = $this->findLog2($i->id);
                        if ($temp['table']) {
                            DB::table('tbl_' . $temp['table'])->where($temp['column_log'], $i->id)->update(['id_warna' => $request->warna_baru]);
                        }
                        LogStokPenerimaan::find($i->id)->update(['id_warna' => $request->warna_baru]);
                    }
                }
                if ($request->id_motif_lama !== $request->motif_baru) {
                    foreach ($data as $i) {
                        // $temp = $this->findLog($i->code, $i->id);
                        $temp = $this->findLog2($i->id);
                        if ($temp['table']) {
                            DB::table('tbl_' . $temp['table'])->where($temp['column_log'], $i->id)->update(['id_motif' => $request->motif_baru]);
                        }
                        LogStokPenerimaan::find($i->id)->update(['id_motif' => $request->motif_baru]);
                    }
                }
                if ($request->volume_lama !== $request->volume_baru) {
                    return $this->jsonResponse(false, ['gagal' => ['perubahan volume, fungsi belum di berikan']]);
                }
                if (
                    $request->no_kikw_lama === $request->no_kikw_baru &&
                    $request->id_no_beam_lama === $request->no_beam_baru &&
                    $request->id_mesin_lama === $request->mesin_baru &&
                    $request->id_barang_lusi_lama === $request->barang_lusi_baru &&
                    $request->id_barang_sarung_lama === $request->barang_sarung_baru &&
                    $request->id_warna_lama === $request->warna_baru &&
                    $request->id_motif_lama === $request->motif_baru &&
                    $request->volume_lama === $request->volume_baru
                ) {
                    return $this->jsonResponse(true, 'Data tidak dirubah');
                }

                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        }, 5);
    }
    private function jsonResponse($success, $message, $status = 200)
    {
        return response()->json(['success' => $success, 'messages' => $message], $status);
    }
    function cekRequest($request)
    {
        $rules = [
            'no_kikw_baru' => Rule::unique('tbl_nomor_kikw', 'name')->whereNull('deleted_at')->ignore($request->id_no_kikw_lama),
            'no_beam_baru' => 'required',
            'mesin_baru' => 'required',
            'barang_lusi_baru' => 'required',
            'barang_sarung_baru' => 'required',
            'warna_baru' => 'required',
            'motif_baru' => 'required',
            'volume_baru' => 'required|numeric|gt:0|not_in:0',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['success'] = false;
            $data['messages'] = $validator->getMessageBag()->toArray();
        } else {
            $data['success'] = true;
            $data['messages'] = '';
        }
        return $data;
    }
}
