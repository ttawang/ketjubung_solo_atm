<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Beam;
use App\Models\Gudang;
use App\Models\LogStokPenerimaan;
use App\Models\Mesin;
use App\Models\MesinHistory;
use App\Models\Motif;
use App\Models\NomorBeam;
use App\Models\NomorKikw;
use App\Models\SizingDetail;
use App\Models\Warna;
use App\Models\Warping;
use App\Models\WarpingDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class WarpingController extends Controller
{
    private static $model = 'Warping';
    private static $modelDetail = 'WarpingDetail';

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $rolesId = Auth::user()->roles_id;
            if ($rolesId == 4) return redirect()->route('production.tenun.index');
            return $next($request);
        });
    }

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Warping', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('weaving', 'warping', $data['breadcumbs'], true, true, true, true);

        return view('contents.production.weaving.warping.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {

        if ($mode == 'parent') {
            return view('contents.production.weaving.warping.parent');
        } else {
            $data['data'] = Warping::find($id);
            if (!$tipe) {
                return view('contents.production.weaving.warping.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.weaving.warping.input', $data);
                } else if ($tipe == 'output') {
                    return view('contents.production.weaving.warping.output', $data);
                } else {
                    return view('contents.production.weaving.warping.sisa', $data);
                }
            }
        }
    }
    public function table(Request $request, $mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $temp = Warping::orderBy('tanggal', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('mesin', function ($i) {
                    return $i->relMesin->name;
                })
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('action', function ($i) {
                    $temp = Warping::find($i->id)->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                        'model' => 'Warping'
                    ];
                    $action = actionBtn($i->id, true, true, true, $validasi);
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            if ($tipe == 'input') {
                $temp = WarpingDetail::where([['id_warping', $id], ['code', 'BBW']])->orderBy('created_at', 'desc');

                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('barang', function ($i) {
                        return $i->relBarang->name;
                    })
                    ->addColumn('warna', function ($i) {
                        return $i->relWarna->alias;
                    })
                    ->addColumn('gudang', function ($i) {
                        return $i->relGudang->name;
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Warping::find($i->id_warping)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Warping'
                        ];
                        $action = actionBtn($i->id, false, true, true, $validasi);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } elseif ($tipe == 'output') {
                $temp = WarpingDetail::where([['id_warping', $id], ['code', '!=', 'BBW'], ['code', '!=', 'BBWS'], ['code', '!=', 'BBWSS']])->orderBy('created_at', 'desc');

                if (!empty($request->search['value'])) {
                    $term = $request->search['value'];
                    $temp->where(function ($q) use ($term) {
                        $q->whereHas('relBeam.relNomorKikw', function ($q) use ($term) {
                            $q->where('name', 'like', '%' . $term . '%');
                        })->orwhereHas('relBeam.relNomorBeam', function ($q) use ($term) {
                            $q->where('name', 'like', '%' . $term . '%');
                        })->orwhereHas('relMotif', function ($q) use ($term) {
                            $q->where('alias', 'like', '%' . $term . '%');
                        })->orwhereHas('relWarna', function ($q) use ($term) {
                            $q->where('alias', 'like', '%' . $term . '%');
                        })->orwhereHas('relBarang', function ($q) use ($term) {
                            $q->where('name', 'like', '%' . $term . '%');
                        });
                    });
                }
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('barang', function ($i) {
                        return $i->relBarang->name . ' | ' . $i->relBarang->relTipe->name;
                    })
                    ->addColumn('motif', function ($i) {
                        $motif = '';
                        if ($i->id_motif) {
                            $motif = $i->relMotif->alias;
                        }
                        return $motif;
                    })
                    ->addColumn('warna', function ($i) {
                        $warna = '';
                        if ($i->id_motif) {
                            $warna = $i->relWarna->alias;
                        }
                        return $warna;
                    })
                    ->addColumn('gudang', function ($i) {
                        return $i->relGudang->name;
                    })
                    ->addColumn('mesin', function ($i) {
                        if ($i->id_mesin) {
                            return $i->relMesin->name;
                        } else {
                            return '';
                        }
                    })
                    ->addColumn('no_beam', function ($i) {
                        $nomor = '';
                        if ($i->id_beam) {
                            $nomor = $i->relBeam->relNomorBeam ? $i->relBeam->relNomorBeam->alias : '';
                        }
                        return $nomor;
                    })
                    ->addColumn('no_kikw', function ($i) {
                        if ($i->id_beam) {
                            if ($i->relBeam->id_nomor_kikw) {
                                return $i->relBeam->relNomorKikw->name;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Warping::find($i->id_warping)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Warping'
                        ];
                        $action = actionBtn($i->id, false, true, true, $validasi);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } else {
                $temp = WarpingDetail::where([['id_warping', $id]])->whereIn('code', ['BBWS', 'BBWSS'])->orderBy('created_at', 'desc');
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('barang', function ($i) {
                        return $i->relBarang->name;
                    })
                    ->addColumn('warna', function ($i) {
                        return $i->relWarna->alias;
                    })
                    ->addColumn('gudang', function ($i) {
                        return $i->relGudang->name;
                    })
                    ->addColumn('jenis', function ($i) {
                        return ($i->code == 'BBWS') ? 'Bisa Digunakan' : 'Tidak Bisa Digunakan';
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Warping::find($i->id_warping)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Warping'
                        ];
                        $action = actionBtn($i->id, false, true, true, $validasi);
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
            // dd($id);
            $data = $request->except(['id', '_token', 'mode', 'tipe', 'id_beam', 'jenis']);
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'jenis', 'id_warping', 'volume_1', 'volume_2', 'no_kikw', 'id_nomor_beam', 'id_mesin', 'id_beam']);
            $rule = $this->cekRequest($request, $mode, $tipe);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        Warping::create($data);
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BBW';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['volume_keluar_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        } elseif ($tipe == 'output') {
                            $data = $request->except(['id', '_token', 'mode', 'tipe', 'id_nomor_beam', 'no_kikw', 'id_mesin']);
                            $id_mesin = ($request->id_mesin == 0) ? null : $request->id_mesin;
                            $data['id_mesin'] = $id_mesin;
                            $dataBeam = $request->only(['id_nomor_beam']);
                            if ($dataBeam['id_nomor_beam'] == 0) {
                                $dataBeam['id_nomor_beam'] = NULL;
                            }
                            $barang = Barang::find($data['id_barang']);
                            if ($barang) {
                                if ($barang->id_tipe == 3) {
                                    $data['code'] = 'BL';
                                    $dataBeam['tipe_beam'] = 'LUSI';
                                } else if ($barang->id_tipe == 4) {
                                    $data['code'] = 'BS';
                                    $dataBeam['tipe_beam'] = 'SONGKET';
                                }
                            }
                            if ($request->no_kikw) {
                                $idNomorKikw = NomorKikw::create(['name' => $request->no_kikw])->id;
                                $dataBeam['id_nomor_kikw'] = $idNomorKikw;
                            }
                            $beamId = Beam::create($dataBeam)->id;
                            if ($id_mesin) {
                                MesinHistory::create(['id_mesin' => $id_mesin, 'id_beam' => $beamId])->id;
                                $dataLog['id_mesin'] = $id_mesin;
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];

                            $dataLog['id_beam'] = $beamId;
                            $data['id_beam'] = $beamId;
                        } else {
                            $data['code'] = ($request->jenis == 1) ? 'BBWS' : 'BBWSS';
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        }
                        $logId = LogStokPenerimaan::create($dataLog)->id;
                        $data['id_log_stok_penerimaan'] = $logId;
                        WarpingDetail::create($data);
                        logHistory(self::$modelDetail, 'create');
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        Warping::find($id)->update($data);
                        logHistory(self::$model, 'update');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BBW';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['volume_keluar_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                            $warping = WarpingDetail::find($id);
                            LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update($dataLog);
                            $warping->update($data);
                            logHistory(self::$modelDetail, 'update');
                        } elseif ($tipe == 'output') {
                            $beam = Beam::find($request->id_beam);
                            $sizing = SizingDetail::where('id_beam', $request->id_beam)->first();
                            if (!$beam->is_sizing || !$beam->tipe_pra_tenun) {
                                if (!$sizing) {
                                    $beam_kikw = Beam::find($request->id_beam)->id_nomor_kikw;
                                    if (!$beam_kikw) {
                                        $id_nomor_kikw = NomorKikw::create(['name' => $request->no_kikw])->id;
                                        Beam::find($id)->update(['id_nomor_kikw' => $id_nomor_kikw, 'updated_by' => Auth::id()]);
                                    } else {
                                        NomorKikw::find($beam_kikw)->update(['name' => $request->no_kikw, 'updated_by' => Auth::id()]);
                                    }
                                    $mesin = MesinHistory::where('id_beam', $request->id_beam)->orderBy('id', 'desc')->first();
                                    if (!$mesin) {
                                        $warping = WarpingDetail::find($id);
                                        $warping->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                                        MesinHistory::create(['id_beam' => $request->id_beam, 'id_mesin' => $request->id_mesin]);
                                        LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update(['id_mesin' => $request->id_mesin]);
                                    } else {
                                        $mesin->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                                        $warping = WarpingDetail::find($id);
                                        $warping->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                                        LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update(['id_mesin' => $request->id_mesin]);
                                    }

                                    $data = $request->except(['id', '_token', 'mode', 'tipe', 'id_nomor_beam', 'id_beam', 'id_warping', 'no_kikw', 'id_mesin']);
                                    $barang = Barang::find($data['id_barang']);
                                    if ($barang) {
                                        if ($barang->id_tipe == 3) {
                                            $data['code'] = 'BL';
                                            $dataBeam['tipe_beam'] = 'LUSI';
                                        } else if ($barang->id_tipe == 4) {
                                            $data['code'] = 'BS';
                                            $dataBeam['tipe_beam'] = 'SONGKET';
                                        }
                                    }
                                    $data['updated_by'] = Auth::id();
                                    $dataLog['volume_masuk_1'] = $data['volume_1'];
                                    $dataLog['volume_masuk_2'] = $data['volume_2'];
                                    $dataLog['code'] = $data['code'];
                                    Beam::find($request->id_beam)->update($dataBeam);
                                    $warping = WarpingDetail::find($id);
                                    $warping->update($data);
                                    LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update($dataLog);
                                    logHistory(self::$modelDetail, 'update');
                                } else {
                                    $data = array('success' => false, 'messages' => array('status' => array('Beam telah digunakan pada proses selanjutnya')));
                                    return response()->json($data);
                                }
                            } else {
                                $data = array('success' => false, 'messages' => array('status' => array('Beam telah digunakan pada proses selanjutnya')));
                                return response()->json($data);
                            }
                        } else {
                            $data['code'] = ($request->jenis == 1) ? 'BBWS' : 'BBWSS';
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                            $warping = WarpingDetail::find($id);
                            LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update($dataLog);
                            $warping->update($data);
                            logHistory(self::$modelDetail, 'update');
                        }
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
    function cekRequest($request, $mode, $tipe)
    {
        $rules = [
            'tanggal' => 'required',
        ];
        $messages = [];
        $id = $request->id;
        if ($mode == 'parent') {
            // $rules['no_warping'] = 'required';
            $rules['id_mesin'] = 'required|not_in:0';

            $messages['no_warping.required'] = 'no warping harus diisi';
            $messages['id_mesin.required'] = 'mesin harus diisi';
            $messages['id_mesin.not_in'] = 'mesin harus diisi';
        } else {
            $rules['id_barang'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['id_warna'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';
            $rules['volume_2'] = 'required|numeric|gt:0|not_in:0';

            $messages['id_barang.required'] = 'barang harus diisi';
            $messages['id_barang.not_in'] = 'barang harus diisi';
            $messages['id_gudang.required'] = 'gudang harus diisi';
            $messages['id_gudang.not_in'] = 'gudang harus diisi';
            $messages['id_warna.required'] = 'warna harus diisi';
            $messages['id_warna.not_in'] = 'warna harus diisi';
            $messages['volume_1.required'] = 'volume 1 harus diisi';
            $messages['volume_1.numeric'] = 'volume 1 hanya berupa angka';
            $messages['volume_1.not_in'] = 'volume 1 tidak boleh 0';
            $messages['volume_1.gt'] = 'volume 1 harus lebih besar dari 0';
            $messages['volume_2.required'] = 'volume 2 harus diisi';
            $messages['volume_2.numeric'] = 'volume 2 hanya berupa angka';
            $messages['volume_2.not_in'] = 'volume 2 tidak boleh 0';
            $messages['volume_2.gt'] = 'volume 2 harus lebih besar dari 0';

            if ($tipe == 'output') {
                $barang = Barang::where('id', $request->id_barang)->first()->id_tipe;
                if ($barang == 3) {
                    $rules['id_nomor_beam'] = 'required|not_in:0';
                    $messages['id_nomor_beam.required'] = 'no beam lusi harus diisi';
                    $messages['id_nomor_beam.not_in'] = 'no beam lusi harus diisi';
                }
                if (!$id) {
                    $rules['no_kikw'] = [
                        Rule::unique('tbl_nomor_kikw', 'name')->whereNull('deleted_at'),
                    ];
                    $messages['no_kikw.unique'] = 'no kikw tidak boleh sama';
                } else {
                    $id_nomor_kikw = Beam::find($request->id_beam)->id_nomor_kikw;
                    if ($id_nomor_kikw) {
                        $rules['no_kikw'] = [
                            Rule::unique('tbl_nomor_kikw', 'name')->whereNull('deleted_at')->ignore($id_nomor_kikw),
                        ];
                        $messages['no_kikw.unique'] = 'no kikw tidak boleh sama';
                    }
                }
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
                Warping::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $temp = WarpingDetail::find($id);
                $logId = $temp->id_log_stok_penerimaan;
                $beamId = $temp->id_beam;
                $beam = Beam::find($beamId);
                $sizing = SizingDetail::where('id_beam', $beamId)->first();
                if ($beamId) {
                    if (!$beam->is_sizing || !$beam->tipe_pra_tenun) {
                        if (!$sizing) {
                            WarpingDetail::find($id)->delete();
                            LogStokPenerimaan::find($logId)->delete();
                            if ($beamId) {
                                MesinHistory::where('id_beam', $beamId)->delete();
                                if ($beam->id_nomor_kikw) {
                                    NomorKikw::where('id', $beam->id_nomor_kikw)->delete();
                                }
                                Beam::find($beamId)->delete();
                            }
                            logHistory(self::$modelDetail, 'delete');
                        } else {
                            return response()->json(['success' => false, 'message' => 'Beam telah digunakan pada proses selanjutnya']);
                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Beam telah digunakan pada proses selanjutnya']);
                    }
                } else {
                    WarpingDetail::find($id)->delete();
                    LogStokPenerimaan::find($logId)->delete();
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getData($id, $mode)
    {
        if ($mode == 'parent') {
            $data = Warping::find($id);
        } else {
            $data = WarpingDetail::with('relBarang', 'relWarna', 'relGudang', 'relMesin', 'relMotif', 'relBeam', 'relBeam.relNomorBeam', 'relBeam.relNomorKikw')->find($id);
        }

        return response()->json($data);
    }
    public function getBarang(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relBarang', 'relBarang.relTipe')->selectRaw('id_barang')->where('code', 'BBW')->groupBy('id_barang')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relBarang', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })
                ->get();
        } else if ($tipe == 'output') {
            $data = Barang::with('relTipe')->whereIn('id_tipe', [3, 4])->where('name', 'like', '%' . $term . '%')->get();
        } else {
            $data = WarpingDetail::with('relBarang', 'relBarang.relTipe')->selectRaw('id_barang')->where([['id_warping', $id], ['code', 'BBW']])->groupBy('id_barang')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relBarang', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })->get();
        }
        return $data;
    }
    public function getNomorBeam(Request $request)
    {
        $term = strtolower($request->input('q')) ?? '';
        $temp = Beam::where('finish', 0)->pluck('id_nomor_beam')->toArray();
        $data = NomorBeam::whereNotIn('id', array_filter($temp))->whereRaw("LOWER(name) LIKE '%$term%'")->get();

        return $data;
    }
    public function getNoKikw($id)
    {
        $temp = WarpingDetail::where('id_warping', $id)->where('code', 'BL')->whereNotNull('id_beam')->pluck('id_beam')->toArray();
        // dd($temp);
        $data = Beam::whereIn('id', $temp)->get();
        return $data;
    }
    public function getWarna(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relWarna')->selectRaw('id_warna')->where('code', 'BBW')->groupBy('id_warna')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    });
                })->get();
        } else if ($tipe == 'output') {
            $data = Warna::where('alias', 'like', '%' . $term . '%')->get();
        } else {
            $data = WarpingDetail::with('relWarna')->selectRaw('id_warna')->where([['id_warping', $id], ['code', 'BBW']])->groupBy('id_warna')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    });
                })->get();
        }
        return $data;
    }

    public function getMotif(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
        } else if ($tipe == 'output') {
            $data = Motif::where('alias', 'like', '%' . $term . '%')->get();
        } else {
        }
        return $data;
    }

    public function getGudang(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relGudang')->selectRaw('id_gudang')->where('code', 'BBW')->groupBy('id_gudang')->where(function ($q) use ($term) {
                $q->whereHas('relGudang', function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                });
            })->get();
        } else if ($tipe == 'output') {
            $data = Gudang::where('name', 'like', '%' . $term . '%')->get();
        } else {
            // $data = WarpingDetail::selectRaw('id_gudang')->where([['id_warping', $id], ['code', 'BBW']])->groupBy('id_gudang')->get();
            $data = Gudang::where('name', 'like', '%' . $term . '%')->get();
        }
        return $data;
    }
    public function getMesin(Request $request, $mode)
    {
        $term = $request->input('q');
        if ($mode == 'parent') {
            $data = Mesin::where('jenis', 'WARPING')->where('name', 'like', '%' . $term . '%')->get();
        } else {
            $data = Mesin::where('jenis', 'LOOM')->where('name', 'like', '%' . $term . '%')->get();
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
                    ['code', 'BBW']
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
