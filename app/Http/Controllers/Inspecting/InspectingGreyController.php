<?php

namespace App\Http\Controllers\Inspecting;

use App\Exports\ExportExcelFromView;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\Date;
use App\Models\Barang;
use App\Models\Beam;
use App\Models\Group;
use App\Models\InspectingGreyDetail;
use App\Models\Kualitas;
use App\Models\LogStokPenerimaan;
use App\Models\MappingKualitas;
use App\Models\Tenun;
use App\Models\TenunDetail;
use App\Rules\CekTotal;
use App\Rules\CekTotalGroup1;
use App\Rules\CekTotalGroup2;
use App\Rules\CekTotalGroup3;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class InspectingGreyController extends Controller
{
    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Inspecting', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Inspect Grey', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('inspecting', 'inspect grey', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.inspecting.inspecting_grey.index', $data);
    }

    public function view($mode, $id = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.inspecting.inspecting_grey.parent');
        } elseif ($mode == 'detail') {
            $tenun = TenunDetail::find($id);
            $data['id_tenun_detail'] = $id;
            $data['no_beam'] = $tenun->relBeam->no_beam;
            $data['no_kikw'] = $tenun->relBeam->no_kikw;
            $data['no_loom'] = $tenun->relMesin->name;
            $data['potongan'] = $tenun->volume_1;
            $data['tanggal'] = $tenun->tanggal;
            $data['data'] = $tenun;
            $data['group_1'] = InspectingGreyDetail::where('id_tenun_detail', $id)->where('id_group', 1)->first();
            $data['group_2'] = InspectingGreyDetail::where('id_tenun_detail', $id)->where('id_group', 2)->first();
            $data['group_3'] = InspectingGreyDetail::where('id_tenun_detail', $id)->where('id_group', 3)->first();
            $data['kualitas_b'] = MappingKualitas::whereNotIn('id', [33, 34])->where('id_kualitas', 2)->get();
            $data['kualitas_c'] = MappingKualitas::whereNotIn('id', [33, 34])->where('id_kualitas', 3)->get();
            return view('contents.production.inspecting.inspecting_grey.detail', $data);
        }
    }

    public function table(Request $request, $mode, $id = null)
    {
        if ($mode == 'parent') {
            $barang = ($request->barang == 'semua') ? null : $request->barang;
            $kikw = ($request->kikw == 'semua') ? null : $request->kikw;
            $tgl = ($request->tgl == '') ? null : $request->tgl;
            $sql = "SELECT
                NULL id_inspecting_grey,
                tenun.id id_tenun_detail,
                null tanggal_potong,
                tenun.tanggal,
                tenun.id_beam,
                no_beam.name no_beam,
                no_kikw.name no_kikw,
                tenun.id_songket,
                no_kiks.name no_kiks,
                tenun.id_barang,
                barang.name nama_barang,
                tenun.id_mesin,
                mesin.name nama_mesin,
                tenun.id_warna,
                warna.alias nama_warna,
                tenun.id_motif,
                motif.alias nama_motif,
                tenun.volume_1 potong_total,
                group_1.volume_1 potong_1,
                group_2.volume_1 potong_2,
                group_3.volume_1 potong_3,
                tenun.validated_at,
                null panjang_sarung,
                null keterangan
                FROM tbl_tenun_detail AS tenun
                LEFT JOIN tbl_beam AS beam ON beam.id = tenun.id_beam
                LEFT JOIN tbl_nomor_beam AS no_beam ON no_beam.id = beam.id_nomor_beam
                LEFT JOIN tbl_nomor_kikw AS no_kikw ON no_kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_beam AS songket ON songket.id = tenun.id_songket
                LEFT JOIN tbl_nomor_kikw AS no_kiks ON no_kiks.id = songket.id_nomor_kikw
                LEFT JOIN tbl_barang AS barang ON barang.id = tenun.id_barang
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = tenun.id_mesin
                LEFT JOIN tbl_warna AS warna ON warna.id = tenun.id_warna
                LEFT JOIN tbl_motif AS motif ON motif.id = tenun.id_motif
                LEFT JOIN tbl_inspecting_grey_detail AS group_1 ON group_1.id_tenun_detail = tenun.id AND group_1.id_group = 1
                LEFT JOIN tbl_inspecting_grey_detail AS group_2 ON group_2.id_tenun_detail = tenun.id AND group_2.id_group = 2
                LEFT JOIN tbl_inspecting_grey_detail AS group_3 ON group_3.id_tenun_detail = tenun.id AND group_3.id_group = 3
                WHERE tenun.id_pengiriman_barang_detail IS NULL
                AND tenun.code = 'BG'
                AND tenun.deleted_at IS NULL

                UNION ALL

                SELECT
                ig.id id_inspecting_grey,
                NULL id_tenun_detail,
                ig.tanggal_potong,
                ig.tanggal,
                ig.id_beam,
                no_beam.name no_beam,
                no_kikw.name no_kikw,
                ig.id_songket,
                no_kiks.name no_kiks,
                ig.id_barang,
                barang.name nama_barang,
                ig.id_mesin,
                mesin.name nama_mesin,
                ig.id_warna,
                warna.alias nama_warna,
                ig.id_motif,
                motif.alias nama_motif,
                ig.volume_1 potong_total,
                group_1.volume_1 potong_1,
                group_2.volume_1 potong_2,
                group_3.volume_1 potong_3,
                ig.validated_at,
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
                LEFT JOIN tbl_inspecting_grey_detail AS group_1 ON group_1.id_inspecting_grey = ig.id AND group_1.id_group = 1
                LEFT JOIN tbl_inspecting_grey_detail AS group_2 ON group_2.id_inspecting_grey = ig.id AND group_2.id_group = 2
                LEFT JOIN tbl_inspecting_grey_detail AS group_3 ON group_3.id_inspecting_grey = ig.id AND group_3.id_group = 3
                WHERE ig.code = 'BGIG' AND ig.deleted_at IS NULL
            ";
            $temp = DB::table(DB::raw("({$sql}) as data"))
                ->when($barang, function ($q) use ($barang) {
                    return $q->where('id_barang', $barang);
                })->when($kikw, function ($q) use ($kikw) {
                    return $q->where('id_beam', $kikw);
                })->when($tgl, function ($q) use ($tgl) {
                    return $q->where('tanggal', $tgl);
                })
                ->orderBy('id_tenun_detail', 'desc')->orderBy('id_inspecting_grey', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('tanggal_potong', function ($i) {
                    if ($i->tanggal_potong) {
                        return Date::format($i->tanggal_potong, 98);
                    } else {
                        return '';
                    }
                })
                ->addColumn('no_beam', function ($i) {
                    return $i->no_beam;
                })
                ->addColumn('no_kikw', function ($i) {
                    return $i->no_kikw;
                })
                ->addColumn('no_kiks', function ($i) {
                    return $i->no_kiks;
                })
                ->addColumn('no_loom', function ($i) {
                    return $i->nama_mesin;
                })
                ->addColumn('barang', function ($i) {
                    $barang = $i->nama_barang ? $i->nama_barang . ' | ' : '';
                    $warna = $i->nama_warna ? $i->nama_warna . ' | ' : '';
                    $motif = $i->nama_motif ? $i->nama_motif . '' : '';
                    return $barang . $warna . $motif;
                })
                ->addColumn('potongan', function ($i) {
                    $data = $i->potong_total;
                    return $data;
                })
                ->addColumn('pergroup', function ($i) {
                    $data = '( ' . $i->potong_1 . ', ' . $i->potong_2 . ', ' . $i->potong_3 . ' )';
                    return $data;
                })
                ->addColumn('action', function ($i) {
                    $temp = $i->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                    ];
                    if ($i->id_inspecting_grey) {
                        $validasi['model'] = 'InspectingGrey';
                        $addData = 'data-id_inspecting_grey="' . $i->id_inspecting_grey . '"';
                        $action = actionBtn($i->id_inspecting_grey, true, true, true, $validasi, null, $addData);
                    } else {
                        $validasi['model'] = 'TenunDetail';
                        $addData = 'data-id_inspecting_grey="' . 0 . '"';
                        $action = actionBtn($i->id_tenun_detail, true, true, true, $validasi, null, $addData);
                    }
                    return $action;
                })
                ->addColumn('panjang_sarung', function ($i) {
                    $data = $i->panjang_sarung;
                    return $data;
                })
                ->addColumn('keterangan', function ($i) {
                    $data = $i->keterangan;
                    return $data;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else if ($mode == 'detail') {
            $temp = InspectingGreyDetail::where('id_tenun_detail', $id)->where('code', 'BGIG')->orderBy('created_at', 'desc');
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
    }
    public function simpan(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $id = $request->id;
            $mode = $request->mode;
            $rule = $this->cekRequest($request, $mode);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        $tempTenun = [
                            'id_beam' => $request->id_beam,
                            'tanggal' => $request->tanggal,
                            'id_mesin' => $request->id_mesin,
                            'id_motif' => $request->id_motif,
                            'id_warna' => $request->id_warna,
                            'id_barang' => $request->id_barang,
                            'id_gudang' => 4,
                            'id_satuan_1' => 4,
                            'code' => 'BG'
                        ];

                        $logTenun = $tempTenun;
                        $logTenun['volume_masuk_1'] = $request->total;
                        $logTenunId = LogStokPenerimaan::create($logTenun)->id;

                        $tenun = $tempTenun;
                        $tenun['id_tenun'] = $request->id_tenun;
                        $tenun['volume_1'] = $request->total;
                        $tenun['id_log_stok_penerimaan'] = $logTenunId;
                        $tenun['id_lusi_detail'] = $request->id_lusi;

                        $tenunId = TenunDetail::create($tenun)->id;
                        logHistory('TenunDetail', 'create');

                        $temp = [
                            'id_beam' => $request->id_beam,
                            'tanggal' => $request->tanggal,
                            'id_mesin' => $request->id_mesin,
                            'id_motif' => $request->id_motif,
                            'id_warna' => $request->id_warna,
                            'id_barang' => $request->id_barang,
                            'id_gudang' => 5,
                            'id_satuan_1' => 4,
                            'code' => 'BGIG'
                        ];

                        $LogInspecting1 = $temp;
                        $LogInspecting1['volume_masuk_1'] = $request->group_1;
                        $LogInspecting1Id = LogStokPenerimaan::create($LogInspecting1)->id;
                        $inspecting1 = $temp;
                        $inspecting1['id_tenun_detail'] = $tenunId;
                        $inspecting1['id_group'] = 1;
                        $inspecting1['volume_1'] = $request->group_1;
                        $inspecting1['jml_grade_a'] = $request->group_1_grade_a;
                        $inspecting1['jml_grade_b'] = $request->group_1_grade_b;
                        $inspecting1['jml_grade_c'] = $request->group_1_grade_c;
                        $inspecting1['id_log_stok_penerimaan'] = $LogInspecting1Id;
                        InspectingGreyDetail::create($inspecting1);

                        $LogInspecting2 = $temp;
                        $LogInspecting2['volume_masuk_1'] = $request->group_2;
                        $LogInspecting2Id = LogStokPenerimaan::create($LogInspecting2)->id;
                        $inspecting2 = $temp;
                        $inspecting2['id_tenun_detail'] = $tenunId;
                        $inspecting2['id_group'] = 2;
                        $inspecting2['volume_1'] = $request->group_2;
                        $inspecting2['jml_grade_a'] = $request->group_2_grade_a;
                        $inspecting2['jml_grade_b'] = $request->group_2_grade_b;
                        $inspecting2['jml_grade_c'] = $request->group_2_grade_c;
                        $inspecting2['id_log_stok_penerimaan'] = $LogInspecting2Id;
                        InspectingGreyDetail::create($inspecting2);

                        $LogInspecting3 = $temp;
                        $LogInspecting3['volume_masuk_1'] = $request->group_3;
                        $LogInspecting3Id = LogStokPenerimaan::create($LogInspecting3)->id;
                        $inspecting3 = $temp;
                        $inspecting3['id_tenun_detail'] = $tenunId;
                        $inspecting3['id_group'] = 3;
                        $inspecting3['volume_1'] = $request->group_3;
                        $inspecting3['jml_grade_a'] = $request->group_3_grade_a;
                        $inspecting3['jml_grade_b'] = $request->group_3_grade_b;
                        $inspecting3['jml_grade_c'] = $request->group_3_grade_c;
                        $inspecting3['id_log_stok_penerimaan'] = $LogInspecting3Id;
                        InspectingGreyDetail::create($inspecting3);
                        logHistory('InspectingGreyDetail', 'create');
                    } else if ($mode == 'detail') {
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
                        InspectingGreyDetail::where('id_tenun_detail', $request->id_tenun_detail)->where('id_group', 1)->update($group_1);
                        InspectingGreyDetail::where('id_tenun_detail', $request->id_tenun_detail)->where('id_group', 2)->update($group_2);
                        InspectingGreyDetail::where('id_tenun_detail', $request->id_tenun_detail)->where('id_group', 3)->update($group_3);
                        logHistory('InspectingGreyDetail', 'update');
                    }
                } else {
                    if ($mode == 'parent') {
                        $tempTenun = [
                            'id_beam' => $request->id_beam,
                            'tanggal' => $request->tanggal,
                            'id_mesin' => $request->id_mesin,
                            'id_motif' => $request->id_motif,
                            'id_warna' => $request->id_warna,
                            'id_barang' => $request->id_barang,
                            'id_gudang' => 4,
                            'id_satuan_1' => 4,
                            'code' => 'BG'
                        ];

                        $logTenun = $tempTenun;
                        $logTenun['volume_masuk_1'] = $request->total;
                        $editTenun = TenunDetail::find($id);
                        LogStokPenerimaan::find($editTenun->id_log_stok_penerimaan)->update($logTenun);

                        $tenun = $tempTenun;
                        $tenun['id_tenun'] = $request->id_tenun;
                        $tenun['volume_1'] = $request->total;
                        $tenun['id_lusi_detail'] = $request->id_lusi;
                        $tenun['updated_by'] = Auth::id();

                        $editTenun->update($tenun);
                        logHistory('TenunDetail', 'update');

                        $temp = [
                            'id_beam' => $request->id_beam,
                            'tanggal' => $request->tanggal,
                            'id_mesin' => $request->id_mesin,
                            'id_motif' => $request->id_motif,
                            'id_warna' => $request->id_warna,
                            'id_barang' => $request->id_barang,
                            'id_gudang' => 5,
                            'id_satuan_1' => 4,
                            'code' => 'BGIG'
                        ];

                        $LogInspecting1 = $temp;
                        $LogInspecting1['volume_masuk_1'] = $request->group_1;
                        $editInspecting1 = InspectingGreyDetail::find($request->id_group_1);
                        LogStokPenerimaan::find($editInspecting1->id_log_stok_penerimaan)->update($LogInspecting1);
                        $inspecting1 = $temp;
                        $inspecting1['id_group'] = 1;
                        $inspecting1['volume_1'] = $request->group_1;
                        $inspecting2['jml_grade_a'] = $request->group_1_grade_a;
                        $inspecting2['jml_grade_b'] = $request->group_1_grade_b;
                        $inspecting2['jml_grade_c'] = $request->group_1_grade_c;
                        $inspecting1['updated_by'] = Auth::id();
                        $editInspecting1->update($inspecting1);

                        $LogInspecting2 = $temp;
                        $LogInspecting2['volume_masuk_1'] = $request->group_2;
                        $editInspecting2 = InspectingGreyDetail::find($request->id_group_2);
                        LogStokPenerimaan::find($editInspecting2->id_log_stok_penerimaan)->update($LogInspecting2);
                        $inspecting2 = $temp;
                        $inspecting2['id_group'] = 2;
                        $inspecting2['volume_1'] = $request->group_2;
                        $inspecting2['jml_grade_a'] = $request->group_2_grade_a;
                        $inspecting2['jml_grade_b'] = $request->group_2_grade_b;
                        $inspecting2['jml_grade_c'] = $request->group_2_grade_c;
                        $inspecting2['updated_by'] = Auth::id();
                        $editInspecting2->update($inspecting2);

                        $LogInspecting3 = $temp;
                        $LogInspecting3['volume_masuk_1'] = $request->group_3;
                        $editInspecting3 = InspectingGreyDetail::find($request->id_group_3);
                        LogStokPenerimaan::find($editInspecting3->id_log_stok_penerimaan)->update($LogInspecting3);
                        $inspecting3 = $temp;
                        $inspecting3['id_group'] = 3;
                        $inspecting3['volume_1'] = $request->group_3;
                        $inspecting2['jml_grade_a'] = $request->group_3_grade_a;
                        $inspecting2['jml_grade_b'] = $request->group_3_grade_b;
                        $inspecting2['jml_grade_c'] = $request->group_3_grade_c;
                        $inspecting3['updated_by'] = Auth::id();
                        $editInspecting3->update($inspecting3);
                        logHistory('InspectingGreyDetail', 'update');
                    }
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    function hapus($mode, $id)
    {
        DB::beginTransaction();
        try {
            if ($mode == 'parent') {
                $tenun = TenunDetail::find($id);
                $logTenunId = $tenun->id_log_stok_penerimaan;
                LogStokPenerimaan::find($logTenunId)->delete();
                $tenun->delete();
                logHistory('TenunDetail', 'delete');

                $inspecting = InspectingGreyDetail::where('id_tenun_detail', $id)->get();
                if ($inspecting->count() > 0) {
                    foreach ($inspecting as $i) {
                        LogStokPenerimaan::find($i->id_log_stok_penerimaan)->delete();
                    }
                    InspectingGreyDetail::where('id_tenun_detail', $id)->delete();
                    logHistory('InspectingGreyDetail', 'delete');
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    function cekRequest($request, $mode)
    {
        $messages = [];
        if ($mode == 'parent') {
            $rules = [
                'tanggal' => 'required',
            ];
            $rules['beam'] = 'required|not_in:0';
            $rules['id_barang'] = 'required|not_in:0';
            $rules['group_1'] = 'required|numeric';
            $rules['group_1_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup1];
            $rules['group_2'] = 'required|numeric';
            $rules['group_2_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup2];
            $rules['group_3'] = 'required|numeric';
            $rules['group_3_grade_total'] = ['required', 'numeric', /* 'gt:0', */ new CekTotalGroup3];
            $rules['total'] = 'required|numeric|gt:0';

            $messages['beam.required'] = 'kikw harus diisi';
            $messages['beam.not_in'] = 'kikw harus diisi';

            $messages['id_barang.required'] = 'barang harus diisi';
            $messages['id_barang.not_in'] = 'barang harus diisi';

            $messages['group_1.required'] = 'jumlah group 1 harus diisi';
            $messages['group_1.numeric'] = 'jumlah group 1 hanya berupa angka';

            $messages['group_2.required'] = 'jumlah group 2 harus diisi';
            $messages['group_2.numeric'] = 'jumlah group 2 hanya berupa angka';

            $messages['group_3.required'] = 'jumlah group 3 harus diisi';
            $messages['group_3.numeric'] = 'jumlah group 3 hanya berupa angka';

            $messages['total.required'] = 'total harus diisi';
            $messages['total.numeric'] = 'total hanya berupa angka';
            $messages['total.gt'] = 'total harus lebih besar dari 0';

            $messages['group_1_grade_total.required'] = 'total grade group 1 harus diisi';
            $messages['group_1_grade_total.numeric'] = 'total grade group 1 hanya berupa angka';
            // $messages['group_1_grade_total.gt'] = 'total grade group 1 harus lebih besar dari 0';
            $messages['group_2_grade_total.required'] = 'total grade group 2 harus diisi';
            $messages['group_2_grade_total.numeric'] = 'total grade group 2 hanya berupa angka';
            // $messages['group_2_grade_total.gt'] = 'total grade group 2 harus lebih besar dari 0';
            $messages['group_3_grade_total.required'] = 'total grade group 3 harus diisi';
            $messages['group_3_grade_total.numeric'] = 'total grade group 3 hanya berupa angka';
            // $messages['group_3_grade_total.gt'] = 'total grade group 3 harus lebih besar dari 0';
        } else if ($mode == 'detail') {
            $rules['id_tenun_detail'] = 'required';
            $messages['id_tenun_detail.required'] = 'id_tenun_detail kosong';
        }

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
    function cetak(Request $request)
    {
        $tipe = $request->tipe_cetak;
        $tgl = $request->tgl;
        if ($tipe == 'laporan') {
            $kikw = $request->kikw ? '' : "AND inspect.id_beam = $request->kikw";
            $barang = $request->barang ? '' : "AND inspect.id_barang = $request->barang";
            $sql = "SELECT
                    inspect.id_inspecting_grey,
                    inspect.id_beam,
                    nomor_kikw.name nomor_kikw,
                    nomor_beam.name nomor_beam,
                    inspect.id_mesin,
                    mesin.name nama_mesin,
                    mesin.tipe nama_tipe_mesin,
                    inspect.id_motif,
                    motif.alias nama_motif,
                    inspect.id_warna,
                    warna.alias nama_warna,
                    inspect.id_barang,
                    barang.name nama_barang,
                    inspect.id_group,
                    grup.name nama_group,
                    SUM(COALESCE(inspect.volume_1, 0)) jumlah,
                    SUM(COALESCE(inspect.jml_grade_a, 0)) grade_a,
                    SUM(COALESCE(inspect.jml_grade_b, 0)) grade_b,
                    SUM(COALESCE(inspect.jml_grade_c, 0)) grade_c,
                    SUM(COALESCE(inspect.jml_kualitas_1, 0)) kualitas_1,
                    SUM(COALESCE(inspect.jml_kualitas_2, 0)) kualitas_2,
                    SUM(COALESCE(inspect.jml_kualitas_3, 0)) kualitas_3,
                    SUM(COALESCE(inspect.jml_kualitas_4, 0)) kualitas_4,
                    SUM(COALESCE(inspect.jml_kualitas_5, 0)) kualitas_5,
                    SUM(COALESCE(inspect.jml_kualitas_6, 0)) kualitas_6,
                    SUM(COALESCE(inspect.jml_kualitas_7, 0)) kualitas_7,
                    SUM(COALESCE(inspect.jml_kualitas_8, 0)) kualitas_8,
                    SUM(COALESCE(inspect.jml_kualitas_9, 0)) kualitas_9,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_10,
                    SUM(COALESCE(inspect.jml_kualitas_11, 0)) kualitas_11,
                    SUM(COALESCE(inspect.jml_kualitas_13, 0)) kualitas_12,
                    SUM(COALESCE(inspect.jml_kualitas_14, 0)) kualitas_13,
                    SUM(COALESCE(inspect.jml_kualitas_15, 0)) kualitas_14,
                    SUM(COALESCE(inspect.jml_kualitas_16, 0)) kualitas_15,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_16,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_17,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_18,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_19,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_20,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_21,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_22,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_23,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_24,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_25,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_26,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_27,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_28,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_29,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_30,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_31,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_32,
                    SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_33
                    FROM
                    tbl_inspecting_grey_detail AS inspect
                    LEFT JOIN tbl_beam AS beam ON beam.id = inspect.id_beam
                    LEFT JOIN tbl_nomor_beam AS nomor_beam ON nomor_beam.id = beam.id_nomor_beam
                    LEFT JOIN tbl_nomor_kikw AS nomor_kikw ON nomor_kikw.id = beam.id_nomor_kikw
                    LEFT JOIN tbl_mesin AS mesin ON mesin.id = inspect.id_mesin
                    LEFT JOIN tbl_barang AS barang ON barang.id = inspect.id_barang
                    LEFT JOIN tbl_motif AS motif ON motif.id = inspect.id_motif
                    LEFT JOIN tbl_warna AS warna ON warna.id = inspect.id_warna
                    LEFT JOIN tbl_group AS grup ON grup.id = inspect.id_group
                    WHERE inspect.tanggal = '$tgl'
                    AND inspect.deleted_at IS NULL
                    $kikw
                    $barang
                    GROUP BY
                    inspect.id_inspecting_grey,
                    inspect.id_beam,
                    nomor_beam.name,
                    nomor_kikw.name,
                    inspect.id_mesin,
                    mesin.name,
                    mesin.tipe,
                    inspect.id_motif,
                    motif.alias,
                    inspect.id_warna,
                    warna.alias,
                    inspect.id_barang,
                    barang.name,
                    inspect.id_group,
                    grup.name
                    ORDER BY
                    mesin.tipe,
                    mesin.name,
                    inspect.id_beam
                ";
            $data['data'] = DB::table(DB::raw("({$sql}) as data"))
                ->selectRaw('data.*, ig.panjang_sarung, ig.keterangan')
                ->leftJoin('tbl_inspecting_grey as ig', 'ig.id', 'data.id_inspecting_grey')
                ->get();
            $data['tgl'] = $tgl;
            $judul = 'Inspecting Grey ' . tglIndo($tgl) . '.xlsx';
            $data['file'] = 'contents.production.inspecting.inspecting_grey.cetak-laporan';
            // return view('contents.production.inspecting.inspecting_grey.cetak-laporan', $data);
            return Excel::download(new ExportExcelFromView($data), $judul);
        } else if ($tipe == 'distribusi') {
            $data['data'] = DB::table('tbl_inspecting_grey_detail as data')
                ->selectRaw("
                    data.id_barang,
                    barang.name nama_barang,
                    data.tanggal,
                    data.id_mesin,
                    mesin.name nama_mesin,
                    data.id_motif,
                    motif.alias nama_motif,
                    data.id_warna,
                    warna.alias nama_warna,
                    SUM(data.volume_1) volume_1
                ")
                ->leftJoin('tbl_barang as barang', 'barang.id', 'data.id_barang')
                ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'data.id_mesin')
                ->leftJoin('tbl_motif as motif', 'motif.id', 'data.id_motif')
                ->leftJoin('tbl_warna as warna', 'warna.id', 'data.id_warna')
                ->whereNull('data.deleted_at')
                ->whereDate('data.tanggal', $tgl)
                ->groupByRaw("
                    data.id_barang,
                    barang.name,
                    data.tanggal,
                    data.id_mesin,
                    mesin.name,
                    data.id_motif,
                    motif.alias,
                    data.id_warna,
                    warna.alias
                ")
                ->orderByRaw('motif.alias asc, mesin.name asc, warna.alias asc')
                ->get();
            $data['judul'] = 'Distribusi Sarung Grey ke Jasa Luar Dudulan (' . tglIndo($tgl) . ')';
            $pdf = PDF::loadView("contents.production.inspecting.inspecting_grey.cetak-distribusi", $data)->setPaper('a4', 'landscape');
            return $pdf->stream($data['judul']);
        }
    }

    function getData($id)
    {
        $data = TenunDetail::with('relMotif', 'relWarna', 'relMesin', 'relBeam', 'relBeam.relNomorBeam', 'relBeam.relNomorKikw', 'relBarang')->selectRaw('
                tbl_tenun_detail.*,
                (select volume_1 from tbl_inspecting_grey_detail where id_group = 1 and id_tenun_detail = ' . $id . ') as group_1,
                (select id from tbl_inspecting_grey_detail where id_group = 1 and id_tenun_detail = ' . $id . ') as id_group_1,
                (select jml_grade_a from tbl_inspecting_grey_detail where id_group = 1 and id_tenun_detail = ' . $id . ') as group_1_grade_a,
                (select jml_grade_b from tbl_inspecting_grey_detail where id_group = 1 and id_tenun_detail = ' . $id . ') as group_1_grade_b,
                (select jml_grade_c from tbl_inspecting_grey_detail where id_group = 1 and id_tenun_detail = ' . $id . ') as group_1_grade_c,
                (select volume_1 from tbl_inspecting_grey_detail where id_group = 2 and id_tenun_detail = ' . $id . ') as group_2,
                (select id from tbl_inspecting_grey_detail where id_group = 2 and id_tenun_detail = ' . $id . ') as id_group_2,
                (select jml_grade_a from tbl_inspecting_grey_detail where id_group = 2 and id_tenun_detail = ' . $id . ') as group_2_grade_a,
                (select jml_grade_b from tbl_inspecting_grey_detail where id_group = 2 and id_tenun_detail = ' . $id . ') as group_2_grade_b,
                (select jml_grade_c from tbl_inspecting_grey_detail where id_group = 2 and id_tenun_detail = ' . $id . ') as group_2_grade_c,
                (select volume_1 from tbl_inspecting_grey_detail where id_group = 3 and id_tenun_detail = ' . $id . ') as group_3,
                (select id from tbl_inspecting_grey_detail where id_group = 3 and id_tenun_detail = ' . $id . ') as id_group_3,
                (select jml_grade_a from tbl_inspecting_grey_detail where id_group = 3 and id_tenun_detail = ' . $id . ') as group_3_grade_a,
                (select jml_grade_b from tbl_inspecting_grey_detail where id_group = 3 and id_tenun_detail = ' . $id . ') as group_3_grade_b,
                (select jml_grade_c from tbl_inspecting_grey_detail where id_group = 3 and id_tenun_detail = ' . $id . ') as group_3_grade_c
            ')->where('tbl_tenun_detail.id', $id)->first();
        return $data;
    }

    function getBeam(Request $request, $filter = null)
    {
        $term = $request->input('q');
        $parts = ($term == '') ? [] : explode(" | ", $term);
        $data = DB::table('tbl_tenun_detail as detail')
            ->leftJoin('tbl_motif as motif', 'motif.id', 'detail.id_motif')
            ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'detail.id_mesin')
            ->leftJoin('tbl_barang as barang', 'barang.id', 'detail.id_barang')
            ->leftJoin('tbl_warna as warna', 'warna.id', 'detail.id_warna')
            ->leftJoin('tbl_beam as beam', 'beam.id', 'detail.id_beam')
            ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
            ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
            ->selectRaw('
                detail.id,
                detail.id_tenun,
                detail.id_beam,
                detail.id_mesin,
                detail.id_motif,
                detail.id_warna,
                nomor_kikw.name nomor_kikw,
                nomor_beam.name nomor_beam,
                mesin.name nama_mesin,
                motif.alias nama_motif,
                warna.alias nama_warna,
                barang.alias nama_barang
            ')
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('nomor_kikw.name', $parts)
                        ->orWhereIn('nomor_beam.name', $parts)
                        ->orWhereIn('motif.alias', $parts)
                        ->orWhereIn('warna.alias', $parts)
                        ->orWhereIn('barang.name', $parts)
                        ->orWhereIn('mesin.name', $parts);
                });
            })
            ->where('code', 'BBTL')
            ->whereNull('detail.deleted_at')
            ->orderBy('detail.id', 'desc')->paginate(5);
        return $data;
    }

    function getBarang(Request $request)
    {
        $term = $request->input('q');
        $data = Barang::where('id_tipe', 7)->where('name', 'like', '%' . $term . '%')->get();

        return $data;
    }

    function getStok($id)
    {
        $sub = TenunDetail::selectRaw('id_beam, sum(volume_1) potongan')->where('code', 'BG')->where('id_beam', $id)->groupBy('id_beam');
        $data = Tenun::leftJoinSub($sub, 'sub', 'tbl_tenun.id_beam', 'sub.id_beam')
            ->selectRaw('
            COALESCE((COALESCE(jumlah_beam,0) - COALESCE(sub.potongan,0)),0) stok
        ')->where('tbl_tenun.id_beam', $id)->first();
        if ($data) {
            $temp = $data->setAppends([]);
        } else {
            $temp = [
                'stok' => 0,
            ];
        }
        return $temp;
    }
    function getStokPotong($id, $id_group)
    {
        $data = InspectingGreyDetail::where('id_tenun_detail', $id)->where('id_group', $id_group)->selectRaw('volume_1 as potongan')->first();
        if ($data) {
            $temp = $data;
        } else {
            $temp = [
                'potongan' => 0,
            ];
        }
        return $temp;
    }
}
