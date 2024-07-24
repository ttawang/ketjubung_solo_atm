<?php

namespace App\Http\Controllers;

use App\Models\DistribusiPakan;
use App\Models\DistribusiPakanDetail;
use App\Models\DyeingDetail;
use App\Models\PenerimaanBarang;
use App\Models\PengirimanBarang;
use App\Models\PengirimanSarung;
use App\Models\Tenun;
use App\Models\TenunDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class CetakController extends Controller
{
    public function penerimaanBarang()
    {
        $id = request('id');
        $title = "CETAK KARTU PENERIMAAN BENANG GREY";
        $data = PenerimaanBarang::with('relPenerimaanBarangDetail')->where('id', $id)->first();
        // $customPaper = [0, 0, 567.00, 283.80];
        $pdf = PDF::loadview('contents.production.template_cetak.penerimaan_barang', compact('data', 'title'))->setPaper('f4', 'potrait');
        return $pdf->stream();
    }

    public function pengirimanBarang()
    {
        // dd(request()->all());
        $id = request('id');
        $title = "CETAK KARTU PENGIRIMAN";
        $data = PengirimanBarang::with(['relPengirimanDetail' => function ($query) {
            return $query->where('status', 'ASAL');
        }])->where('id', $id)->first();
        $withWarnaId = [2, 3, 4, 5, 6, 11, 12, 13, 14, 15, 16, 20, 21];
        $withBeamId = [5, 6, 11, 12, 13, 14, 15, 16];
        if ($data->id_tipe_pengiriman == 7) {
            $temp['judul'] = "Bukti Penyerahan Hasil Tenun (BPHT) -  {$data->nomor}";
            $temp['parent'] = $data;
            $sql = "SELECT
                data.id_pengiriman_barang,
                data.id_mesin,
                mesin.name nama_mesin,
                data.id_beam,
                no_beam.name nomor_beam,
                no_kikw.name nomor_kikw,
                data.id_songket,
                warna_songket.alias nama_warna_songket,
                no_kiks.name nomor_kiks,
                data.id_motif,
                motif.alias nama_motif,
                data.id_warna,
                warna.alias nama_warna,
                SUM(COALESCE(data.volume_1,0)) potong,
                MAX(data.sisa) sisa
                FROM
                (
                    SELECT
                    pe.id,
                    pe.id_pengiriman_barang,
                    pe.id_mesin,
                    pe.id_beam,
                    pe.id_songket,
                    pe.id_motif,
                    pe.id_warna,
                    so.id_warna id_warna_songket,
                    pe.volume_1,
                    (
                        SELECT
                        parent.jumlah_beam - SUM(de.volume_1)
                        FROM tbl_tenun_detail AS de
                        LEFT JOIN tbl_tenun AS parent ON parent.id = de.id_tenun
                        WHERE parent.id_beam = pe.id_beam
                        AND de.code = 'BG'
                        AND de.deleted_at IS NULL
                        AND (de.id_pengiriman_barang_detail <= pe.id OR de.id_pengiriman_barang_detail IS NULL)
                        GROUP BY parent.jumlah_beam
                    ) sisa
                    FROM tbl_pengiriman_barang_detail AS pe
                    LEFT JOIN tbl_tenun_detail AS so ON so.id_beam = pe.id_songket AND so.code = 'BBTS' AND so.deleted_at IS NULL
                    WHERE pe.deleted_at IS NULL
                    AND pe.status = 'ASAL'
                    AND pe.id_pengiriman_barang = $id
                ) AS data
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = data.id_warna_songket
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_beam AS songket ON songket.id = data.id_songket
                LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
                GROUP BY data.id_pengiriman_barang, data.id_mesin, data.id_beam, data.id_songket, data.id_motif, data.id_warna, data.volume_1, mesin.name, motif.alias, warna.alias, warna_songket.alias, no_beam.name, no_kikw.name, no_kiks.name
            ";
            $temp['detail'] = DB::table(DB::raw("({$sql}) as data"))->orderByRaw('nama_motif asc, nama_mesin asc')->get();
            $pdf = PDF::loadView("contents.production.cetak.bpht", $temp)->setPaper('a4', 'potrait');
            return $pdf->stream($temp['judul']);
        } elseif ($data->id_tipe_pengiriman == 8) {
            $temp['judul'] = "Lampiran BPSG -  {$data->nomor}";
            $temp['parent'] = $data;
            /* $sql = "SELECT
                kirim.id_barang,
                barang.name nama_barang,
                kirim.id_mesin,
                mesin.name nama_mesin,
                kirim.id_motif,
                motif.alias nama_motif,
                kirim.id_warna,
                warna.alias nama_warna,
                kirim.id_songket,
                warna_songket.alias nama_warna_songket,
                SUM(COALESCE(kirim.baik,0)) baik,
                SUM(COALESCE(kirim.cacat,0)) cacat,
                SUM(COALESCE(kirim.baik,0)) + SUM(COALESCE(kirim.cacat,0)) kirim,
                SUM(COALESCE(terima.volume_1,0)) terima
                FROM
                (
                    SELECT
                    COALESCE(baik.id_barang, cacat.id_barang) id_barang,
                    COALESCE(baik.id_mesin, cacat.id_mesin) id_mesin,
                    COALESCE(baik.id_motif, cacat.id_motif) id_motif,
                    COALESCE(baik.id_warna, cacat.id_warna) id_warna,
                    COALESCE(COALESCE(baik.id_songket,0), cacat.id_songket) id_songket,
                    COALESCE(baik.volume_1,0) baik,
                    COALESCE(cacat.volume_1,0) cacat
                    FROM tbl_pengiriman_barang_detail AS baik
                    FULL OUTER JOIN
                    (
                        SELECT
                        id_barang,
                        id_mesin,
                        id_motif,
                        id_warna,
                        COALESCE(id_songket,0) id_songket,
                        COALESCE(volume_1,0) volume_1
                        FROM tbl_pengiriman_barang_detail AS kirim
                        WHERE deleted_at IS NULL AND status = 'ASAL' AND id_grade = 3 AND id_pengiriman_barang = $id
                    ) AS cacat ON cacat.id_barang = baik.id_barang AND cacat.id_mesin = baik.id_mesin AND cacat.id_motif = baik.id_motif AND cacat.id_warna = baik.id_warna AND COALESCE(cacat.id_songket,0) = COALESCE(baik.id_songket,0)
                    WHERE baik.deleted_at IS NULL AND baik.status = 'ASAL' AND (baik.id_grade IS NULL OR baik.id_grade = 1) AND baik.id_pengiriman_barang = $id
                ) AS kirim
                LEFT JOIN
                (
                    SELECT
                    id_barang,
                    id_mesin,
                    id_motif,
                    id_warna,
                    COALESCE(id_songket,0) id_songket,
                    SUM(COALESCE(terima.volume_1,0)) volume_1
                    FROM tbl_pengiriman_barang_detail AS terima
                    WHERE deleted_at IS NULL AND status = 'TUJUAN' AND id_pengiriman_barang = $id
                    GROUP BY  id_barang, id_mesin, id_motif, id_warna, id_songket
                ) AS terima ON terima.id_barang = kirim.id_barang AND terima.id_mesin = kirim.id_mesin AND terima.id_motif = kirim.id_motif AND terima.id_warna = kirim.id_warna AND terima.id_songket = kirim.id_songket
                LEFT JOIN tbl_barang AS barang ON barang.id = kirim.id_barang
                LEFT JOIN tbl_motif AS motif ON motif.id = kirim.id_motif
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = kirim.id_mesin
                LEFT JOIN tbl_warna AS warna ON warna.id = kirim.id_warna
                LEFT JOIN tbl_tenun_detail AS songket ON songket.id_beam = kirim.id_songket
                LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = songket.id_warna
                GROUP BY kirim.id_barang, kirim.id_mesin, kirim.id_motif, kirim.id_warna, barang.name, warna.alias, mesin.name, motif.alias, kirim.id_songket, warna_songket.alias
                ORDER BY barang.name, motif.alias, warna.alias
            "; */
            /* $sql = "SELECT
                (
                    SELECT
                    MAX(tanggal) tanggal
                    FROM
                    (
                        SELECT
                        tanggal,
                        id_barang,
                        id_warna,
                        id_motif,
                        id_mesin,
                        id_beam,
                        id_songket,
                        SUM(COALESCE(volume_1,0)) volume_1
                        FROM tbl_tenun_detail
                        WHERE deleted_at IS NULL
                        AND id_barang = data.id_barang
                        AND id_warna = data.id_warna
                        AND id_motif = data.id_motif
                        AND id_mesin = data.id_mesin
                        AND COALESCE(id_songket,0) = COALESCE(data.id_songket,0)
                        AND COALESCE(id_beam,0) = COALESCE(data.id_beam,0)
                        AND volume_1 = data.kirim
                        AND code = 'BG'
                        GROUP BY tanggal, id_barang, id_warna, id_motif, id_mesin, id_beam, id_songket
                    ) as data
                ) AS tanggal,
                data.*
                FROM
                (
                    SELECT
                    kirim.id_barang,
                    barang.name nama_barang,
                    kirim.id_mesin,
                    mesin.name nama_mesin,
                    kirim.id_motif,
                    motif.alias nama_motif,
                    kirim.id_warna,
                    warna.alias nama_warna,
                    kirim.id_beam,
                    kirim.id_songket,
                    warna_songket.alias nama_warna_songket,
                    SUM(COALESCE(kirim.baik,0)) baik,
                    SUM(COALESCE(kirim.cacat,0)) cacat,
                    SUM(COALESCE(kirim.baik,0)) + SUM(COALESCE(kirim.cacat,0)) kirim,
                    SUM(COALESCE(terima.volume_1,0)) terima
                    FROM
                    (
                        SELECT
                        COALESCE(baik.id_barang, cacat.id_barang) id_barang,
                        COALESCE(baik.id_mesin, cacat.id_mesin) id_mesin,
                        COALESCE(baik.id_motif, cacat.id_motif) id_motif,
                        COALESCE(baik.id_warna, cacat.id_warna) id_warna,
                        COALESCE(COALESCE(baik.id_beam,0), cacat.id_beam) id_beam,
                        COALESCE(COALESCE(baik.id_songket,0), cacat.id_songket) id_songket,
                        COALESCE(baik.volume_1,0) baik,
                        COALESCE(cacat.volume_1,0) cacat
                        FROM tbl_pengiriman_barang_detail AS baik
                        FULL OUTER JOIN
                        (
                            SELECT
                            id_barang,
                            id_mesin,
                            id_motif,
                            id_warna,
                            COALESCE(id_beam,0) id_beam,
                            COALESCE(id_songket,0) id_songket,
                            COALESCE(volume_1,0) volume_1
                            FROM tbl_pengiriman_barang_detail AS kirim
                            WHERE deleted_at IS NULL AND status = 'ASAL' AND id_grade = 3 AND id_pengiriman_barang = $id
                        ) AS cacat ON cacat.id_barang = baik.id_barang AND cacat.id_mesin = baik.id_mesin AND cacat.id_motif = baik.id_motif AND cacat.id_warna = baik.id_warna AND COALESCE(cacat.id_songket,0) = COALESCE(baik.id_songket,0) AND COALESCE(cacat.id_beam,0) = COALESCE(baik.id_beam,0)
                        WHERE baik.deleted_at IS NULL AND baik.status = 'ASAL' AND (baik.id_grade IS NULL OR baik.id_grade = 1) AND baik.id_pengiriman_barang = $id
                    ) AS kirim
                    LEFT JOIN
                    (
                        SELECT
                        id_barang,
                        id_mesin,
                        id_motif,
                        id_warna,
                        COALESCE(id_beam,0) id_beam,
                        COALESCE(id_songket,0) id_songket,
                        SUM(COALESCE(terima.volume_1,0)) volume_1
                        FROM tbl_pengiriman_barang_detail AS terima
                        WHERE deleted_at IS NULL AND status = 'TUJUAN' AND id_pengiriman_barang = $id
                        GROUP BY  id_barang, id_mesin, id_motif, id_warna, id_songket, id_beam
                    ) AS terima ON terima.id_barang = kirim.id_barang AND terima.id_mesin = kirim.id_mesin AND terima.id_motif = kirim.id_motif AND terima.id_warna = kirim.id_warna AND terima.id_songket = kirim.id_songket AND terima.id_beam = kirim.id_beam
                    LEFT JOIN tbl_barang AS barang ON barang.id = kirim.id_barang
                    LEFT JOIN tbl_motif AS motif ON motif.id = kirim.id_motif
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = kirim.id_mesin
                    LEFT JOIN tbl_warna AS warna ON warna.id = kirim.id_warna
                    LEFT JOIN tbl_tenun_detail AS songket ON songket.id_beam = kirim.id_songket AND songket.code = 'BBTS' AND songket.deleted_at IS NULL
                    LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = songket.id_warna
                    GROUP BY kirim.id_barang, kirim.id_mesin, kirim.id_motif, kirim.id_warna, barang.name, warna.alias, mesin.name, motif.alias, kirim.id_songket, warna_songket.alias, kirim.id_beam
                    ORDER BY barang.name, motif.alias, warna.alias
                ) AS data
            "; */
            $sql = "SELECT
                    kirim.tanggal_potong tanggal,
                    kirim.id_barang,
                    barang.name nama_barang,
                    kirim.id_mesin,
                    mesin.name nama_mesin,
                    kirim.id_motif,
                    motif.alias nama_motif,
                    kirim.id_warna,
                    warna.alias nama_warna,
                    kirim.id_beam,
                    kirim.id_songket,
                    warna_songket.alias nama_warna_songket,
                    SUM(COALESCE(kirim.baik,0)) baik,
                    SUM(COALESCE(kirim.cacat,0)) cacat,
                    SUM(COALESCE(kirim.baik,0)) + SUM(COALESCE(kirim.cacat,0)) kirim,
                    SUM(COALESCE(terima.volume_1,0)) terima
                    FROM
                    (
                        SELECT
                        COALESCE(baik.tanggal_potong, cacat.tanggal_potong) tanggal_potong,
                        COALESCE(baik.id_barang, cacat.id_barang) id_barang,
                        COALESCE(baik.id_mesin, cacat.id_mesin) id_mesin,
                        COALESCE(baik.id_motif, cacat.id_motif) id_motif,
                        COALESCE(baik.id_warna, cacat.id_warna) id_warna,
                        COALESCE(COALESCE(baik.id_beam,0), cacat.id_beam) id_beam,
                        COALESCE(COALESCE(baik.id_songket,0), cacat.id_songket) id_songket,
                        COALESCE(baik.volume_1,0) baik,
                        COALESCE(cacat.volume_1,0) cacat
                        FROM tbl_pengiriman_barang_detail AS baik
                        FULL OUTER JOIN
                        (
                            SELECT
                            tanggal_potong,
                            id_barang,
                            id_mesin,
                            id_motif,
                            id_warna,
                            COALESCE(id_beam,0) id_beam,
                            COALESCE(id_songket,0) id_songket,
                            COALESCE(volume_1,0) volume_1
                            FROM tbl_pengiriman_barang_detail AS kirim
                            WHERE deleted_at IS NULL AND status = 'ASAL' AND id_grade = 3 AND id_pengiriman_barang = $id
                        ) AS cacat ON cacat.id_barang = baik.id_barang AND cacat.id_mesin = baik.id_mesin AND cacat.id_motif = baik.id_motif AND cacat.id_warna = baik.id_warna AND COALESCE(cacat.id_songket,0) = COALESCE(baik.id_songket,0) AND COALESCE(cacat.id_beam,0) = COALESCE(baik.id_beam,0)
                        WHERE baik.deleted_at IS NULL AND baik.status = 'ASAL' AND (baik.id_grade IS NULL OR baik.id_grade = 1) AND baik.id_pengiriman_barang = $id
                    ) AS kirim
                    LEFT JOIN
                    (
                        SELECT
                        tanggal_potong,
                        id_barang,
                        id_mesin,
                        id_motif,
                        id_warna,
                        COALESCE(id_beam,0) id_beam,
                        COALESCE(id_songket,0) id_songket,
                        SUM(COALESCE(terima.volume_1,0)) volume_1
                        FROM tbl_pengiriman_barang_detail AS terima
                        WHERE deleted_at IS NULL AND status = 'TUJUAN' AND id_pengiriman_barang = $id
                        GROUP BY  id_barang, id_mesin, id_motif, id_warna, id_songket, id_beam, tanggal_potong
                    ) AS terima ON terima.id_barang = kirim.id_barang AND terima.id_mesin = kirim.id_mesin AND terima.id_motif = kirim.id_motif AND terima.id_warna = kirim.id_warna AND terima.id_songket = kirim.id_songket AND terima.id_beam = kirim.id_beam AND terima.tanggal_potong = kirim.tanggal_potong
                    LEFT JOIN tbl_barang AS barang ON barang.id = kirim.id_barang
                    LEFT JOIN tbl_motif AS motif ON motif.id = kirim.id_motif
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = kirim.id_mesin
                    LEFT JOIN tbl_warna AS warna ON warna.id = kirim.id_warna
                    LEFT JOIN tbl_tenun_detail AS songket ON songket.id_beam = kirim.id_songket AND songket.code = 'BBTS' AND songket.deleted_at IS NULL
                    LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = songket.id_warna
                    GROUP BY kirim.id_barang, kirim.id_mesin, kirim.id_motif, kirim.id_warna, barang.name, warna.alias, mesin.name, motif.alias, kirim.id_songket, warna_songket.alias, kirim.id_beam, kirim.tanggal_potong
                    ORDER BY motif.alias, mesin.name
            ";
            $temp['detail'] = DB::table(DB::raw("({$sql}) as data"))->get();
            // return view('contents.production.cetak.bpsg');
            $pdf = PDF::loadView("contents.production.cetak.bpsg", $temp)->setPaper('a4');
            return $pdf->stream($temp['judul']);
        } else {
            $checkNullVolume2 = $data->relPengirimanDetail()->value('id_satuan_2') == null;
            $pdf = PDF::loadview('contents.production.template_cetak.pengiriman_barang', compact('data', 'title', 'checkNullVolume2'))->setPaper('a4', 'potrait');
            return $pdf->stream();
        }
    }

    public function dyeing()
    {
        $id = request('id');
        $title = "CETAK KARTU INSTRUKSI KERJA DYEING";
        $queryOvercone = DB::table('tbl_dyeing_detail')
            ->whereNull('deleted_at')
            ->where('status', 'OVERCONE')
            ->whereNotNull('id_warna')
            ->groupBy('id', 'tanggal', 'id_barang', 'id_warna');

        $queryDyeoven = DB::table('tbl_dyeing_detail')
            ->leftJoinSub($queryOvercone, 'qover', function ($join) {
                return $join->on('tbl_dyeing_detail.id', 'qover.id_parent');
            })
            ->whereNull('tbl_dyeing_detail.deleted_at')
            ->where('tbl_dyeing_detail.status', 'DYEOVEN')
            ->whereNotNull('tbl_dyeing_detail.id_warna')
            ->selectRaw('tbl_dyeing_detail.id, tbl_dyeing_detail.id_warna, tbl_dyeing_detail.id_parent, 
            tbl_dyeing_detail.tanggal as tanggal_dyeoven, qover.tanggal as tanggal_overcone,
            SUM(COALESCE(tbl_dyeing_detail.volume_1, 0)) as volume_dyeoven_1, SUM(COALESCE(tbl_dyeing_detail.volume_2, 0)) as volume_dyeoven_2, 
            SUM(COALESCE(qover.volume_1, 0)) as volume_overcone_1, SUM(COALESCE(qover.volume_2, 0)) as volume_overcone_2')
            ->groupBy('tbl_dyeing_detail.id', 'tbl_dyeing_detail.tanggal', 'qover.tanggal', 'tbl_dyeing_detail.id_barang', 'tbl_dyeing_detail.id_warna');

        $data = DyeingDetail::where('id_dyeing', $id)
            ->leftJoinSub($queryDyeoven, 'qdyeoven', function ($join) {
                return $join->on('tbl_dyeing_detail.id', 'qdyeoven.id_parent');
            })
            ->whereNull('tbl_dyeing_detail.id_parent')
            ->selectRaw('tbl_dyeing_detail.id, MAX(tbl_dyeing_detail.id_dyeing) as id_dyeing, tbl_dyeing_detail.id_gudang, tbl_dyeing_detail.id_barang, qdyeoven.id_warna, 
            tbl_dyeing_detail.tanggal as tanggal_softcone, qdyeoven.tanggal_dyeoven as tanggal_dyeoven, qdyeoven.tanggal_overcone as tanggal_overcone,
            SUM(COALESCE(tbl_dyeing_detail.volume_1, 0)) as volume_softcone_1, SUM(COALESCE(tbl_dyeing_detail.volume_2, 0)) as volume_softcone_2,
            SUM(COALESCE(qdyeoven.volume_dyeoven_1, 0)) as volume_dyeoven_1, SUM(COALESCE(qdyeoven.volume_dyeoven_2, 0)) as volume_dyeoven_2, 
            SUM(COALESCE(qdyeoven.volume_overcone_1, 0)) as volume_overcone_1, SUM(COALESCE(qdyeoven.volume_overcone_2, 0)) as volume_overcone_2')
            ->groupBy('tbl_dyeing_detail.id', 'tbl_dyeing_detail.id_gudang', 'tbl_dyeing_detail.tanggal', 'tbl_dyeing_detail.tanggal', 'qdyeoven.tanggal_dyeoven', 'qdyeoven.tanggal_overcone', 'tbl_dyeing_detail.id_barang', 'qdyeoven.id_warna')
            ->get();

        $pdf = PDF::loadview('contents.production.template_cetak.kartu_instruksi_kerja_dyeing', compact('id', 'data', 'title'))->setPaper('a4', 'landscape');
        return $pdf->stream();
    }
    public function tenun()
    {
        $id = request('id');
        $no_kikw = Tenun::find($id)->throughNomorKikw->name;
        $songket = TenunDetail::where('id_tenun', $id)->where('code', 'BBTS')->pluck('id')->toArray();
        $lusi = TenunDetail::where('id_tenun', $id)->where('code', 'BBTL')->first();
        $data['no_kikw'] = $no_kikw;
        $data['judul'] = 'KIKW - ' . $no_kikw;
        $data['lusi'] = $lusi ?? null;
        $data['songket_1'] = array_key_exists(0, $songket) ? TenunDetail::find($songket[0]) : null;
        $data['songket_2'] = array_key_exists(1, $songket) ? TenunDetail::find($songket[1]) : null;
        $data['songket_3'] = array_key_exists(2, $songket) ? TenunDetail::find($songket[2]) : null;

        // return view('contents.production.template_cetak.kikw', $data);
        return dd('not available, please contact developer');
        $pdf = PDF::loadview('contents.production.template_cetak.kikw', $data)->setPaper('f4', 'potrait');
        return $pdf->stream($data['judul']);
    }
    public function distribusiPakan(Request $request)
    {
        $id = $request->id;
        $data['parent'] = DistribusiPakan::find($id);
        $data['judul'] = ($data['parent']->tipe == 'rappier') ? 'DPR (DISTRIBUSI BENANG PAKAN PADA RAPPIER LOOM)' : 'DPS (DISTRIBUSI BENANG PAKAN PADA SHUTTLE LOOM)';
        $sql = "SELECT
            data.*,
            mesin.name nama_mesin,
            no_kikw.name no_kikw,
            warna_1.alias nama_warna_1,
            warna_2.alias nama_warna_2,
            warna_3.alias nama_warna_3,
            warna_4.alias nama_warna_4,
            warna_5.alias nama_warna_5,
            warna_6.alias nama_warna_6,
            warna_7.alias nama_warna_7,
            warna_8.alias nama_warna_8,
            warna_9.alias nama_warna_9,
            warna_10.alias nama_warna_10
            FROM
            (
            WITH NumberedDistribusi AS (
                SELECT
                    de.id_barang,
                    de.id_beam,
                    de.id_mesin,
                    de.id_warna,
                    de.volume_1,
                    de.volume_2,
                    ROW_NUMBER() OVER (PARTITION BY de.id_barang, de.id_beam, de.id_mesin ORDER BY de.id_warna) AS warna_rank
                FROM
                    tbl_distribusi_pakan_detail AS de
                WHERE
                    de.id_distribusi_pakan = $id
            )
            SELECT
                id_barang,
                id_beam,
                id_mesin,
                MAX(CASE WHEN warna_rank = 1 THEN id_warna END) AS id_warna_1,
                MAX(CASE WHEN warna_rank = 1 THEN volume_1 END) AS volume_1_warna_1,
                MAX(CASE WHEN warna_rank = 1 THEN volume_2 END) AS volume_2_warna_1,
                MAX(CASE WHEN warna_rank = 2 THEN id_warna END) AS id_warna_2,
                MAX(CASE WHEN warna_rank = 2 THEN volume_1 END) AS volume_1_warna_2,
                MAX(CASE WHEN warna_rank = 2 THEN volume_2 END) AS volume_2_warna_2,
                MAX(CASE WHEN warna_rank = 3 THEN id_warna END) AS id_warna_3,
                MAX(CASE WHEN warna_rank = 3 THEN volume_1 END) AS volume_1_warna_3,
                MAX(CASE WHEN warna_rank = 3 THEN volume_2 END) AS volume_2_warna_3,
                MAX(CASE WHEN warna_rank = 4 THEN id_warna END) AS id_warna_4,
                MAX(CASE WHEN warna_rank = 4 THEN volume_1 END) AS volume_1_warna_4,
                MAX(CASE WHEN warna_rank = 4 THEN volume_2 END) AS volume_2_warna_4,
                MAX(CASE WHEN warna_rank = 5 THEN id_warna END) AS id_warna_5,
                MAX(CASE WHEN warna_rank = 5 THEN volume_1 END) AS volume_1_warna_5,
                MAX(CASE WHEN warna_rank = 5 THEN volume_2 END) AS volume_2_warna_5,
                MAX(CASE WHEN warna_rank = 6 THEN id_warna END) AS id_warna_6,
                MAX(CASE WHEN warna_rank = 6 THEN volume_1 END) AS volume_1_warna_6,
                MAX(CASE WHEN warna_rank = 6 THEN volume_2 END) AS volume_2_warna_6,
                MAX(CASE WHEN warna_rank = 7 THEN id_warna END) AS id_warna_7,
                MAX(CASE WHEN warna_rank = 7 THEN volume_1 END) AS volume_1_warna_7,
                MAX(CASE WHEN warna_rank = 7 THEN volume_2 END) AS volume_2_warna_7,
                MAX(CASE WHEN warna_rank = 8 THEN id_warna END) AS id_warna_8,
                MAX(CASE WHEN warna_rank = 8 THEN volume_1 END) AS volume_1_warna_8,
                MAX(CASE WHEN warna_rank = 8 THEN volume_2 END) AS volume_2_warna_8,
                MAX(CASE WHEN warna_rank = 9 THEN id_warna END) AS id_warna_9,
                MAX(CASE WHEN warna_rank = 9 THEN volume_1 END) AS volume_1_warna_9,
                MAX(CASE WHEN warna_rank = 9 THEN volume_2 END) AS volume_2_warna_9,
                MAX(CASE WHEN warna_rank = 10 THEN id_warna END) AS id_warna_10,
                MAX(CASE WHEN warna_rank = 10 THEN volume_1 END) AS volume_1_warna_10,
                MAX(CASE WHEN warna_rank = 10 THEN volume_2 END) AS volume_2_warna_10
            FROM
                NumberedDistribusi
            GROUP BY
                id_barang,
                id_beam,
                id_mesin
            ) AS data
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_warna AS warna_1 ON warna_1.id = data.id_warna_1
            LEFT JOIN tbl_warna AS warna_2 ON warna_2.id = data.id_warna_2
            LEFT JOIN tbl_warna AS warna_3 ON warna_3.id = data.id_warna_3
            LEFT JOIN tbl_warna AS warna_4 ON warna_4.id = data.id_warna_4
            LEFT JOIN tbl_warna AS warna_5 ON warna_5.id = data.id_warna_5
            LEFT JOIN tbl_warna AS warna_6 ON warna_6.id = data.id_warna_6
            LEFT JOIN tbl_warna AS warna_7 ON warna_7.id = data.id_warna_7
            LEFT JOIN tbl_warna AS warna_8 ON warna_8.id = data.id_warna_8
            LEFT JOIN tbl_warna AS warna_9 ON warna_9.id = data.id_warna_9
            LEFT JOIN tbl_warna AS warna_10 ON warna_10.id = data.id_warna_10
        ";
        $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->orderBy('nama_mesin')->get();
        $pdf = PDF::loadview('contents.production.cetak.distribusi-pakan', $data)->setPaper('f4', 'landscape');
        return $pdf->stream($data['judul']);
    }
    function pengirimanSarung(Request $request)
    {
        // dd($request->all());
        $tujuan = '';
        if ($request->tipe_selected !== "null" && $request->tipe_selected !== null) {
            $tujuan = $request->tipe_selected;
        }
        $parent = PengirimanSarung::find($request->id);
        $data['judul'] = 'PENGIRIMAN SARUNG ' . ($tujuan ? $tujuan . ' ' : '') . '- ' . $parent['nomor'];
        $sql = "SELECT
            data.*,
            barang.name nama_barang,
            mesin.name nama_mesin,
            no_beam.name no_beam,
            no_kikw.name no_kikw,
            no_kiks.name no_kiks,
            motif.alias nama_motif,
            warna.alias nama_warna,
            tenun_detail.id_warna id_warna_songket,
            warna_songket.alias nama_warna_songket
            FROM
            (
                WITH volume_data AS (
                    SELECT
                        id_pengiriman_sarung,
                        id_gudang,
                        id_beam,
                        id_songket,
                        id_mesin,
                        id_barang,
                        id_warna,
                        id_motif,
                        id_grade,
                        tanggal_potong,
                        volume_1
                    FROM tbl_pengiriman_sarung_detail
                    WHERE deleted_at IS NULL
                )
                SELECT
                    id_pengiriman_sarung,
                    id_gudang,
                    id_beam,
                    id_songket,
                    id_mesin,
                    id_barang,
                    id_warna,
                    id_motif,
                    SUM(CASE WHEN id_grade = 1 THEN volume_1 ELSE 0 END) AS baik,
                    SUM(CASE WHEN id_grade = 3 THEN volume_1 ELSE 0 END) AS cacat,
                    tanggal_potong,
                    SUM(volume_1) AS jml
                FROM volume_data
                GROUP BY
                    id_pengiriman_sarung,
                    id_gudang,
                    id_beam,
                    id_songket,
                    id_mesin,
                    id_barang,
                    id_warna,
                    id_motif,
                    tanggal_potong
            ) AS data
            LEFT JOIN tbl_tenun AS tenun ON tenun.id_beam = data.id_beam AND tenun.deleted_at IS NULL
            LEFT JOIN tbl_tenun_detail AS tenun_detail ON tenun_detail.id_tenun = tenun.id AND tenun_detail.id_beam = data.id_songket AND tenun_detail.deleted_at IS NULL
            LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
            LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
            LEFT JOIN tbl_warna AS warna_songket ON warna_songket.id = tenun_detail.id_warna
            LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_beam AS songket ON songket.id = data.id_songket
            LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
            WHERE data.id_pengiriman_sarung = 36
            ORDER BY barang.name, motif.alias, mesin.name, warna.alias";
        $data['parent'] = $parent;
        $data['detail'] = $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->get();
        $pdf = PDF::loadview('contents.production.cetak.pengiriman-sarung', $data)->setPaper('f4', 'potrait');
        return $pdf->stream($data['judul']);
    }
}
