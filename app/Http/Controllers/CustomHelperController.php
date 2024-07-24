<?php

namespace App\Http\Controllers;

use App\Exports\ExportExcelFromView;
use App\Models\LogStokPenerimaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomHelperController extends Controller
{
    protected static $models = [
        'Warping' => \App\Models\Warping::class,
        'Sizing' => \App\Models\Sizing::class,
        'Pakan' => \App\Models\Pakan::class,
        'Leno' => \App\Models\Leno::class,
        'TenunDetail' => \App\Models\TenunDetail::class,
        'Dudulan' => \App\Models\Dudulan::class,
        'InspectDudulanDetail' => \App\Models\InspectDudulanDetail::class,
        'InspectingGrey' => \App\Models\InspectingGrey::class,
        'JahitSambungDetail' => \App\Models\JahitSambungDetail::class,
        'FoldingDetail' => \App\Models\FoldingDetail::class,
        'P1' => \App\Models\P1::class,
        'InspectP1Detail' => \App\Models\InspectP1Detail::class,
        'FinishingCabut' => \App\Models\FinishingCabut::class,
        'InspectFinishingCabutDetail' => \App\Models\InspectFinishingCabutDetail::class,
        'JiggerDetail' => \App\Models\JiggerDetail::class,
        'DryingDetail' => \App\Models\DryingDetail::class,
        'P2' => \App\Models\P2::class,
        'InspectP2Detail' => \App\Models\InspectP2Detail::class,
    ];
    function validasi($model, $id, $status)
    {
        DB::beginTransaction();
        try {
            if ($status === 'simpan') {
                $data['validated_at'] = Carbon::now();
                $msg = 'Data berhasil divalidasi';
                logHistory($model, 'validasi');
            } else {
                $data['validated_at'] = null;
                $msg = 'Pembatalan validasi data berhasil';
                logHistory($model, 'batal_validasi');
            }
            self::$models[$model]::find($id)->update($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => $msg]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    function getSpkPengiriman()
    {
        $iduser = Auth::user()->roles_id;
        $data = DB::table('tbl_pengiriman_barang as parent')
            ->leftJoin('tbl_tipe_pengiriman as tipe', 'tipe.id', 'parent.id_tipe_pengiriman')
            ->when($iduser != 1, function ($q) use ($iduser) {
                $q->where('tipe.roles_id_tujuan', $iduser);
            })
            ->whereNull('parent.deleted_at')
            ->selectRaw('tipe.initial, parent.nomor, parent.id')
            ->orderBy('parent.id', 'desc')
            ->get();
        // $data = TipePengiriman::all();
        return $data;
    }
    function cetakDistribusiPakan(Request $request)
    {
        $tglAwal = $request->tglAwal;
        $tglAkhir = $request->tglAkhir;
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
                    de.tanggal >= '$tglAwal' AND de.tanggal <= '$tglAkhir' 
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
        $data['judul'] = 'DISTRIBUSI PAKAN (' . tglIndo($tglAwal) . ' - ' . tglIndo($tglAkhir) . ')';
        $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->orderBy('nama_mesin')->get();
        $pdf = PDF::loadview('contents.production.cetak.distribusi-pakan-all', $data)->setPaper('f4', 'landscape');
        return $pdf->stream($data['judul']);
    }
    function cetak(Request $request)
    {
        $proses = $request->proses;
        $id = $request->id ?? null;
        $model = getModelByProses($proses);
        $tipeCetak = $request->tipe_cetak ?? null;

        if ($tipeCetak == 'spk') { /* surat perintah kerja */
            $code = $model['code_input'];
        } else if ($tipeCetak == 'pspk') { /* penyelesaian perintah kerja */
            $code = $model['code_output'];
        } else {
            $code = null;
        }

        $menu = [
            'inspecting' => ['dudulan'],
            'finishing' => ['p1', 'finishing_cabut', 'p2'],
            'inspect_finishing' => ['inspect_p1', 'inspect_finishing_cabut', 'inspect_p2', 'jigger', 'drying'],
        ];
        $add = '';
        if ($proses == 'dudulan') {
            if ($tipeCetak == 'spk') {
                $sql = "SELECT
                    data.tanggal_potong,
                    data.id_barang,
                    barang.name nama_barang,
                    data.id_motif,
                    motif.alias nama_motif,
                    data.id_warna,
                    warna.alias nama_warna,
                    data.id_mesin,
                    mesin.name nama_mesin,
                    SUM(COALESCE(data.baik,0)) baik,
                    SUM(COALESCE(data.cacat,0)) cacat,
                    SUM(COALESCE(data.total,0)) total
                    FROM
                    (
                    SELECT
                        COALESCE(baik.tanggal_potong, cacat.tanggal_potong) tanggal_potong,
                        COALESCE(baik.id_barang, cacat.id_barang) id_barang,
                        COALESCE(baik.id_mesin, cacat.id_mesin) id_mesin,
                        COALESCE(baik.id_motif, cacat.id_motif) id_motif,
                        COALESCE(baik.id_warna, cacat.id_warna) id_warna,
                        COALESCE(baik.volume_1,0) baik,
                        COALESCE(cacat.volume_1,0) cacat,
                        COALESCE(baik.volume_1,0) + COALESCE(cacat.volume_1,0) total
                        FROM tbl_dudulan_detail AS baik
                        FULL OUTER JOIN
                        (
                            SELECT
                            tanggal_potong,
                            id_barang,
                            id_mesin,
                            id_motif,
                            id_warna,
                            COALESCE(volume_1,0) volume_1
                            FROM tbl_dudulan_detail
                            WHERE deleted_at IS NULL AND code = 'BGIG' AND id_grade = 3 AND id_dudulan = $id
                        ) AS cacat ON cacat.id_barang = baik.id_barang AND cacat.id_mesin = baik.id_mesin AND cacat.id_motif = baik.id_motif AND cacat.id_warna = baik.id_warna AND cacat.tanggal_potong = baik.tanggal_potong
                        WHERE baik.deleted_at IS NULL AND baik.code = 'BGIG' AND (baik.id_grade IS NULL OR baik.id_grade = 1) AND baik.id_dudulan = $id 
                    ) AS data
                    LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                    LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                    LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                    GROUP BY data.id_barang, data.id_mesin, data.id_motif, data.id_warna, barang.name, warna.alias, mesin.name, motif.alias, data.tanggal_potong
                    ORDER BY barang.name, motif.alias, warna.alias, mesin.name, data.tanggal_potong
                ";
                $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->get();
                $data['parent'] = $model['parent']::whereId($id)->first();
            } else {
                $sql = "SELECT
                    id_barang,
                    barang.name nama_barang,
                    id_mesin,
                    mesin.name nama_mesin,
                    id_motif,
                    motif.alias nama_motif,
                    COALESCE(MAX(CASE WHEN tahap = 1 THEN data.total END), 0) AS tahap_1,
                    COALESCE(MAX(CASE WHEN tahap = 2 THEN data.total END), 0) AS tahap_2,
                    COALESCE(MAX(CASE WHEN tahap = 3 THEN data.total END), 0) AS tahap_3,
                    SUM(baik) baik,
                    SUM(cacat) cacat,
                    SUM(total) total
                    FROM
                    (
                        SELECT
                        COALESCE(baik.tanggal, cacat.tanggal) AS tanggal,
                        COALESCE(baik.id_barang, cacat.id_barang) AS id_barang,
                        COALESCE(baik.id_mesin, cacat.id_mesin) AS id_mesin,
                        COALESCE(baik.id_motif, cacat.id_motif) AS id_motif,
                        COALESCE(baik.volume_1, 0) AS baik,
                        COALESCE(cacat.volume_1, 0) AS cacat,
                        COALESCE(baik.volume_1, 0) + COALESCE(cacat.volume_1, 0) AS total,
                        ROW_NUMBER() OVER(
                            PARTITION BY 
                                COALESCE(baik.id_barang, cacat.id_barang), 
                                COALESCE(baik.id_mesin, cacat.id_mesin), 
                                COALESCE(baik.id_motif, cacat.id_motif)
                            ORDER BY COALESCE(baik.tanggal, cacat.tanggal)) AS tahap
                        FROM tbl_dudulan_detail AS baik
                        FULL OUTER JOIN
                        (
                            SELECT
                                tanggal,
                                id_barang,
                                id_mesin,
                                id_motif,
                                COALESCE(volume_1, 0) AS volume_1
                            FROM tbl_dudulan_detail
                            WHERE deleted_at IS NULL AND code = 'BGD' AND id_grade = 3 AND id_dudulan = $id
                        ) AS cacat ON cacat.id_barang = baik.id_barang AND cacat.id_mesin = baik.id_mesin AND cacat.id_motif = baik.id_motif
                        WHERE baik.deleted_at IS NULL AND baik.code = 'BGD' AND (baik.id_grade IS NULL OR baik.id_grade = 1) AND baik.id_dudulan = $id
                    ) AS data
                    LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                    LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                    GROUP BY id_barang, id_mesin, id_motif, barang.name, motif.alias, mesin.name
                ";
                $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->get();
                $data['parent'] = $model['parent']::whereId($id)->first();
            }
            $add = "-$proses";
            $data['judul'] = strtoupper($tipeCetak) . ' - ' . strtoupper($proses);
            $pdf = PDF::loadView("contents.production.cetak.{$tipeCetak}{$add}", $data)->setPaper('a5', 'potrait');
        } else {
            if (in_array($proses, $menu['inspect_finishing'])) {
                $parent = substr($proses, strlen('inspect_'));
                $spk = ($request->spk == 'null' || $request->spk == null) ? '' : "AND de.id_{$parent} = $request->spk";
                $tanggal = $request->tanggal == null ? '' : "AND de.tanggal = '$request->tanggal'";
                if ($proses == 'jigger' || $proses == 'drying') {
                    $sql = "SELECT
                        de.id,
                        de.tanggal,
                        de.tanggal_potong,
                        de.id_gudang,
                        gudang.name nama_gudang,
                        de.id_beam,
                        no_beam.name no_beam,
                        no_kikw.name no_kikw,
                        de.id_songket,
                        no_kiks.name no_kiks,
                        de.id_barang,
                        barang.name nama_barang,
                        de.id_warna,
                        warna.alias nama_warna,
                        de.id_motif,
                        motif.alias nama_motif,
                        de.id_mesin,
                        mesin.name nama_mesin,
                        de.id_grade,
                        grade.grade nama_grade,
                        de.volume_1 jml
                        FROM tbl_{$proses}_detail AS de
                        LEFT JOIN tbl_beam AS beam ON beam.id = de.id_beam
                        LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                        LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                        LEFT JOIN tbl_beam AS songket ON songket.id = de.id_songket
                        LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
                        LEFT JOIN tbl_barang AS barang ON barang.id = de.id_barang
                        LEFT JOIN tbl_motif AS motif ON motif.id = de.id_motif
                        LEFT JOIN tbl_warna AS warna ON warna.id = de.id_warna
                        LEFT JOIN tbl_mesin AS mesin ON mesin.id = de.id_mesin
                        LEFT JOIN tbl_gudang AS gudang ON gudang.id = de.id_gudang
                        LEFT JOIN tbl_kualitas AS grade ON grade.id = de.id_grade
                        WHERE de.deleted_at IS NULL
                        $tanggal
                    ";
                } else {
                    $sql = "SELECT
                        de.id_{$parent},
                        parent.nomor,
                        de.id,
                        de.tanggal,
                        de.tanggal_potong,
                        de.id_gudang,
                        gudang.name nama_gudang,
                        de.id_beam,
                        no_beam.name no_beam,
                        no_kikw.name no_kikw,
                        de.id_songket,
                        no_kiks.name no_kiks,
                        de.id_barang,
                        barang.name nama_barang,
                        de.id_warna,
                        warna.alias nama_warna,
                        de.id_motif,
                        motif.alias nama_motif,
                        de.id_mesin,
                        mesin.name nama_mesin,
                        de.id_grade,
                        grade.grade nama_grade,
                        kualitas.kode nama_kualitas,
                        de.volume_1 jml
                        FROM tbl_{$proses}_detail AS de
                        LEFT JOIN tbl_{$parent} AS parent ON parent.id = de.id_{$parent}
                        LEFT JOIN
                        (
                            SELECT
                            de.id_{$proses}_detail id,
                            STRING_AGG(ku.kode, ', ') kode
                            FROM tbl_{$proses}_kualitas AS de
                            LEFT JOIN tbl_mapping_kualitas AS ku ON ku.id = de.id_kualitas
                            WHERE de.deleted_at IS NULL
                            GROUP BY de.id_{$proses}_detail
                        ) AS kualitas ON kualitas.id = de.id
                        LEFT JOIN tbl_beam AS beam ON beam.id = de.id_beam
                        LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                        LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                        LEFT JOIN tbl_beam AS songket ON songket.id = de.id_songket
                        LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
                        LEFT JOIN tbl_barang AS barang ON barang.id = de.id_barang
                        LEFT JOIN tbl_motif AS motif ON motif.id = de.id_motif
                        LEFT JOIN tbl_warna AS warna ON warna.id = de.id_warna
                        LEFT JOIN tbl_mesin AS mesin ON mesin.id = de.id_mesin
                        LEFT JOIN tbl_gudang AS gudang ON gudang.id = de.id_gudang
                        LEFT JOIN tbl_kualitas AS grade ON grade.id = de.id_grade
                        WHERE de.deleted_at IS NULL
                        $spk $tanggal
                    ";
                }
                $add = '';
                $data['spk'] = null;
                $data['tanggal'] = null;
                if ($proses != 'jigger' && $proses != 'drying') {
                    if ($request->spk != 'null' || $request->spk != null) {
                        $data['parent'] = $model['parent']::whereId($request->spk)->first();
                        $add = $add . ' - ' . str_replace('/', '-', $data['parent']->nomor);
                        $data['spk'] = $request->spk;
                    }
                }
                if ($request->tanggal) {
                    $add = $add . ' (' . tglCustom($request->tanggal) . ')';
                    $data['tanggal'] = $request->tanggal;
                }
                if (strpos($proses, 'inspect') !== false) {
                    $add = 'INSPECT JASA LUAR ' . str_replace('_', ' ', strtoupper($parent)) . $add;
                } else {
                    $add = str_replace('_', ' ', strtoupper($proses)) . $add;
                }
                $data['detail'] = DB::table(DB::raw("({$sql}) as data"))->orderByRaw('tanggal asc, nama_motif asc')->get();
                $data['judul'] = $add;
                $data['proses'] = $proses;
                $data['file'] = 'contents.production.cetak.inspect-finishing';
                // return view('contents.production.cetak.inspect-finishing', $data);
                return Excel::download(new ExportExcelFromView($data), $data['judul'] . '.xlsx');
            } else {
                $data['detail'] = DB::table("tbl_{$proses}_detail as data")
                    ->where("id_{$proses}", $id)
                    ->where('code', $code)
                    ->whereNull('data.deleted_at')
                    ->selectRaw("
                        id_{$proses},
                        code,
                        tanggal,
                        tanggal_potong,
                        id_motif,
                        motif.alias nama_motif,
                        SUM(volume_1) volume_1
                    ")
                    ->leftJoin('tbl_motif as motif', 'motif.id', 'data.id_motif')
                    ->groupBy("id_{$proses}", 'code', 'tanggal', 'id_motif', 'motif.alias', 'tanggal_potong')->get();
                $data['parent'] = $model['parent']::whereId($id)
                    ->withSum(["rel{$model['detail_name']} as total_kirim" => function ($query) use ($model) {
                        $query->where('code', $model['code_input']);
                    }], 'volume_1')->first();
                $data['proses'] = ucwords(str_replace("_", " ", $proses));
                if (in_array($proses, $menu['finishing'])) {
                    $data['menu'] = 'Finishing';
                }
                if (in_array($proses, $menu['inspecting'])) {
                    $data['menu'] = 'Inspecting';
                }
                $data['judul'] = strtoupper("{$tipeCetak} {$proses} - {$data['parent']->nomor}");
                $pdf = PDF::loadView("contents.production.cetak.{$tipeCetak}{$add}", $data)->setPaper('a5', 'landscape');
            }
        }

        return $pdf->stream($data['judul']);
    }
    function terimaSemuaBarangJasaLuar(Request $request)
    {
        DB::beginTransaction();
        try {
            $proses = $request->proses;
            $model = getModelByProses($proses);
            $modelInspect = getModelByProses("inspect_{$proses}");
            $diterima = $model['detail']::where("id_{$proses}", $request->id)->whereNotNull('id_parent')->pluck('id_parent');
            $kirim = $model['detail']::where("id_{$proses}", $request->id)->whereNull('id_parent')->whereNotIn('id', $diterima)->get();
            if (count($kirim) > 0) {
                foreach ($kirim as $i) {
                    $data = [];
                    $log = [];
                    $data['id_mesin'] = $i->id_mesin;
                    $data['id_gudang'] = $i->id_gudang;
                    $data['id_barang'] = $i->id_barang;
                    $data['id_warna'] = $i->id_warna;
                    $data['id_motif'] = $i->id_motif;
                    $data['id_beam'] = $i->id_beam;
                    $data['id_songket'] = $i->id_songket;
                    $data['id_grade'] = $i->id_grade;
                    $data['id_kualitas'] = $i->id_kualitas;
                    $data['id_satuan_1'] = $i->id_satuan_1;
                    $data['id_satuan_2'] = $i->id_satuan_2;
                    $data['tanggal'] = $request->tanggal;
                    $data['tanggal_potong'] = $i->tanggal_potong;
                    $data['code'] = $model['code_output'];

                    /* log */
                    $log = $data;
                    $log['volume_masuk_1'] = $i->volume_1;
                    $log['volume_masuk_2'] = $i->volume_2;
                    $logId = LogStokPenerimaan::create($log)->id;

                    $logInspectKeluar = $data;
                    $logInspectKeluar['volume_keluar_1'] = $i->volume_1;
                    $logInspectKeluar['volume_keluar_2'] = $i->volume_2;
                    $logInspectKeluarId = LogStokPenerimaan::create($logInspectKeluar)->id;

                    $logInspectMasuk = $data;
                    $logInspectMasuk['volume_masuk_1'] = $i->volume_1;
                    $logInspectMasuk['volume_masuk_2'] = $i->volume_2;
                    $logInspectMasuk['code'] = "I{$data['code']}";
                    $logInspectMasukId = LogStokPenerimaan::create($logInspectMasuk)->id;
                    /* end log */

                    $kolomSpk = "id_{$proses}";
                    $data["$kolomSpk"] = $i->$kolomSpk;
                    $data['volume_1'] = $i->volume_1;
                    $data['volume_2'] = $i->volume_2;

                    /* inspect */
                    $dataInspect = $data;
                    $dataInspect['code'] = "I{$data['code']}";
                    $dataInspect['id_log_stok_penerimaan_keluar'] = $logInspectKeluarId;
                    $dataInspect['id_log_stok_penerimaan_masuk'] = $logInspectMasukId;
                    $modelInspect['detail']::create($dataInspect);
                    /* end inspect */

                    $data['id_parent'] = $i->id;
                    $data['id_log_stok_penerimaan'] = $logId;
                    if (isset($i->id_inspect_retur)) $data['id_inspect_retur'] = $i->id_inspect_retur;
                    $model['detail']::create($data);
                }
            } else {
                return response()->json(['success' => false, 'messages' => 'Data telah diterima semua'], 500);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil diterima'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['messages' => $e->getMessage()], 500);
        }
    }

    public function simpan(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $proses = $request->input('proses', null);
            $mode = $request->input('mode', null);
            $tipe = $request->input('tipe', null);

            if (!$proses || !$mode || ($mode != 'parent' && !$tipe)) {
                return $this->jsonResponse(false, 'Proses, mode, atau tipe belum di set', 500);
            }

            $rule = $this->cekRequest($request);

            if (!$rule['success']) {
                return response()->json($rule);
            }

            $model = getModelByProses($proses);
            $id = $request->input('id');

            $dataParent = $request->only(['tanggal', 'nomor', 'id_supplier', 'catatan']);
            $kolomSpk = "id_{$proses}";

            $dataDetail = $request->only([
                'tanggal', 'id_gudang', 'id_barang', 'id_warna', 'id_motif', 'id_grade',
                'id_kualitas', 'id_beam', 'id_songket', 'tanggal_potong', 'id_mesin', 'id_satuan_1', 'id_satuan_2',
                'volume_1', 'volume_2', 'id_parent', 'id_inspect_retur', $kolomSpk
            ]);

            $dataInspect = $request->only([
                'tanggal', 'id_gudang', 'id_barang', 'id_warna', 'id_motif', 'id_grade',
                'id_kualitas', 'id_beam', 'id_songket', 'tanggal_potong', 'id_mesin', 'id_satuan_1', 'id_satuan_2',
                'volume_1', 'volume_2', $kolomSpk
            ]);

            $logDetail = $request->only([
                'tanggal', 'id_gudang', 'id_barang', 'id_warna', 'id_motif', 'id_grade',
                'id_kualitas', 'id_beam', 'id_songket', 'tanggal_potong', 'id_mesin', 'id_satuan_1', 'id_satuan_2'
            ]);

            $logInspectKeluar = $logInspectMasuk = $logDetail;

            if ($tipe == 'input' || $tipe == 'output') {
                $dataDetail['code'] = $logDetail['code'] = $model["code_{$tipe}"];
                $logDetail['volume_' . ($tipe == 'input' ? 'keluar' : 'masuk') . '_1'] = $request->input('volume_1', 0);
                $logDetail['volume_' . ($tipe == 'input' ? 'keluar' : 'masuk') . '_2'] = $request->input('volume_2');
            }

            if ($tipe == 'output') {
                $logInspectKeluar['code'] = $model['code_output'];
                $logInspectKeluar['volume_keluar_1'] = $request->input('volume_1', 0);
                $logInspectKeluar['volume_keluar_2'] = $request->input('volume_2');

                $logInspectMasuk['code'] = "I{$model['code_output']}";
                $logInspectMasuk['volume_masuk_1'] = $request->input('volume_1', 0);
                $logInspectMasuk['volume_masuk_2'] = $request->input('volume_2');

                $dataInspect['code'] = "I{$model['code_output']}";
            }

            if ($tipe == 'hilang') {
                $dataDetail['code'] = $logDetail['code'] = $model["code_hilang"];
                $logDetail['volume_masuk_1'] = $request->input('volume_1', 0);
                $logDetail['volume_masuk_2'] = $request->input('volume_2');
            }

            if (!$id) {
                if ($mode == 'parent') {
                    $model['parent']::create($dataParent);
                    logHistory($model['parent_name'], 'create');
                } elseif ($mode == 'detail') {
                    $idLog = LogStokPenerimaan::create($logDetail)->id;
                    $dataDetail['id_log_stok_penerimaan'] = $idLog;
                    $model['detail']::create($dataDetail);
                    if ($tipe == 'output') {
                        $idLogInspectKeluar = LogStokPenerimaan::create($logInspectKeluar)->id;
                        $idLogInspectMasuk = LogStokPenerimaan::create($logInspectMasuk)->id;
                        $dataInspect['id_log_stok_penerimaan_keluar'] = $idLogInspectKeluar;
                        $dataInspect['id_log_stok_penerimaan_masuk'] = $idLogInspectMasuk;
                        $modelInspect = getModelByProses("inspect_{$proses}");
                        $modelInspect['detail']::create($dataInspect);
                    }
                    logHistory($model['detail_name'], 'create');
                }
            } else {
                if ($mode == 'parent') {
                    $model['parent']::find($id)->update($dataParent);
                    logHistory($model['parent_name'], 'update');
                } elseif ($mode == 'detail') {
                    $detail = $model['detail']::find($id);
                    LogStokPenerimaan::find($detail->id_log_stok_penerimaan)->update($logDetail);
                    $detail->update($dataDetail);
                    logHistory($model['detail_name'], 'update');
                }
            }

            return $this->jsonResponse(true, 'Data berhasil disimpan');
        }, 5);
    }

    private function jsonResponse($success, $message, $status = 200)
    {
        return response()->json(['success' => $success, 'messages' => $message], $status);
    }

    function cekRequest($request)
    {
        $mode = $request->mode;
        if ($mode == 'parent') {
            $rules = [
                'nomor' => 'required',
                'id_supplier' => 'required|not_in:0',
            ];
        }
        if ($mode == 'detail') {
            $rules = [
                'id_grade' => 'required|not_in:0',
                'id_barang' => 'required|not_in:0',
                'id_gudang' => 'required|not_in:0',
                'id_warna' => 'required|not_in:0',
                'id_motif' => 'required|not_in:0',
                'volume_1' => 'required|numeric|gt:0|not_in:0',

            ];
        }
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
