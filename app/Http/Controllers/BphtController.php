<?php

namespace App\Http\Controllers;

use App\Helpers\Date;
use App\Models\Barang;
use App\Models\LogStokPenerimaan;
use App\Models\PengirimanBarang;
use App\Models\PengirimanBarangDetail;
use App\Models\TenunDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;
use Yajra\DataTables\DataTables;

class BphtController extends Controller
{
    public function detail()
    {
        $id = request('id'); /* id ppengiriman barang detail */
        $data['data'] = PengirimanBarang::whereId($id)->withSum(['relPengirimanDetail as total' => function ($q) {
            $q->where('status', 'ASAL');
        }], 'volume_1')->first();
        return view('contents.production.pengiriman_barang.detail-bpht', $data);
    }
    public function table(Request $request)
    {
        $id = $request->id_pengiriman_barang;
        $sql = "SELECT
            (
                SELECT
                pa.jumlah_beam - SUM(de.volume_1)
                FROM tbl_tenun_detail AS de
                LEFT JOIN tbl_tenun AS pa ON pa.id = de.id_tenun
                WHERE pa.id_beam = data.id_beam
                AND de.code = 'BG'
                AND de.deleted_at IS NULL
                AND (de.id_pengiriman_barang_detail <= data.id OR de.id_pengiriman_barang_detail IS NULL)
                GROUP BY pa.jumlah_beam
            ) sisa_beam,
            parent.validated_at,
            barang.name nama_barang,
            warna.alias nama_warna,
            motif.alias nama_motif,
            gudang.name nama_gudang,
            mesin.name nama_mesin,
            no_kikw.name no_kikw,
            no_beam.name no_beam,
            no_kiks.name no_kiks,
            data.*
            FROM tbl_pengiriman_barang_detail AS data
            LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = data.id_pengiriman_barang
            LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_beam AS songket ON songket.id = data.id_songket
            LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
            LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
            LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
            LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_gudang AS gudang ON gudang.id = data.id_gudang
            WHERE data.status = 'ASAL'
            AND data.deleted_at IS NULL
            AND data.id_pengiriman_barang = $id
        ";
        $data = DB::table(DB::raw("({$sql}) as data"));
        if (!empty($request->search['value'])) {
            $term = $request->search['value'];
            $data->where(function ($q) use ($term) {
                $q->where('no_kikw', 'like', '%' . $term . '%')
                    ->orwhere('no_beam', 'like', '%' . $term . '%')
                    ->orwhere('no_kiks', 'like', '%' . $term . '%')
                    ->orwhere('nama_motif', 'like', '%' . $term . '%')
                    ->orwhere('nama_warna', 'like', '%' . $term . '%')
                    ->orwhere('nama_barang', 'like', '%' . $term . '%')
                    ->orwhere('nama_gudang', 'like', '%' . $term . '%')
                    ->orwhere('nama_mesin', 'like', '%' . $term . '%');
            });
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('barang', function ($i) {
                return $i->nama_barang;
            })
            ->addColumn('warna', function ($i) {
                return $i->nama_warna;
            })
            ->addColumn('motif', function ($i) {
                return $i->nama_motif;
            })
            ->addColumn('gudang', function ($i) {
                return $i->nama_gudang;
            })
            ->addColumn('mesin', function ($i) {
                return $i->nama_mesin;
            })
            ->addColumn('no_kikw', function ($i) {
                return $i->no_kikw;
            })
            ->addColumn('no_kiks', function ($i) {
                return $i->no_kiks;
            })
            ->addColumn('jml', function ($i) {
                return $i->volume_1;
            })
            ->addColumn('catatan', function ($i) {
                return $i->catatan;
            })
            ->addColumn('sisa_beam', function ($i) {
                return $i->sisa_beam;
            })
            ->addColumn('action', function ($i) {
                $validated = $i->validated_at;
                $validasi = [
                    'status' => false,
                    'data' => $validated,
                    'model' => 'PengirimanBarang'
                ];
                $terima = PengirimanBarangDetail::where('id_parent_detail', $i->id)->first();
                if ($terima) {
                    $action = '<span class="badge badge-outline badge-success">Diterima</span>';
                } else {
                    $action = actionBtn($i->id, false, true, true, $validasi);
                }
                return $action;
            })
            ->rawColumns(['action'])
            ->make('true');
    }
    public function getBeam(Request $request)
    {
        $term = $request->input('term');
        $parts = ($term == '') ? [] : explode(" | ", $term);
        $sql = "SELECT
            data.*,
            no_kikw.name no_kikw,
            no_beam.name no_beam,
            gudang.name nama_gudang,
            warna.alias nama_warna,
            motif.alias nama_motif,
            mesin.name nama_mesin,
            volume_2 - potong total
            FROM
            (
                SELECT
                td.id id_tenun_detail,
                td.id_tenun,
                td.id_beam,
                beam.id_nomor_beam,
                beam.id_nomor_kikw,
                td.id_gudang,
                td.id_barang,
                barang.name nama_barang,
                potong.id_barang id_barang_benang,
                potong.nama_barang nama_barang_benang,
                td.id_warna,
                td.id_motif,
                td.id_mesin,
                td.id_satuan_1,
                SUM(COALESCE(td.volume_1,0)) volume_1,
                td.id_satuan_2,
                SUM(COALESCE(td.volume_2,0)) volume_2,
                SUM(COALESCE(potong.potong,0)) potong
                FROM tbl_tenun_detail AS td
                LEFT JOIN tbl_beam AS beam ON beam.id = td.id_beam
                LEFT JOIN tbl_barang AS barang ON barang.id = td.id_barang
                LEFT JOIN
                (
                    SELECT
                    td.id_tenun,
                    td.id_beam,
                    td.id_gudang,
                    td.id_barang,
                    barang.name nama_barang,
                    td.id_warna,
                    td.id_motif,
                    td.id_mesin,
                    SUM(COALESCE(td.volume_1,0)) potong
                    FROM tbl_tenun_detail AS td
                    LEFT JOIN tbl_beam AS beam ON beam.id = td.id_beam
                    LEFT JOIN tbl_barang AS barang ON barang.id = td.id_barang
                    WHERE td.deleted_at IS NULL AND td.code = 'BG' AND beam.finish = 0
                    GROUP BY td.id_tenun, td.id_beam, td.id_gudang, td.id_barang, barang.name, td.id_warna, td.id_motif, td.id_mesin, td.id_satuan_1, td.id_satuan_2
                ) AS potong ON potong.id_tenun = td.id_tenun AND potong.id_beam = td.id_beam AND potong.id_gudang = td.id_gudang AND potong.nama_barang = barang.name AND potong.id_warna = td.id_warna AND potong.id_motif = td.id_motif AND potong.id_mesin = td.id_mesin
                WHERE td.deleted_at IS NULL AND td.code = 'BBTL' AND beam.finish = 0
                GROUP BY td.id_tenun, td.id_beam, beam.id_nomor_beam, beam.id_nomor_kikw, td.id_gudang, td.id_barang, barang.name, potong.id_barang, td.id_warna, td.id_motif, td.id_mesin, td.id_satuan_1, td.id_satuan_2, potong.nama_barang, td.id
            ) AS data
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = data.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = data.id_nomor_kikw
            LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
            LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_gudang AS gudang ON gudang.id = data.id_gudang
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
            ->orderBy('id_tenun', 'desc')->paginate(5);
        return $data;
    }
    public function getSongket(Request $request)
    {
        $term = $request->input('term');
        $parts = ($term == '') ? [] : explode(" | ", $term);
        $id_tenun = $request->id_tenun ?? null;
        $sql = "SELECT
            td.id_tenun_detail,
            td.id_tenun,
            td.id_beam,
            no_kikw.name no_kikw,
            td.id_gudang,
            gudang.name nama_gudang,
            td.id_barang,
            td.nama_barang,
            td.id_warna,
            warna.alias nama_warna,
            td.id_motif,
            motif.alias nama_motif,
            td.id_mesin,
            mesin.name nama_mesin,
            COALESCE(td.jml,0) jml,
            COALESCE(potong.jml,0) potong,
            COALESCE(td.jml,0) - COALESCE(potong.jml,0) total
            FROM
            (
                SELECT
                d.id id_tenun_detail,
                d.id_tenun,
                d.id_beam,
                d.id_songket,
                d.id_gudang,
                d.id_barang,
                barang.name nama_barang,
                d.id_warna,
                d.id_motif,
                d.id_mesin,
                COALESCE(d.volume_2,0) jml
                FROM tbl_tenun_detail AS d
                LEFT JOIN tbl_beam AS beam ON beam.id = d.id_beam
                LEFT JOIN tbl_barang AS barang ON barang.id = d.id_barang
                WHERE d.deleted_at IS NULL
                AND d.code = 'BBTS'
            ) AS td
            LEFT JOIN
            (
                SELECT
                d.id_tenun,
                d.id_beam,
                d.id_songket,
                d.id_gudang,
                d.id_barang,
                barang.name nama_barang,
                d.id_warna,
                d.id_motif,
                d.id_mesin,
                SUM(COALESCE(d.volume_1,0)) jml
                FROM tbl_tenun_detail AS d
                LEFT JOIN tbl_barang AS barang ON barang.id = d.id_barang
                WHERE d.deleted_at IS NULL AND d.code = 'BG'
                GROUP BY d.id_tenun, d.id_beam, d.id_songket, d.id_gudang, d.id_barang, d.id_warna, d.id_motif, d.id_mesin, barang.name
            ) AS potong 
            ON potong.id_tenun = td.id_tenun AND
            potong.id_songket = td.id_beam AND
            potong.id_gudang = td.id_gudang AND
            potong.id_mesin = td.id_mesin
            LEFT JOIN tbl_beam AS beam ON beam.id = td.id_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_motif AS motif ON motif.id = td.id_motif
            LEFT JOIN tbl_warna AS warna ON warna.id = td.id_warna
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = td.id_mesin
            LEFT JOIN tbl_gudang AS gudang ON gudang.id = td.id_gudang
        ";
        $data = DB::table(DB::raw("({$sql}) as data"))
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('no_kikw', $parts)
                        ->orWhereIn('nama_motif', $parts)
                        ->orWhereIn('nama_warna', $parts)
                        ->orWhereIn('nama_barang', $parts)
                        ->orWhereIn('nama_mesin', $parts);
                });
            })->where('id_tenun', $id_tenun)->orderBy('id_tenun_detail', 'desc')->paginate(5);
        return $data;
    }
    public function getBarang(Request $request)
    {
        $term = $request->input('term');
        $nama_barang = $request->nama_barang;
        $data = Barang::where('id_tipe', 7)->where('name', $nama_barang)
            ->when(!empty($term), function ($q) use ($term) {
                $q->where('name', $term);
            })->paginate(5);
        return $data;
    }
    public function getData($id)
    {
        $sql = "SELECT
            data.id_pengiriman_barang_detail,
            data.id_pengiriman_barang,
            data.tanggal,
            data.id_tenun,
            data.id_songket_sarung,
            data.id_gudang,
            data.id_tenun_detail_sarung,
            data.id_tenun_detail_beam,
            data.id_tenun_detail_songket,
            data.id_barang_sarung,
            data.id_barang_beam,
            data.id_barang_songket,
            barang_sarung.name nama_barang_sarung,
            barang_beam.name nama_barang_beam,
            barang_songket.name nama_barang_songket,
            data.id_beam_sarung,
            data.id_beam_beam,
            data.id_beam_songket,
            no_beam_sarung.name no_beam_sarung,
            no_beam_beam.name no_beam_beam,
            no_beam_songket.name no_beam_songket,
            no_kikw_sarung.name no_kikw_sarung,
            no_kikw_beam.name no_kikw_beam,
            no_kikw_songket.name no_kikw_songket,
            data.id_warna_sarung,
            data.id_warna_beam,
            data.id_warna_songket,
            warna_sarung.alias nama_warna_sarung,
            warna_beam.alias nama_warna_beam,
            warna_songket.alias nama_warna_songket,
            data.id_motif_sarung,
            data.id_motif_beam,
            data.id_motif_songket,
            motif_sarung.alias nama_motif_sarung,
            motif_beam.alias nama_motif_beam,
            motif_songket.alias nama_motif_songket,
            data.id_mesin_sarung,
            data.id_mesin_beam,
            data.id_mesin_songket,
            mesin_sarung.name nama_mesin_sarung,
            mesin_beam.name nama_mesin_beam,
            mesin_songket.name nama_mesin_songket,
            data.jml_sarung,
            data.jml_beam,
            data.jml_songket,
            data.jml_beam + jml_sarung - COALESCE(potong_beam.jml) total_beam,
            data.jml_songket + jml_sarung - COALESCE(potong_songket.jml) total_songket
            FROM
            (
                SELECT
                pengiriman_detail.id id_pengiriman_barang_detail,
                pengiriman_detail.id_pengiriman_barang,
                pengiriman_detail.tanggal,
                pengiriman_detail.volume_1 jml_sarung,
                pengiriman_detail.id_gudang,
                sarung.id_tenun,
                sarung.id id_tenun_detail_sarung,
                sarung.id_lusi_detail id_tenun_detail_beam,
                sarung.id_songket_detail id_tenun_detail_songket,
                sarung.id_beam id_beam_sarung,
                sarung.id_songket id_songket_sarung,
                sarung.id_barang id_barang_sarung,
                beam.id_barang id_barang_beam,
                songket.id_barang id_barang_songket,
                beam.id_beam id_beam_beam,
                songket.id_beam id_beam_songket,
                sarung.id_warna id_warna_sarung,
                beam.id_warna id_warna_beam,
                songket.id_warna id_warna_songket,
                sarung.id_motif id_motif_sarung,
                beam.id_motif id_motif_beam,
                songket.id_motif id_motif_songket,
                sarung.id_mesin id_mesin_sarung,
                beam.id_mesin id_mesin_beam,
                songket.id_mesin id_mesin_songket,
                beam.volume_2 jml_beam,
                songket.volume_2 jml_songket
                FROM
                tbl_pengiriman_barang_detail AS pengiriman_detail
                LEFT JOIN tbl_tenun_detail AS sarung ON sarung.id_pengiriman_barang_detail = pengiriman_detail.id
                LEFT JOIN tbl_tenun_detail AS beam ON beam.id = sarung.id_lusi_detail
                LEFT JOIN tbl_tenun_detail AS songket ON songket.id = sarung.id_songket_detail
                WHERE pengiriman_detail.id = $id
            ) AS data
            LEFT JOIN
            (
                SELECT
                id_tenun,
                id_beam,
                id_gudang,
                id_barang,
                id_warna,
                id_motif,
                id_mesin,
                SUM(COALESCE(volume_1,0)) jml
                FROM tbl_tenun_detail
                WHERE deleted_at IS NULL AND code = 'BG'
                GROUP BY id_tenun, id_beam, id_gudang, id_barang, id_warna, id_motif, id_mesin
            ) AS potong_beam ON potong_beam.id_tenun = data.id_tenun AND potong_beam.id_gudang = data.id_gudang AND potong_beam.id_beam = data.id_beam_beam AND potong_beam.id_barang = data.id_barang_sarung AND potong_beam.id_warna = data.id_warna_sarung AND potong_beam.id_motif = data.id_motif_sarung AND potong_beam.id_mesin = data.id_mesin_sarung
            LEFT JOIN
            (
                SELECT
                id_tenun,
                id_beam,
                id_songket,
                id_gudang,
                id_barang,
                id_warna,
                id_motif,
                id_mesin,
                SUM(COALESCE(volume_1,0)) jml
                FROM tbl_tenun_detail
                WHERE deleted_at IS NULL AND code = 'BG'
                GROUP BY id_tenun, id_beam, id_gudang, id_barang, id_warna, id_motif, id_mesin, id_songket
            ) AS potong_songket ON potong_songket.id_tenun = data.id_tenun AND potong_songket.id_gudang = data.id_gudang AND potong_songket.id_beam = data.id_beam_beam AND potong_songket.id_barang = data.id_barang_sarung AND potong_songket.id_warna = data.id_warna_sarung AND potong_songket.id_motif = data.id_motif_sarung AND potong_songket.id_mesin = data.id_mesin_sarung AND potong_songket.id_songket = data.id_beam_songket
            LEFT JOIN tbl_barang AS barang_sarung ON barang_sarung.id = data.id_barang_sarung
            LEFT JOIN tbl_barang AS barang_beam ON barang_beam.id = data.id_barang_beam
            LEFT JOIN tbl_barang AS barang_songket ON barang_songket.id = data.id_barang_songket
            LEFT JOIN tbl_warna AS warna_sarung ON warna_sarung.id = data.id_warna_sarung
            LEFT JOIN tbl_warna AS warna_beam ON warna_beam.id = data.id_warna_beam
            LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = data.id_warna_songket
            LEFT JOIN tbl_motif AS motif_sarung ON motif_sarung.id = data.id_motif_sarung
            LEFT JOIN tbl_motif AS motif_beam ON motif_beam.id = data.id_motif_beam
            LEFT JOIN tbl_motif AS motif_songket ON motif_songket.id = data.id_motif_songket
            LEFT JOIN tbl_mesin AS mesin_sarung ON mesin_sarung.id = data.id_mesin_sarung
            LEFT JOIN tbl_mesin AS mesin_beam ON mesin_beam.id = data.id_mesin_beam
            LEFT JOIN tbl_mesin AS mesin_songket ON mesin_songket.id = data.id_mesin_songket
            LEFT JOIN tbl_beam AS beam_sarung ON beam_sarung.id = data.id_beam_sarung
            LEFT JOIN tbl_beam AS beam_beam ON beam_beam.id = data.id_beam_beam
            LEFT JOIN tbl_beam AS beam_songket ON beam_songket.id = data.id_beam_songket
            LEFT JOIN tbl_nomor_beam AS no_beam_sarung ON no_beam_sarung.id = beam_sarung.id_nomor_beam
            LEFT JOIN tbl_nomor_beam AS no_beam_beam ON no_beam_beam.id = beam_beam.id_nomor_beam
            LEFT JOIN tbl_nomor_beam AS no_beam_songket ON no_beam_songket.id = beam_songket.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw_sarung ON no_kikw_sarung.id = beam_sarung.id_nomor_kikw
            LEFT JOIN tbl_nomor_kikw AS no_kikw_beam ON no_kikw_beam.id = beam_beam.id_nomor_kikw
            LEFT JOIN tbl_nomor_kikw AS no_kikw_songket ON no_kikw_songket.id = beam_songket.id_nomor_kikw        
        ";
        $data = DB::table(DB::raw("({$sql}) as data"))->first();
        return $data;
    }
    public function simpan(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $beam = $request->beam;
                if ($request->songket) {
                    $songket = $request->songket;
                } else {
                    $songket = [
                        'id_beam' => null,
                        'id_tenun_detail' => null,
                    ];
                }

                $log = [
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_beam' => $beam['id_beam'],
                    'id_mesin' => $beam['id_mesin'],
                    'volume_masuk_1' => $request['volume_masuk_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'id_songket' => $songket['id_beam'],
                ];

                /* pengiriman barang */
                $logKeluar = $log;
                $logKeluar['code'] = 'BG';
                $logKeluar['volume_masuk_1'] = 0;
                $logKeluar['volume_keluar_1'] = $request['volume_1'];
                $logKeluarId = LogStokPenerimaan::create($logKeluar)->id;

                $pengiriman = [
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_pengiriman_barang' => $request['id_pengiriman_barang'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_beam' => $beam['id_beam'],
                    'id_mesin' => $beam['id_mesin'],
                    'id_log_stok' => $logKeluarId,
                    'volume_1' => $request['volume_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'status' => 'ASAL',
                    'id_songket' => $songket['id_beam'],
                ];
                $pengirimanBarangDetailId = PengirimanBarangDetail::create($pengiriman)->id;
                /* end pengiriman barang */

                /* tenun */
                $logTenun = $log;
                $logTenun['code'] = 'BG';
                $logTenun['volume_masuk_1'] = $request['volume_1'];
                $logTenunId = LogStokPenerimaan::create($logTenun)->id;

                $tenun = [
                    'id_tenun' => $beam['id_tenun'],
                    'id_beam' => $beam['id_beam'],
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_mesin' => $beam['id_mesin'],
                    'id_log_stok_penerimaan' => $logTenunId,
                    'volume_1' => $request['volume_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'code' => 'BG',
                    'id_songket_detail' => $songket['id_tenun_detail'],
                    'id_lusi_detail' => $beam['id_tenun_detail'],
                    'id_songket' => $songket['id_beam'],
                    'id_pengiriman_barang_detail' => $pengirimanBarangDetailId,
                ];
                TenunDetail::create($tenun);
                /* end tenun */

                logHistory('PengirimanBarangDetail', 'create');
                logHistory('TenunDetail', 'create');

                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        }, 5);
    }
    public function update(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $id = $request->id;
                $dataPengiriman = PengirimanBarangDetail::find($id);
                $dataPengirimanLog = LogStokPenerimaan::find($dataPengiriman->id_log_stok);
                $dataTenun = TenunDetail::where('id_pengiriman_barang_detail', $id)->first();
                $dataTenunLog = LogStokPenerimaan::find($dataTenun->id_log_stok_penerimaan);

                $beam = $request->beam;
                if ($request->songket) {
                    $songket = $request->songket;
                } else {
                    $songket = [
                        'id_beam' => null,
                        'id_tenun_detail' => null,
                    ];
                }

                $log = [
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_beam' => $beam['id_beam'],
                    'id_mesin' => $beam['id_mesin'],
                    'volume_masuk_1' => $request['volume_masuk_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'id_songket' => $songket['id_beam'],
                ];

                /* pengiriman barang */
                $logKeluar = $log;
                $logKeluar['code'] = 'BG';
                $logKeluar['volume_masuk_1'] = 0;
                $logKeluar['volume_keluar_1'] = $request['volume_1'];
                $dataPengirimanLog->update($logKeluar);

                $pengiriman = [
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_pengiriman_barang' => $request['id_pengiriman_barang'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_beam' => $beam['id_beam'],
                    'id_mesin' => $beam['id_mesin'],
                    'volume_1' => $request['volume_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'status' => 'ASAL',
                    'id_songket' => $songket['id_beam'],
                ];
                $dataPengiriman->update($pengiriman);
                /* end pengiriman barang */

                /* tenun */
                $logTenun = $log;
                $logTenun['code'] = 'BG';
                $logTenun['volume_masuk_1'] = $request['volume_1'];
                $dataTenunLog->update($logTenun);

                $tenun = [
                    'id_tenun' => $beam['id_tenun'],
                    'id_beam' => $beam['id_beam'],
                    'tanggal' => $request['tanggal'],
                    'tanggal_potong' => $request['tanggal'],
                    'id_gudang' => $request['id_gudang'],
                    'id_barang' => $request['id_barang'],
                    'id_warna' => $beam['id_warna'],
                    'id_motif' => $beam['id_motif'],
                    'id_mesin' => $beam['id_mesin'],
                    'volume_1' => $request['volume_1'],
                    'id_satuan_1' => $request['id_satuan_1'],
                    'code' => 'BG',
                    'id_songket_detail' => $songket['id_tenun_detail'],
                    'id_lusi_detail' => $beam['id_tenun_detail'],
                    'id_songket' => $songket['id_beam'],
                ];
                $dataTenun->update($tenun);
                /* end tenun */

                logHistory('PengirimanBarangDetail', 'update');
                logHistory('TenunDetail', 'update');

                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        }, 5);
    }
    public function hapus()
    {
        DB::beginTransaction();
        try {
            $id = request('id');
            $logId = PengirimanBarangDetail::find($id)->id_log_stok;

            PengirimanBarangDetail::find($id)->delete();
            LogStokPenerimaan::find($logId)->delete();

            $tenun = TenunDetail::where('id_pengiriman_barang_detail', $id)->first();
            LogStokPenerimaan::find($tenun->id_log_stok_penerimaan)->delete();
            $tenun->delete();

            logHistory('PengirimanBarangDetail', 'delete');
            logHistory('TenunDetail', 'delete');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function jsonResponse($success, $message, $status = 200)
    {
        return response()->json(['success' => $success, 'messages' => $message], $status);
    }

    function cekRequest($request)
    {
        $rules = [
            'tanggal' => 'required',
            'id_gudang' => 'required',
            'id_barang' => 'required',
            'id_satuan_1' => 'required',
            'beam' => 'required',
            // 'songket' => 'required',
            'volume_1' => 'required|numeric',

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
    function kosongkan()
    {
        /* hanya sekali pakai */
        DB::beginTransaction();
        try {
            $sql = "SELECT
                CASE 
                    WHEN pe.id_beam IS NOT NULL THEN 'jangan'  
                    ELSE 'boleh'
                END AS status,
                tenun.id_beam,
                tenun.id_gudang,
                tenun.id_barang,
                tenun.id_warna,
                tenun.id_motif,
                tenun.id_mesin,
                tenun.id_satuan_1,
                SUM(tenun.volume_1) volume_1
                FROM tbl_tenun_detail AS tenun
                LEFT JOIN
                (
                SELECT
                *
                FROM tbl_pengiriman_barang_detail AS pe
                WHERE pe.deleted_at IS NULL
                AND pe.id_pengiriman_barang = 965
                ) AS pe ON pe.id_beam = tenun.id_beam AND pe.id_gudang = tenun.id_gudang AND pe.id_barang = tenun.id_barang AND pe.id_warna = tenun.id_warna AND pe.id_motif = tenun.id_motif AND pe.id_mesin = tenun.id_mesin AND pe.id_satuan_1 = tenun.id_satuan_1
                WHERE tenun.deleted_at IS NULL
                AND tenun.code = 'BG'
                AND tenun.id_pengiriman_barang_detail IS NULL
                GROUP BY
                tenun.id_beam,
                tenun.id_gudang,
                tenun.id_barang,
                tenun.id_warna,
                tenun.id_motif,
                tenun.id_mesin,
                tenun.id_satuan_1,
                pe.id_beam
            ";
            $data = DB::table(DB::raw("({$sql}) as data"))->where('status', 'boleh')->get();
            foreach ($data as $i) {
                $log = [
                    'tanggal' => date('Y-m-d'),
                    'id_gudang' => $i->id_gudang,
                    'id_barang' => $i->id_barang,
                    'id_warna' => $i->id_warna,
                    'id_motif' => $i->id_motif,
                    'id_beam' => $i->id_beam,
                    'id_mesin' => $i->id_mesin,
                    'volume_masuk_1' => 0,
                    'volume_keluar_1' => $i->volume_1,
                    'id_satuan_1' => $i->id_satuan_1,
                    'code' => 'BG',
                    'id_gudang' => $i->id_gudang,
                ];
                $logId = LogStokPenerimaan::create($log)->id;
                $pengiriman = [
                    'id_pengiriman_barang' => 965,
                    'id_gudang' => $i->id_gudang,
                    'id_barang' => $i->id_barang,
                    'id_warna' => $i->id_warna,
                    'id_motif' => $i->id_motif,
                    'id_beam' => $i->id_beam,
                    'id_mesin' => $i->id_mesin,
                    'id_log_stok' => $logId,
                    'volume_1' => $i->volume_1,
                    'id_satuan_1' => $i->id_satuan_1,
                    'status' => 'ASAL',
                    'tanggal' => date('Y-m-d'),
                ];
                PengirimanBarangDetail::create($pengiriman);
            }
            DB::commit();
            return dd('data telah dikosongkan');
        } catch (Exception $e) {
            DB::rollBack();
            return dd('data gagal dikosongkan' . $e);
        }
    }
    function undo()
    {
        DB::beginTransaction();
        try {
            $data = PengirimanBarangDetail::where('id_pengiriman_barang', 965)->get();
            foreach ($data as $i) {
                LogStokPenerimaan::find($i->id_log_stok)->delete();
                PengirimanBarangDetail::where('id', $i->id)->delete();
            }
            DB::commit();
            return dd('data telah diundo');
        } catch (Exception $e) {
            DB::rollBack();
            return dd('data gagal diundo' . $e);
        }
    }
}
