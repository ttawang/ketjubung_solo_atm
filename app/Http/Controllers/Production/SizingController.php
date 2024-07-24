<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Beam;
use App\Models\Gudang;
use App\Models\Leno;
use App\Models\LenoDetail;
use App\Models\LogStokPenerimaan;
use App\Models\MesinHistory;
use App\Models\NomorKikw;
use App\Models\PengirimanBarangDetail;
use App\Models\Sizing;
use App\Models\SizingDetail;
use App\Models\Supplier;
use App\Models\Warna;
use App\Models\WarpingDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SizingController extends Controller
{
    private static $model = 'Sizing';
    private static $modelDetail = 'SizingDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Sizing', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('weaving', 'sizing', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.weaving.sizing.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.weaving.sizing.parent');
        } else {
            $data['data'] = Sizing::find($id);
            if (!$tipe) {
                return view('contents.production.weaving.sizing.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.weaving.sizing.input', $data);
                } else if ($tipe == 'output') {
                    return view('contents.production.weaving.sizing.output', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $temp = Sizing::orderBy('created_at', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('supplier', function ($i) {
                    return $i->relSupplier->name;
                })
                ->addColumn('action', function ($i) {
                    $temp = Sizing::find($i->id)->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                        'model' => 'Sizing'
                    ];
                    $action = actionBtn($i->id, true, true, true, $validasi);
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            if ($tipe == 'input') {
                $sub = DB::table('tbl_sizing_detail')->selectRaw('id_parent, COUNT(*) as jumlah_terima')->where('code', 'BZ')->whereNull('deleted_at')->groupBy('id_parent');
                $temp = SizingDetail::leftJoinSub($sub, 'sub', function ($join) {
                    return $join->on('tbl_sizing_detail.id', 'sub.id_parent');
                })->where([['id_sizing', $id], ['code', 'BL']])->orderBy('created_at', 'desc');
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('barang', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relBarang->name;
                    })
                    ->addColumn('mesin', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relMesin->name;
                    })
                    ->addColumn('warna', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relWarna->alias;
                    })
                    ->addColumn('motif', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relMotif->alias;
                    })
                    ->addColumn('gudang', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relGudang->name;
                    })
                    ->addColumn('volume_1', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->volume_masuk_1;
                    })
                    ->addColumn('volume_2', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->volume_masuk_2;
                    })
                    ->addColumn('no_beam', function ($i) {
                        return $i->relBeam->relNomorBeam->alias;
                    })
                    ->addColumn('no_kikw', function ($i) {
                        return $i->relBeam->relNomorKikw->name;
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Sizing::find($i->id_sizing)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Sizing'
                        ];
                        $action = actionBtn($i->id, false, $i->jumlah_terima == 0, $i->jumlah_terima == 0, $validasi);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } elseif ($tipe == 'output') {
                $temp = SizingDetail::where([['id_sizing', $id], ['code', 'BZ']])->orderBy('created_at', 'desc');
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('barang', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relBarang->name;
                    })
                    ->addColumn('mesin', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relMesin->name;
                    })
                    ->addColumn('warna', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relWarna->alias;
                    })
                    ->addColumn('motif', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relMotif->alias;
                    })
                    ->addColumn('gudang', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->relGudang->name;
                    })
                    ->addColumn('volume_1', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->volume_masuk_1;
                    })
                    ->addColumn('volume_2', function ($i) {
                        return $i->relBeam->relLogStokPenerimaanBL->volume_masuk_2;
                    })
                    ->addColumn('no_beam', function ($i) {
                        return $i->relBeam->relNomorBeam->alias;
                    })
                    ->addColumn('no_kikw', function ($i) {
                        return $i->relBeam->relNomorKikw->name;
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Sizing::find($i->id_sizing)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Sizing'
                        ];

                        $idNomorKikwParent = $i->throughSizingBeam()->value('id_nomor_kikw');
                        $idNomorKikwSecondary = $i->relBeam()->value('id_nomor_kikw');
                        $checkIfParent = $idNomorKikwParent == $idNomorKikwSecondary ? 'YA' : 'TIDAK';
                        $countParent = DB::table('tbl_sizing_detail')->where('id_parent', $i->id_parent)->count();

                        $customBtn = '<a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                            onclick="editTerima($(this));" data-id="' . $i->id . '" data-check-beam="' . $checkIfParent . '" data-count-parent="' . $countParent . '">
                            <i class="icon md-edit" aria-hidden="true"></i>
                        </a><a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                            onclick="hapus($(this));" data-id="' . $i->id . '">
                            <i class="icon md-delete" aria-hidden="true"></i>
                        </a>';

                        $action = actionBtn($i->id, false, false, false, $validasi, $customBtn);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            }
        }
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
            $data = $request->except(['id', '_token', 'mode', 'tipe', 'id_parent', 'id_nomor_beam', 'beam2', 'radioTipe', 'volume_beam', 'id_beam_secondary', 'id_nomor_kikw', 'no_kikw', 'id_motif', 'id_mesin', 'tanggal2']);
            $data2 = $request->except(['id', '_token', 'mode', 'tipe', 'id_parent', 'id_nomor_beam', 'beam2', 'radioTipe', 'volume_beam', 'id_beam_secondary', 'id_nomor_kikw', 'no_kikw', 'id_motif', 'id_mesin', 'tanggal2']);
            $rule = $this->cekRequest($request, $mode, $id);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        Sizing::create($data);
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BL';
                            $temp = LogStokPenerimaan::where([['code', 'BL'], ['id_beam', $request->id_beam]])->orderBy('id', 'asc')->first()->setAppends([])->toArray();
                            $dataLog = unsetMultiKeys(['id', 'tanggal', 'volume_masuk_1', 'volume_masuk_2', 'volume_keluar_1', 'volume_keluar_2', 'created_at', 'updated_at', 'deleted_at', 'is_dyeing_jasa_luar', 'is_doubling', 'is_stokopname', 'is_saldoawal', 'id_grade', 'id_kualitas', 'is_sizing', 'tipe_pra_tenun'], $temp);
                            $dataLog['volume_keluar_1'] = $temp['volume_masuk_1'];
                            $dataLog['volume_keluar_2'] = $temp['volume_masuk_2'];
                            $dataLog['code'] = $data['code'];
                            $dataLog['tanggal'] = $data['tanggal'];
                        } elseif ($tipe == 'output') {
                            $data['code'] = 'BZ';
                            $data['id_parent'] = $request['id_parent'];

                            $temp = LogStokPenerimaan::with('relBeam')->where([['code', 'BL'], ['id_beam', $request->id_beam]])->orderBy('id', 'asc')->first()->setAppends([])->toArray();

                            $beam['id_nomor_beam'] = $request['id_nomor_beam'];
                            $beam['id_nomor_kikw'] = $temp['rel_beam']['id_nomor_kikw'];
                            $beam['tipe_beam']     = 'LUSI';
                            $beam['is_sizing']     = 'YA';
                            $beam['id_beam_prev']  = $request->id_beam;
                            $data['id_beam']       = Beam::create($beam)->id;

                            $dataLog = unsetMultiKeys(['id', 'id_beam', 'rel_beam', 'tanggal', 'volume_masuk_1', 'volume_masuk_2', 'volume_keluar_1', 'volume_keluar_2', 'created_at', 'updated_at', 'deleted_at', 'is_dyeing_jasa_luar', 'is_doubling', 'is_stokopname', 'is_saldoawal', 'id_grade', 'id_kualitas', 'is_sizing', 'tipe_pra_tenun'], $temp);
                            $dataLog['id_beam'] = $data['id_beam'];
                            $dataLog['volume_masuk_1'] = 1;
                            $dataLog['volume_masuk_2'] = ($request['radioTipe'] == 'multi') ? $request['volume_beam'] : $temp['volume_masuk_2'];
                            $dataLog['code'] = 'BL';
                            $dataLog['is_sizing'] = 'YA';
                            $dataLog['tanggal'] = $data['tanggal'];
                            MesinHistory::create(['id_mesin' => $temp['id_mesin'], 'id_beam' => $data['id_beam']]);

                            if ($request['radioTipe'] == 'multi') {
                                $data2['code'] = 'BZ';
                                $data2['id_parent'] = $request['id_parent'];
                                $data2['tanggal'] = $request['tanggal2'];

                                $inputBeam2 = $request['beam2'];
                                $beam2['id_nomor_beam'] = $inputBeam2['id_nomor_beam'];
                                $beam2['id_nomor_kikw'] = NomorKikw::create(['name' => $inputBeam2['no_kikw']])->id;
                                $beam2['tipe_beam']     = 'LUSI';
                                $beam2['is_sizing']     = 'YA';
                                $beam2['id_beam_prev']  = $request->id_beam;
                                $data2['id_beam']   = Beam::create($beam2)->id;

                                $dataLogBeam2 = unsetMultiKeys(['id', 'id_beam', 'id_mesin', 'id_motif', 'rel_beam', 'tanggal', 'volume_masuk_1', 'volume_masuk_2', 'volume_keluar_1', 'volume_keluar_2', 'created_at', 'updated_at', 'deleted_at', 'is_dyeing_jasa_luar', 'is_doubling', 'is_stokopname', 'is_saldoawal', 'id_grade', 'id_kualitas', 'is_sizing', 'tipe_pra_tenun'], $temp);
                                $dataLogBeam2['id_beam'] = $data2['id_beam'];
                                $dataLogBeam2['id_motif'] = $inputBeam2['id_motif'];
                                $dataLogBeam2['id_mesin'] = $inputBeam2['id_mesin'];
                                $dataLogBeam2['volume_masuk_1'] = 1;
                                $dataLogBeam2['volume_masuk_2'] = $inputBeam2['volume_beam'];
                                $dataLogBeam2['code'] = 'BL';
                                $dataLogBeam2['is_sizing'] = 'YA';
                                $dataLogBeam2['tanggal'] = $request['tanggal2'];
                                MesinHistory::create(['id_mesin' => $inputBeam2['id_mesin'], 'id_beam' => $data2['id_beam']]);
                            }

                            Beam::find($request->id_beam)->update(['finish' => 1]);
                        }

                        $logId = LogStokPenerimaan::create($dataLog)->id;
                        $data['id_log_stok_penerimaan'] = $logId;
                        SizingDetail::create($data);

                        if ($tipe == 'output' && $request['radioTipe'] == 'multi') {
                            $logId2 = LogStokPenerimaan::create($dataLogBeam2)->id;
                            $data2['id_log_stok_penerimaan'] = $logId2;
                            SizingDetail::create($data2);
                        }

                        logHistory(self::$modelDetail, 'create');
                        /* if ($tipe == 'output') {
                            WarpingDetail::find($data['id_warping_detail'])->update(['id_sizing_detail' => $dataId]);
                        } */
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        Sizing::find($id)->update($data);
                        logHistory(self::$model, 'update');
                    } else {
                        if ($tipe == 'input') {
                            $temp = LogStokPenerimaan::where([['code', 'BL'], ['id_beam', $request->id_beam]])->orderBy('id', 'asc')->first()->setAppends([])->toArray();
                            $dataLog = unsetMultiKeys(['id', 'tanggal', 'volume_masuk_1', 'volume_masuk_2', 'volume_keluar_1', 'volume_keluar_2', 'created_at', 'updated_at', 'deleted_at', 'is_dyeing_jasa_luar', 'is_doubling', 'is_stokopname', 'is_saldoawal', 'id_grade', 'id_kualitas', 'is_sizing', 'tipe_pra_tenun'], $temp);
                            $dataLog['volume_keluar_1'] = $temp['volume_masuk_1'];
                            $dataLog['volume_keluar_2'] = $temp['volume_masuk_2'];
                            $dataLog['tanggal'] = $data['tanggal'];
                        } else {
                            if ($request['radioTipe'] == 'single') {
                                $data['tanggal'] = $request['tanggal'];
                                $dataLog['tanggal'] = $request['tanggal'];
                                $param['id_nomor_beam'] = $request['id_nomor_beam'];
                                Beam::where('id', $request['id_beam_secondary'])->update($param);
                            } else if ($request['radioTipe'] == 'multi-single') {
                                $data['tanggal'] = $request['tanggal'];
                                $dataLog['tanggal'] = $request['tanggal'];
                                $dataLog['volume_masuk_2'] = $request['volume_beam'];
                                $param['id_nomor_beam'] = $request['id_nomor_beam'];
                                Beam::where('id', $request['id_beam_secondary'])->update($param);
                            } else if ($request['radioTipe'] == 'multi') {
                                $data['tanggal'] = $request['tanggal'];
                                $dataLog['tanggal'] = $request['tanggal'];
                                $dataLog['id_motif'] = $request['id_motif'];
                                $dataLog['id_mesin'] = $request['id_mesin'];
                                $dataLog['volume_masuk_2'] = $request['volume_beam'];

                                MesinHistory::where('id_beam', $request['id_beam_secondary'])->update(['id_mesin' => $request['id_mesin']]);
                                NomorKikw::where('id', $request['id_nomor_kikw'])->update(['name' => $request['no_kikw']]);

                                $param['id_nomor_beam'] = $request['id_nomor_beam'];
                                Beam::where('id', $request['id_beam_secondary'])->update($param);
                            }
                        }

                        if(!empty($dataLog)) LogStokPenerimaan::where('id', $request['id_log_stok_penerimaan'])->update($dataLog);
                        if(!empty($data)) SizingDetail::where('id', $id)->update($data);
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
    function cekRequest($request, $mode, $id = null)
    {
        $rules = [
            'tanggal' => 'required',
        ];
        $messages = [];
        if ($mode == 'parent') {
            $rules['no_sizing'] = 'required';
            $rules['id_supplier'] = 'required|not_in:0';

            $messages['no_sizing.required'] = 'no sizing harus diisi';
            $messages['id_supplier.required'] = 'supplier harus diisi';
            $messages['id_supplier.not_in'] = 'supplier harus diisi';
        } else {
            if (!$id) {
                $rules['id_beam'] = 'required|not_in:0';

                $messages['id_beam.required'] = 'no beam harus diisi';
                $messages['id_beam.not_in'] = 'no beam harus diisi';
            }
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
    public function hapus($id, $mode)
    {
        DB::beginTransaction();
        try {
            if ($mode == 'parent') {
                Sizing::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $temp = SizingDetail::find($id);
                $logId = $temp->id_log_stok_penerimaan;
                $beamId = $temp->id_beam;
                $beam = Beam::where('id', $beamId);
                $idBeamPrev = $beam->first()->id_beam_prev;
                if ($idBeamPrev != null) {

                    $idNomorKikwParent = DB::table('tbl_beam')->where('id', $idBeamPrev)->value('id_nomor_kikw');
                    $idNomorKikw = $beam->first()->id_nomor_kikw;
                    if ($idNomorKikwParent != $idNomorKikw) NomorKikw::where('id', $idNomorKikw)->delete();

                    MesinHistory::where('id_beam', $beamId)->delete();
                    Beam::where('id', $idBeamPrev)->update(['finish' => 0]);
                    $beam->delete();
                }
                SizingDetail::find($id)->delete();
                LogStokPenerimaan::find($logId)->delete();
                logHistory(self::$modelDetail, 'delete');
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    function getSupplier(Request $request)
    {
        $term = $request->input('q');
        $data = Supplier::where('name', 'like', '%' . $term . '%')->get();
        return $data;
    }
    public function getData($id, $mode)
    {
        if ($mode == 'parent') {
            $data = Sizing::find($id);
        } else {
            $data = SizingDetail::with(['throughNomorKikw', 'throughNomorBeam', 'relLogStokPenerimaanBL.relWarna', 'relLogStokPenerimaanBL.relMotif', 'relMesinHistoryLatest.relMesin', 'relLogStokPenerimaan'])->find($id);

            if($data->id_parent != null){
                $dataParentBeam = SizingDetail::with(['relLogStokPenerimaanBL.relWarna', 'relLogStokPenerimaanBL.relMotif', 'relMesinHistoryLatest.relMesin'])->find($data->id_parent);
                $selectedBeam = [
                    'id' => $dataParentBeam->id_beam,
                    'text' => $data->relMesinHistoryLatest->relMesin->name . ' | ' . $data->throughNomorKikw()->value('name') . ' | ' . $data->throughNomorBeam()->value('name') . ' | ' . $data->relLogStokPenerimaanBL->relWarna->name . ' | ' . $data->relLogStokPenerimaanBL->relMotif->alias
                ];
                $data->parent_beam = $selectedBeam;
            }
        }

        return response()->json($data);
    }
    public function getBarang(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = Beam::with('relNomorKikw', 'relNomorBeam', 'relLogStokPenerimaanBL.relWarna', 'relLogStokPenerimaanBL.relMotif', 'relMesinHistoryLatest.relMesin')->whereNull('is_sizing')->where('finish', 0)->whereNull('tipe_pra_tenun')->where('tipe_beam', 'LUSI')->whereNotNull('id_nomor_kikw')->has('relMesinHistory')
                ->where(function ($q) {
                    $q->whereHas('relLogStokPenerimaanBL', function ($q) {
                        $q->whereNotNull('id_warna')->whereNotNull('id_motif');
                    });
                })
                ->where(function ($q) use ($term) {
                    $q->whereHas('relNomorKikw', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    })->orwhereHas('relNomorBeam', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    })->orwhereHas('relLogStokPenerimaanBL.relMotif', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    })->orwhereHas('relLogStokPenerimaanBL.relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    })->orwhereHas('relMesinHistoryLatest.relMesin', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })->orderBy('id', 'asc')->paginate(5);
        } else if ($tipe == 'output') {
            $temp = SizingDetail::where('id_sizing', $id)->doesntHave('relSizingParent')->where('code', 'BL')->pluck('id_beam', 'id')->toArray();
            $data = Beam::with('relNomorKikw', 'relNomorBeam', 'relLogStokPenerimaanBL.relWarna', 'relLogStokPenerimaanBL.relMotif', 'relSizing')->whereNull('is_sizing')
                ->whereIn('id', $temp)
                ->whereHas('relSizing', function ($query) use ($id) {
                    return $query->where('id_sizing', $id);
                })
                ->where(function ($q) use ($term) {
                    $q->whereHas('relNomorKikw', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    })->orwhereHas('relNomorBeam', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    })->orwhereHas('relLogStokPenerimaanBL.relMotif', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    })->orwhereHas('relLogStokPenerimaanBL.relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    });
                })->paginate(5);
        }
        return $data;
    }
    public function getStokBarang($barang, $warna, $gudang, $tipe = null)
    {
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::selectRaw('
                    id_satuan_1,
                    sum(coalesce(volume_masuk_1,0)) - sum(coalesce(volume_keluar_1,0)) as stok_1,
                    id_satuan_2,
                    sum(coalesce(volume_masuk_2,0)) - sum(coalesce(volume_keluar_2,0)) as stok_2
                ')
                ->where([
                    ['id_barang', $barang],
                    ['id_warna', $warna],
                    ['id_gudang', $gudang],
                    ['id_satuan_1', 1],
                    ['id_satuan_2', 2],
                    ['code', 'BHD']
                ])
                ->groupBy('id_barang', 'id_warna', 'id_gudang', 'id_satuan_1', 'id_satuan_2', 'code')
                ->first();
        }
        if ($data) {
            $temp = $data;
        } else {
            $temp = [
                'id_satuan_1' => 1,
                'stok_1' => 0,
                'id_satuan_2' => 2,
                'stok_2' => 0,
            ];
        }
        return response()->json($temp);
    }
}
