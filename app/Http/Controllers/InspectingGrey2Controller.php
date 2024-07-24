<?php

namespace App\Http\Controllers;

use App\Helpers\Date;
use App\Models\InspectingGrey;
use App\Models\InspectingGreyDetail;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Rules\CekTotalGroup1;
use App\Rules\CekTotalGroup2;
use App\Rules\CekTotalGroup3;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class InspectingGrey2Controller extends Controller
{
    function view($id)
    {
        $inspectingGrey = InspectingGrey::find($id);
        $data['id_inspecting_grey'] = $id;
        $data['no_beam'] = $inspectingGrey->relBeam->no_beam;
        $data['no_kikw'] = $inspectingGrey->relBeam->no_kikw;
        $data['no_loom'] = $inspectingGrey->relMesin->name;
        $data['potongan'] = $inspectingGrey->volume_1;
        $data['tanggal'] = $inspectingGrey->tanggal;
        $data['data'] = $inspectingGrey;
        $data['group_1'] = InspectingGreyDetail::where('id_inspecting_grey', $id)->where('id_group', 1)->first();
        $data['group_2'] = InspectingGreyDetail::where('id_inspecting_grey', $id)->where('id_group', 2)->first();
        $data['group_3'] = InspectingGreyDetail::where('id_inspecting_grey', $id)->where('id_group', 3)->first();
        $data['kualitas_b'] = MappingKualitas::whereNotIn('id', [33, 34])->where('id_kualitas', 2)->get();
        $data['kualitas_c'] = MappingKualitas::whereNotIn('id', [33, 34])->where('id_kualitas', 3)->get();
        return view('contents.production.inspecting.inspecting_grey.detail-baru', $data);
    }
    function table($id)
    {
        $temp = InspectingGreyDetail::where('id_inspecting_grey', $id)->orderBy('id_group', 'asc');
        return DataTables::of($temp)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($i) {
                return Date::format($i->tanggal, 98);
            })
            ->addColumn('group', function ($i) {
                return $i->relGroup->name;
            })
            ->addColumn('motif', function ($i) {
                return $i->relMotif->alias;
            })
            ->addColumn('warna', function ($i) {
                return $i->relWarna->alias;
            })
            ->addColumn('barang', function ($i) {
                return $i->relBarang->name;
            })
            ->addColumn('potongan', function ($i) {
                return $i->volume_1;
            })
            ->addColumn('a', function ($i) {
                return $i->jml_grade_a;
            })
            ->addColumn('b', function ($i) {
                return $i->jml_grade_b;
            })
            ->addColumn('c', function ($i) {
                return $i->jml_grade_c;
            })
            ->rawColumns(['action'])
            ->make('true');
    }
    function getBarang()
    {
        $term = request('term');
        $parts = ($term == '') ? [] : explode(" | ", $term);
        $sql = "SELECT
            log.id_gudang,
            log.id_barang,
            barang.name nama_barang,
            log.id_warna,
            warna.alias nama_warna,
            log.id_motif,
            motif.alias nama_motif,
            log.id_grade,
            log.id_kualitas,
            log.id_beam,
            no_beam.name no_beam,
            no_kikw.name no_kikw,
            log.id_songket,
            no_kiks.name no_kiks,
            log.id_mesin,
            mesin.name nama_mesin,
            log.id_satuan_1,
            SUM(COALESCE(log.volume_masuk_1,0)) - SUM(COALESCE(log.volume_keluar_1,0)) total,
            log.code,
            log.tanggal_potong,
            TO_CHAR(log.tanggal_potong, 'DD-MM-YYYY') tanggal_potong_text
            FROM log_stok_penerimaan AS log
            LEFT JOIN tbl_barang AS barang ON barang.id = log.id_barang
            LEFT JOIN tbl_warna AS warna ON warna.id = log.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = log.id_motif
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = log.id_mesin
            LEFT JOIN tbl_beam AS beam ON beam.id = log.id_beam
            LEFT JOIN tbl_beam AS songket ON songket.id = log.id_songket
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
            WHERE log.deleted_at IS NULL AND log.code ='BBG' AND log.id_gudang = 5
            GROUP BY log.id_gudang, log.id_barang, log.id_warna, log.id_motif, log.id_grade, log.id_kualitas, log.id_beam, log.id_songket, log.id_mesin, log.id_satuan_1, log.code, barang.name, warna.alias, motif.alias, no_beam.name, no_kikw.name, no_kiks.name, mesin.name, log.tanggal_potong";
        $data = DB::table(DB::raw("({$sql}) as data"))
            ->where('total', '>', 0)
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('no_kikw', $parts)
                        ->orWhereIn('no_kiks', $parts)
                        ->orWhereIn('no_beam', $parts)
                        ->orWhereIn('nama_motif', $parts)
                        ->orWhereIn('nama_warna', $parts)
                        ->orWhereIn('nama_barang', $parts)
                        ->orWhereIn('nama_mesin', $parts)
                        ->orWhereIn(DB::raw("TO_CHAR(tanggal_potong, 'DD-MM-YYYY')"), $parts);
                });
            })->paginate(5);
        return $data;
    }
    function getData($id)
    {
        $sql = "SELECT
            ig.id,
            ig.tanggal,
            ig.id_barang,
            barang.name nama_barang,
            ig.id_warna,
            warna.alias nama_warna,
            ig.id_motif,
            motif.alias nama_motif,
            ig.id_beam,
            no_beam.name no_beam,
            no_kikw.name no_kikw,
            ig.id_songket,
            no_kiks.name no_kiks,
            ig.id_mesin,
            mesin.name nama_mesin,
            ig.code code_masuk,
            log.code code_keluar,
            sisa.total + ig.volume_1 total,
            ig.volume_1,
            ig.tanggal_potong,
            TO_CHAR(ig.tanggal_potong, 'DD-MM-YYYY') tanggal_potong_text,
            group_1.volume_1 group_1,
            group_1.jml_grade_a group_1_grade_a,
            group_1.jml_grade_b group_1_grade_b,
            group_1.jml_grade_c group_1_grade_c,
            group_1.jml_grade_a + group_1.jml_grade_b + group_1.jml_grade_c group_1_grade_total,
            group_2.volume_1 group_2,
            group_2.jml_grade_a group_2_grade_a,
            group_2.jml_grade_b group_2_grade_b,
            group_2.jml_grade_c group_2_grade_c,
            group_2.jml_grade_a + group_2.jml_grade_b + group_2.jml_grade_c group_2_grade_total,
            group_3.volume_1 group_3,
            group_3.jml_grade_a group_3_grade_a,
            group_3.jml_grade_b group_3_grade_b,
            group_3.jml_grade_c group_3_grade_c,
            group_3.jml_grade_a + group_3.jml_grade_b + group_3.jml_grade_c group_3_grade_total,
            ig.panjang_sarung,
            ig.keterangan
            FROM tbl_inspecting_grey AS ig
            LEFT JOIN tbl_beam AS beam ON beam.id = ig.id_beam
            LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
            LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
            LEFT JOIN tbl_beam AS songket ON songket.id = ig.id_songket
            LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
            LEFT JOIN tbl_barang AS barang ON barang.id = ig.id_barang
            LEFT JOIN tbl_mesin AS mesin ON mesin.id = ig.id_mesin
            LEFT JOIN tbl_warna AS warna ON warna.id = ig.id_warna
            LEFT JOIN tbl_motif AS motif ON motif.id = ig.id_motif
            LEFT JOIN log_stok_penerimaan AS log ON log.id = ig.id_log_stok_penerimaan_keluar
            LEFT JOIN tbl_inspecting_grey_detail AS group_1 ON group_1.id_inspecting_grey = ig.id AND group_1.id_group = 1
            LEFT JOIN tbl_inspecting_grey_detail AS group_2 ON group_2.id_inspecting_grey = ig.id AND group_2.id_group = 2
            LEFT JOIN tbl_inspecting_grey_detail AS group_3 ON group_3.id_inspecting_grey = ig.id AND group_3.id_group = 3
            LEFT JOIN
            (
                SELECT
                id_barang,id_warna,id_motif,id_beam,id_songket,id_mesin,code,
                SUM(COALESCE(log.volume_masuk_1,0)) - SUM(COALESCE(log.volume_keluar_1,0)) total
                FROM log_stok_penerimaan AS log
                WHERE log.deleted_at IS NULL AND log.code ='BBG' AND log.id_gudang = 5
                GROUP BY log.id_gudang, log.id_barang, log.id_warna, log.id_motif, log.id_grade, log.id_kualitas, log.id_beam, log.id_songket, log.id_mesin, log.id_satuan_1, log.code, log.tanggal_potong
            ) AS sisa ON sisa.id_barang = ig.id_barang AND sisa.id_warna = ig.id_warna AND sisa.id_motif = ig.id_motif AND sisa.id_beam = ig.id_beam AND sisa.id_songket = ig.id_songket AND sisa.id_mesin = ig.id_mesin AND sisa.code = log.code
            WHERE ig.id = $id
        ";
        $data = DB::table(DB::raw("({$sql}) as data"))->first();
        return $data;
    }
    function simpanKualitas($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $group_1 = [];
            $group_2 = [];
            $group_3 = [];
            $jumlah_kualitas = MappingKualitas::whereNotIn('id', [33, 34])->get();
            foreach ($jumlah_kualitas as $i) {
                $val1 = 'group_1_kualitas_' . $i->id;
                $group_1['jml_kualitas_' . $i->id] = $request->$val1 ?? 0;
                $val2 = 'group_2_kualitas_' . $i->id;
                $group_2['jml_kualitas_' . $i->id] = $request->$val2 ?? 0;
                $val3 = 'group_3_kualitas_' . $i->id;
                $group_3['jml_kualitas_' . $i->id] = $request->$val3 ?? 0;
            }
            InspectingGreyDetail::where('id_inspecting_grey', $request->id)->where('id_group', 1)->update($group_1);
            InspectingGreyDetail::where('id_inspecting_grey', $request->id)->where('id_group', 2)->update($group_2);
            InspectingGreyDetail::where('id_inspecting_grey', $request->id)->where('id_group', 3)->update($group_3);
            logHistory('InspectingGreyDetail', 'update');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    function simpan(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $barang = $request->barang;
                $barang['id_songket'] = ($barang['id_songket'] == 'null') ? null : $barang['id_songket'];
                $barang['tanggal_potong'] = ($barang['tanggal_potong'] == 'null') ? null : $barang['tanggal_potong'];
                $log = [
                    'tanggal' => $request->tanggal,
                    'id_gudang' => $request->id_gudang,
                    'id_satuan_1' => $request->id_satuan_1,
                    'id_barang' => $barang['id_barang'],
                    'id_warna' => $barang['id_warna'],
                    'id_motif' => $barang['id_motif'],
                    'id_beam' => $barang['id_beam'],
                    'id_mesin' => $barang['id_mesin'],
                    'id_songket' => $barang['id_songket'],
                    'tanggal_potong' => $barang['tanggal_potong'],
                ];

                $logKeluar = $log;
                $logKeluar['code'] = 'BBG';
                $logKeluar['volume_masuk_1'] = 0;
                $logKeluar['volume_keluar_1'] = $request->volume_1;
                $logKeluarId = LogStokPenerimaan::create($logKeluar)->id;

                $logMasuk = $log;
                $logMasuk['code'] = 'BGIG';
                $logMasuk['volume_masuk_1'] = $request->volume_1;
                $logMasuk['volume_keluar_1'] = 0;
                $logMasukId = LogStokPenerimaan::create($logMasuk)->id;

                $inspectingGrey = [
                    'tanggal' => $log['tanggal'],
                    'id_beam' => $log['id_beam'],
                    'id_songket' => $log['id_songket'],
                    'tanggal_potong' => $log['tanggal_potong'],
                    'id_mesin' => $log['id_mesin'],
                    'id_barang' => $log['id_barang'],
                    'id_motif' => $log['id_motif'],
                    'id_warna' => $log['id_warna'],
                    'id_gudang' => $log['id_gudang'],
                    'id_satuan_1' => $log['id_satuan_1'],
                    'volume_1' => $request->volume_1,
                    'code' => 'BGIG',
                    'id_log_stok_penerimaan_keluar' => $logKeluarId,
                    'id_log_stok_penerimaan_masuk' => $logMasukId,
                    'panjang_sarung' => $request['panjang_sarung'],
                    'keterangan' => $request['keterangan'],
                ];
                $inspectingGreyId = InspectingGrey::create($inspectingGrey)->id;

                for ($i = 1; $i <= 3; $i++) {
                    $data = [
                        'tanggal' => $log['tanggal'],
                        'id_beam' => $log['id_beam'],
                        'id_mesin' => $log['id_mesin'],
                        'id_motif' => $log['id_motif'],
                        'id_warna' => $log['id_warna'],
                        'id_barang' => $log['id_barang'],
                        'id_gudang' => $log['id_gudang'],
                        'id_satuan_1' => $log['id_satuan_1'],
                        'id_group' => $i,
                        'code' => 'BGIG',
                        'id_inspecting_grey' => $inspectingGreyId,
                        'id_songket' => $log['id_songket'],
                        'tanggal_potong' => $log['tanggal_potong'],
                    ];
                    $group = 'group_' . $i;
                    $data['volume_1'] = $request[$group];
                    foreach (range('a', 'c') as $j) {
                        $grade = '_grade_' . $j;
                        $data['jml' . $grade] = $request[$group . $grade];
                    }
                    InspectingGreyDetail::create($data);
                }

                logHistory('InspectingGrey', 'create');
                logHistory('InspectingGreyDetail', 'create');

                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        }, 5);
    }
    function update(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $dataInspectingGrey = InspectingGrey::find($request->id);
                $dataInspectingGreyDetail['group_1'] = InspectingGreyDetail::where([['id_inspecting_grey', $request->id], ['id_group', 1]])->first();
                $dataInspectingGreyDetail['group_2'] = InspectingGreyDetail::where([['id_inspecting_grey', $request->id], ['id_group', 2]])->first();
                $dataInspectingGreyDetail['group_3'] = InspectingGreyDetail::where([['id_inspecting_grey', $request->id], ['id_group', 3]])->first();
                $dataLogKeluar = LogStokPenerimaan::find($dataInspectingGrey->id_log_stok_penerimaan_keluar);
                $dataLogMasuk = LogStokPenerimaan::find($dataInspectingGrey->id_log_stok_penerimaan_masuk);

                $barang = $request->barang;
                $barang['id_songket'] = ($barang['id_songket'] == 'null') ? null : $barang['id_songket'];
                $barang['tanggal_potong'] = ($barang['tanggal_potong'] == 'null') ? null : $barang['tanggal_potong'];
                $log = [
                    'tanggal' => $request->tanggal,
                    'id_gudang' => $request->id_gudang,
                    'id_satuan_1' => $request->id_satuan_1,
                    'id_barang' => $barang['id_barang'],
                    'id_warna' => $barang['id_warna'],
                    'id_motif' => $barang['id_motif'],
                    'id_beam' => $barang['id_beam'],
                    'id_mesin' => $barang['id_mesin'],
                    'id_songket' => $barang['id_songket'],
                    'tanggal_potong' => $barang['tanggal_potong'],
                ];

                $logKeluar = $log;
                $logKeluar['code'] = 'BBG';
                $logKeluar['volume_masuk_1'] = 0;
                $logKeluar['volume_keluar_1'] = $request->volume_1;
                $dataLogKeluar->update($logKeluar);

                $logMasuk = $log;
                $logMasuk['code'] = 'BGIG';
                $logMasuk['volume_masuk_1'] = $request->volume_1;
                $logMasuk['volume_keluar_1'] = 0;
                $dataLogMasuk->update($logMasuk);

                $inspectingGrey = [
                    'tanggal' => $log['tanggal'],
                    'id_beam' => $log['id_beam'],
                    'id_songket' => $log['id_songket'],
                    'tanggal_potong' => $log['tanggal_potong'],
                    'id_mesin' => $log['id_mesin'],
                    'id_barang' => $log['id_barang'],
                    'id_motif' => $log['id_motif'],
                    'id_warna' => $log['id_warna'],
                    'id_gudang' => $log['id_gudang'],
                    'id_satuan_1' => $log['id_satuan_1'],
                    'volume_1' => $request->volume_1,
                    'code' => 'BGIG',
                    'panjang_sarung' => $request['panjang_sarung'],
                    'keterangan' => $request['keterangan'],
                ];
                $dataInspectingGrey->update($inspectingGrey);

                for ($i = 1; $i <= 3; $i++) {
                    $data = [
                        'tanggal' => $log['tanggal'],
                        'id_beam' => $log['id_beam'],
                        'id_mesin' => $log['id_mesin'],
                        'id_motif' => $log['id_motif'],
                        'id_warna' => $log['id_warna'],
                        'id_barang' => $log['id_barang'],
                        'id_gudang' => $log['id_gudang'],
                        'id_satuan_1' => $log['id_satuan_1'],
                        'code' => 'BGIG',
                        'id_songket' => $log['id_songket'],
                        'tanggal_potong' => $log['tanggal_potong'],
                    ];
                    $group = 'group_' . $i;
                    $data['volume_1'] = $request[$group];
                    foreach (range('a', 'c') as $j) {
                        $grade = '_grade_' . $j;
                        $data['jml' . $grade] = $request[$group . $grade];
                    }
                    $dataInspectingGreyDetail[$group]->update($data);
                }

                logHistory('InspectingGrey', 'update');
                logHistory('InspectingGreyDetail', 'update');

                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        }, 5);
    }
    function hapus($id)
    {
        DB::beginTransaction();
        try {
            $logKeluarId = InspectingGrey::find($id)->id_log_stok_penerimaan_keluar;
            $logMasukId = InspectingGrey::find($id)->id_log_stok_penerimaan_masuk;

            LogStokPenerimaan::find($logKeluarId)->delete();
            LogStokPenerimaan::find($logMasukId)->delete();

            InspectingGreyDetail::where('id_inspecting_grey', $id)->delete();
            InspectingGrey::find($id)->delete();

            logHistory('InspectingGreyDetail', 'delete');
            logHistory('InspectingGrey', 'delete');
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
            'barang' => 'required',
            'volume_1' => 'required|numeric|gt:0|not_in:0',
        ];
        $rules['group_1'] = 'required|numeric';
        $rules['group_1_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup1];
        $rules['group_2'] = 'required|numeric';
        $rules['group_2_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup2];
        $rules['group_3'] = 'required|numeric';
        $rules['group_3_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup3];

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
