<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\JahitSambung;
use App\Models\JahitSambungDetail;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;

class JahitSambungController extends Controller
{
    private static $model = 'JahitSambung';
    private static $modelDetail = 'JahitSambungDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Jahit Sambung', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('finishing', 'jahit sambung', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.finishing.jahit_sambung.index', $data);
    }
    public function view()
    {
        $data['grade'] = $this->getGrade();
        return view('contents.production.finishing.jahit_sambung.parent', $data);
    }
    public function table()
    {
        $temp = JahitSambungDetail::orderBy('created_at', 'desc');
        $column = [
            'tanggal',
            'tanggal_potong',
            'no_kikw',
            'no_kiks',
            'grade',
            'barang',
            'warna',
            'motif',
            'gudang',
            'mesin',
            'action' => ['edit', 'hapus', 'validasi']
        ];
        $position = ['non_jasa_luar', 'parent'];
        return getDataTable($temp, $column, 'jahit_sambung', $position);
    }
    function cetak(Request $request)
    {
        $tgl = $request->tgl;
        $sql = "SELECT
            data.*,
            no_beam.name no_beam,
            no_kikw.name no_kikw,
            no_kiks.name no_kiks,
            mesin.name nama_mesin,
            barang.name nama_barang,
            motif.alias nama_motif,
            warna.alias nama_warna
            FROM
            (
                SELECT
                p1.id_p1,
                p1.nomor nomor_p1,
                p1.tanggal tanggal_p1,
                p1.id_supplier,
                p1.nama_supplier,
                js.tanggal tanggal_js,
                js.tanggal_potong,
                js.id_beam,
                js.id_songket,
                js.id_mesin,
                js.id_barang,
                js.id_motif,
                js.id_warna,
                js.pcs
                FROM
                (
                    SELECT
                    tanggal,
                    COALESCE(tanggal_potong,'1997-10-23') tanggal_potong,
                    COALESCE(id_beam,0) id_beam,
                    COALESCE(id_songket,0) id_songket,
                    COALESCE(id_mesin,0) id_mesin,
                    id_barang,
                    id_motif,
                    id_warna,
                    SUM(volume_1) pcs
                    FROM tbl_jahit_sambung_detail
                    GROUP BY tanggal, tanggal_potong, id_beam, id_songket, id_mesin, id_barang, id_motif, id_warna
                ) AS js
                LEFT JOIN
                (
                    SELECT
                    de.id_p1,
                    pa.nomor,
                    pa.id_supplier,
                    supplier.name nama_supplier,
                    de.tanggal,
                    COALESCE(de.tanggal_potong,'1997-10-23') tanggal_potong,
                    COALESCE(id_beam,0) id_beam,
                    COALESCE(id_songket,0) id_songket,
                    COALESCE(id_mesin,0) id_mesin,
                    de.id_barang,
                    de.id_motif,
                    de.id_warna,
                    SUM(de.volume_1) pcs
                    FROM tbl_p1_detail AS de
                    LEFT JOIN tbl_p1 AS pa ON pa.id = de.id_p1
                    LEFT JOIN tbl_supplier AS supplier ON supplier.id = pa.id_supplier
                    WHERE de.deleted_at IS NULL
                    GROUP BY de.id_p1, pa.nomor, pa.id_supplier, supplier.name, de.tanggal, de.tanggal_potong, de.id_beam, de.id_songket, de.id_mesin, de.id_barang, de.id_motif, de.id_warna
                ) AS p1 ON js.tanggal = p1.tanggal AND js.tanggal_potong = p1.tanggal_potong  AND js.id_beam = p1.id_beam  AND js.id_songket = p1.id_songket  AND js.id_mesin = p1.id_mesin AND js.id_barang = p1.id_barang AND js.id_motif = p1.id_motif AND js.id_warna = p1.id_warna  AND js.pcs = p1.pcs
            ) AS data
            LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_beam AS songket ON songket.id = data.id_songket
            LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
            LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
            LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
        ";
        $data['data'] = DB::table(DB::raw("({$sql}) as data"))->where('tanggal_js', $tgl)->orderByRaw('nama_barang asc, nama_motif asc, nama_mesin asc, nama_warna asc')->get();
        $data['judul'] = 'DATA JAHIT PENYAMBUNGAN SARUNG GREY Tanggal ' . tglIndo($tgl);
        $pdf = PDF::loadView('contents.production.finishing.jahit_sambung.cetak', $data)->setPaper('a4', 'landscape');
        return $pdf->stream($data['judul']);
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
                    $dataLogOut['code'] = 'BGF';

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'JS';

                    $data['code'] = 'JS';

                    $logIdOut = LogStokPenerimaan::create($dataLogOut)->id;
                    $data['id_log_stok_penerimaan_keluar'] = $logIdOut;

                    $logIdIn = LogStokPenerimaan::create($dataLogIn)->id;
                    $data['id_log_stok_penerimaan_masuk'] = $logIdIn;

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    JahitSambungDetail::create($data);
                    logHistory(self::$modelDetail, 'create');
                } else {
                    $inspect = JahitSambungDetail::find($id);
                    $id_log_stok_penerimaan_keluar = $inspect->id_log_stok_penerimaan_keluar;
                    $id_log_stok_penerimaan_masuk = $inspect->id_log_stok_penerimaan_masuk;

                    $dataLogOut = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogOut['id_grade'] = $data['id_grade_awal'];
                    $dataLogOut['volume_keluar_1'] = $data['volume_1'];
                    $dataLogOut['code'] = 'BGF';

                    $dataLogIn = unsetMultiKeys(['id_grade_awal'], $dataLog);
                    $dataLogIn['volume_masuk_1'] = $data['volume_1'];
                    $dataLogIn['code'] = 'JS';

                    $data['code'] = 'JS';
                    $data['updated_by'] = Auth::id();

                    LogStokPenerimaan::find($id_log_stok_penerimaan_keluar)->update($dataLogOut);

                    LogStokPenerimaan::find($id_log_stok_penerimaan_masuk)->update($dataLogIn);

                    $data = unsetMultiKeys(['id_grade_awal'], $data);
                    JahitSambungDetail::find($id)->update($data);
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

            $logIdOut = JahitSambungDetail::find($id)->id_log_stok_penerimaan_keluar;
            $logIdIn = JahitSambungDetail::find($id)->id_log_stok_penerimaan_masuk;
            JahitSambungDetail::find($id)->delete();
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
    function getData(Request $request)
    {
        $data = getDataJasaDalam($request, 'jahit_sambung');
        return $data;
    }
    function getBarang(Request $request)
    {
        $term = $request->input('term');
        $gudang = $request->id_gudang ?? 0;
        $condition = [
            'code' => 'BGF',
            'id_satuan_1' => 4,
            'id_gudang' => $gudang ?? 0
        ];
        return getBarangJasaLuar('log_stok', 'log_stok_penerimaan', $condition, $term);
    }
    function getGudang(Request $request)
    {
        $term = $request->input('term');
        $atribut = [
            'code' => 'BGF',
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
        return getStokJasaLuar($request, 'jahit_sambung');
    }
}
