<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\LogStokPenerimaan;
use App\Models\Pakan;
use App\Models\PakanDetail;
use App\Models\PengirimanBarangDetail;
use App\Models\Warna;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class PakanController extends Controller
{
    private static $model = 'Pakan';
    private static $modelDetail = 'PakanDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pakan', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('weaving', 'pakan', $data['breadcumbs'], true, false, true, true);
        return view('contents.production.weaving.pakan.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.weaving.pakan.parent');
        } else {
            $data['data'] = Pakan::find($id);
            if (!$tipe) {
                return view('contents.production.weaving.pakan.detail', $data);
            } else {
                if ($tipe == 'input') {
                    return view('contents.production.weaving.pakan.input', $data);
                } else if ($tipe == 'output') {
                    return view('contents.production.weaving.pakan.output', $data);
                } else {
                    return view('contents.production.weaving.pakan.sisa', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $subQuery = DB::table('tbl_pakan_detail')->selectRaw('id_pakan, COUNT(*) as jumlah_pakan_detail')->whereNull('deleted_at')->groupBy('id_pakan');
            $temp = Pakan::leftJoinSub($subQuery, 'sq', function($query){
                return $query->on('tbl_pakan.id', 'sq.id_pakan');
            })
            ->selectRaw('tbl_pakan.*, sq.jumlah_pakan_detail')
            ->orderBy('created_at', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('action', function ($i) {
                    $temp = Pakan::find($i->id)->validated_at;
                    $validasi = [
                        'status' => true,
                        'data' => $temp,
                        'model' => 'Pakan'
                    ];
                    $action = actionBtn($i->id, true, true, $i->jumlah_pakan_detail== 0, $validasi);
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            if ($tipe == 'input') {
                $temp = PakanDetail::where([['id_pakan', $id]])->where('code', 'BPR')->orderBy('created_at', 'desc');
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
                        $temp = Pakan::find($i->id_pakan)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Pakan'
                        ];
                        $action = actionBtn($i->id, false, false, false, $validasi);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } elseif ($tipe == 'output') {
                $temp = PakanDetail::where('id_pakan', $id)->where('code', 'BPS')->orderBy('created_at', 'desc');
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('proses', function ($i) {
                        if ($i->code == 'BPR') {
                            return 'Rappier';
                        } else {
                            return 'Shuttle';
                        }
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
                    ->addColumn('satuan_1', function ($i) {
                        return $i->relSatuan1->name;
                    })
                    ->addColumn('satuan_2', function ($i) {
                        $data = ($i->id_satuan_2) ? $i->relSatuan2->name : '';
                        return $data;
                    })
                    ->addColumn('action', function ($i) {
                        $temp = Pakan::find($i->id_pakan)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Pakan'
                        ];
                        $action = actionBtn($i->id, false, false, true, $validasi);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } else {
                $temp = PakanDetail::where('id_pakan', $id)->whereIn('code', ['BBPS'])->orderBy('created_at', 'desc');
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
                        $temp = Pakan::find($i->id_pakan)->validated_at;
                        $validasi = [
                            'status' => false,
                            'data' => $temp,
                            'model' => 'Pakan'
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
            $data = $request->except(['id', '_token', 'mode', 'tipe', 'proses', 'is_auto']);
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'id_pakan', 'volume_1', 'volume_2', 'nomor', 'proses', 'is_auto']);

            $rule = $this->cekRequest($request, $mode, $tipe);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        $idPakan = Pakan::create($data)->id;

                        if ($request->is_auto == 'YA') {
                            $dataPakanDetail = $dataPakanDetailRappier = [];
                            PengirimanBarangDetail::whereHas('relPengirimanBarang', function ($query) {
                                return $query->where('id_tipe_pengiriman', 21);
                            })->where('status', 'TUJUAN')->whereNull('id_pakan_detail')->where('tanggal', $data['tanggal'])
                                ->each(function ($item, $key) use ($idPakan, &$dataPakanDetail, &$dataPakanDetailRappier) {
                                    $item = $item->toArray();
                                    $logStok['tanggal']         = $item['tanggal'];
                                    $logStok['id_barang']       = $item['id_barang'];
                                    $logStok['id_warna']        = $item['id_warna'];
                                    $logStok['id_gudang']       = $item['id_gudang'];
                                    $logStok['code']            = 'BBWP';
                                    $logStok['volume_keluar_1'] = $item['volume_1'];
                                    $logStok['id_satuan_1']     = $item['id_satuan_1'];
                                    $logStok['volume_keluar_2'] = $item['volume_2'];
                                    $logStok['id_satuan_2']     = $item['id_satuan_2'];
                                    $idLogStokPenerimaan = LogStokPenerimaan::create($logStok)->id;

                                    $dataPakanDetail[$key]['id_pakan']               = $idPakan;
                                    $dataPakanDetail[$key]['tanggal']                = $item['tanggal'];
                                    $dataPakanDetail[$key]['id_barang']              = $item['id_barang'];
                                    $dataPakanDetail[$key]['id_warna']               = $item['id_warna'];
                                    $dataPakanDetail[$key]['id_gudang']              = $item['id_gudang'];
                                    $dataPakanDetail[$key]['id_log_stok_penerimaan'] = $idLogStokPenerimaan;
                                    $dataPakanDetail[$key]['volume_1']               = $item['volume_1'];
                                    $dataPakanDetail[$key]['id_satuan_1']            = $item['id_satuan_1'];
                                    $dataPakanDetail[$key]['volume_2']               = $item['volume_2'];
                                    $dataPakanDetail[$key]['id_satuan_2']            = $item['id_satuan_2'];
                                    $dataPakanDetail[$key]['code']                   = 'BBWP';
                                    $dataPakanDetail[$key]['created_by']             = Auth::id();
                                    $dataPakanDetail[$key]['created_at']             = now();

                                    $idDetailPakan = PakanDetail::insertGetId($dataPakanDetail[$key]);

                                    $logStokRappier = unsetMultiKeys(['code', 'id_barang', 'volume_keluar_1', 'volume_keluar_2'], $logStok);
                                    $logStokRappier['code'] = 'BPR';
                                    
                                    $checkIdBarang = fixBenangPakan($item['id_barang']);
                                    if ($checkIdBarang['id'] == 0) throw new Exception("Error Pakan {$checkIdBarang['name']}", 1);
                                    $logStokRappier['id_barang'] = $checkIdBarang['id'];

                                    $logStokRappier['volume_masuk_1'] = $item['volume_1'];
                                    $logStokRappier['volume_masuk_2'] = $item['volume_2'];
                                    $idLogStokPenerimaanRappier = LogStokPenerimaan::create($logStokRappier)->id;

                                    $dataPakanDetailRappier[$key] = unsetMultiKeys(['code', 'id_barang', 'id_log_stok_penerimaan'], $dataPakanDetail[$key]);
                                    $dataPakanDetailRappier[$key]['id_parent_detail'] = $idDetailPakan;
                                    $dataPakanDetailRappier[$key]['code'] = 'BPR';
                                    $dataPakanDetailRappier[$key]['id_barang'] = $logStokRappier['id_barang'];
                                    $dataPakanDetailRappier[$key]['id_log_stok_penerimaan'] = $idLogStokPenerimaanRappier;

                                    DB::table('tbl_pengiriman_barang_detail')->where('id', $item['id'])->update(['id_pakan_detail' => $idDetailPakan]);
                                });

                            if (!empty($dataPakanDetailRappier)) PakanDetail::insert($dataPakanDetailRappier);
                        }
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = $request['code'];
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['volume_keluar_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        } elseif ($tipe == 'output') {
                            if ($request->proses === 'rappier') {
                                $data['code'] = 'BPR';
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                            } else {
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                                $data['code'] = 'BPS';
                            }
                            $dataLog['code'] = $data['code'];
                        } else {
                            $data['code'] = 'BBPS';
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = 'BBWP';
                        }
                        $logId = LogStokPenerimaan::create($dataLog)->id;
                        $data['id_log_stok_penerimaan'] = $logId;
                        PakanDetail::create($data);
                        logHistory(self::$modelDetail, 'create');
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        Pakan::find($id)->update($data);
                        logHistory(self::$model, 'update');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = $request['code'];
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['volume_keluar_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        } elseif ($tipe == 'output') {
                            /*if (!$request->has('volume_2')) {
                                $data['code'] = 'BPS';
                                $data['volume_2'] = null;
                                $data['id_satuan_2'] = null;
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                                $dataLog['id_satuan_2'] = $data['id_satuan_2'];
                            } else {
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                                $data['code'] = 'BPR';
                            }
                            $dataLog['code'] = $data['code'];*/
                            if ($request->proses === 'rappier') {
                                $data['code'] = 'BPR';
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                            } else {
                                $dataLog['volume_masuk_1'] = $data['volume_1'];
                                $dataLog['volume_masuk_2'] = $data['volume_2'];
                                $data['code'] = 'BPS';
                            }
                            $dataLog['code'] = $data['code'];
                        } else {
                            $data['code'] = 'BBPS';
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = 'BBWP';
                        }
                        $pakan = PakanDetail::find($id);
                        LogStokPenerimaan::find($pakan->id_log_stok_penerimaan)->update($dataLog);
                        $pakan->update($data);
                        logHistory(self::$modelDetail, 'update');
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
        if ($mode == 'parent') {
            $rules['nomor'] = 'required';
            $messages['nomor.required'] = 'nomor harus diisi';
        } else {
            $rules['id_barang'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['id_warna'] = 'required|not_in:0';
            $rules['id_gudang'] = 'required|not_in:0';
            $rules['volume_1'] = 'required|numeric|gt:0|not_in:0';
            if ($tipe == 'input' || $tipe == 'sisa') {
                $rules['volume_2'] = 'required|numeric|gt:0|not_in:0';
                $messages['volume_2.required'] = 'volume 2 harus diisi';
                $messages['volume_2.numeric'] = 'volume 2 hanya berupa angka';
                $messages['volume_2.not_in'] = 'volume 2 tidak boleh 0';
                $messages['volume_2.gt'] = 'volume 2 harus lebih besar dari 0';
            } else {
                $rules['proses'] = 'required|not_in:0';
                $messages['proses.required'] = 'proses harus diisi';
                $messages['proses.not_in'] = 'proses harus diisi';
                // if ($request->proses == 'rappier') {
                $rules['volume_2'] = 'required|numeric|gt:0|not_in:0';
                $messages['volume_2.required'] = 'volume 2 harus diisi';
                $messages['volume_2.numeric'] = 'volume 2 hanya berupa angka';
                $messages['volume_2.not_in'] = 'volume 2 tidak boleh 0';
                $messages['volume_2.gt'] = 'volume 2 harus lebih besar dari 0';
                // }
            }
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
                Pakan::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $fetch = PakanDetail::find($id);
                $logId = $fetch->id_log_stok_penerimaan;

                if ($fetch->id_parent_detail != null) {
                    $parentDetailId = $fetch->id_parent_detail;
                    $logIdParentDetail = PakanDetail::find($parentDetailId)->id_log_stok_penerimaan;

                    PakanDetail::find($parentDetailId)->delete();
                    LogStokPenerimaan::find($logIdParentDetail)->delete();
                }

                PakanDetail::find($id)->delete();
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
    public function getData($id, $mode)
    {
        if ($mode == 'parent') {
            $data = Pakan::find($id);
        } else {
            $data = PakanDetail::with('relBarang', 'relGudang', 'relWarna')->find($id);
        }

        return response()->json($data);
    }
    function getBarang(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relBarang')->selectRaw('id_barang')->where('code', 'BBWP')->groupBy('id_barang')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relBarang', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })->get();
        } else if ($tipe == 'output') {
            $data = Barang::whereIn('id_tipe', [5])->where('name', 'like', '%' . $term . '%')->get();
        } else {
            $data = PakanDetail::with('relBarang')->selectRaw('id_barang')->where('id_pakan', $id)->where('code', 'BBWP')->groupBy('id_barang')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relBarang', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })->get();
        }
        return $data;
    }
    function getWarna(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relWarna')->selectRaw('id_warna')->where('code', 'BBWP')->groupBy('id_warna')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    });
                })->get();
        } else if ($tipe == 'output') {
            // $data = PakanDetail::with('relWarna')->selectRaw('id_warna')->where('id_pakan', $id)->groupBy('id_warna')
            //     ->where(function ($q) use ($term) {
            //         $q->whereHas('relWarna', function ($q) use ($term) {
            //             $q->where('alias', 'like', '%' . $term . '%');
            //         });
            //     })->get();
            $data = Warna::where('jenis', 'SINGLE')->where('name', 'like', '%' . $term . '%')->get();
        } else {
            $data = PakanDetail::with('relWarna')->selectRaw('id_warna')->where('id_pakan', $id)->where('code', 'BBWP')->groupBy('id_warna')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relWarna', function ($q) use ($term) {
                        $q->where('alias', 'like', '%' . $term . '%');
                    });
                })->get();
        }
        return $data;
    }
    function getGudang(Request $request, $tipe, $id = null)
    {
        $term = $request->input('q');
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::with('relGudang')->selectRaw('id_gudang')->where('code', 'BBWP')->groupBy('id_gudang')
                ->where(function ($q) use ($term) {
                    $q->whereHas('relGudang', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
                })->get();
        } else if ($tipe == 'output') {
            $data = Gudang::where('id', 7)->where('name', 'like', '%' . $term . '%')->get();
        } else {
            $data = Gudang::where('id', 7)->where('name', 'like', '%' . $term . '%')->get();
        }
        return $data;
    }
    public function getStokBarang($barang, $warna, $gudang, $tipe = null)
    {
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::selectRaw('
                    id_satuan_1,
                    sum(coalesce(volume_masuk_1,0)::DECIMAL) - sum(coalesce(volume_keluar_1,0)::DECIMAL) as stok_1,
                    id_satuan_2,
                    sum(coalesce(volume_masuk_2,0)::DECIMAL) - sum(coalesce(volume_keluar_2,0)::DECIMAL) as stok_2
                ')
                ->where([
                    ['id_barang', $barang],
                    ['id_warna', $warna],
                    ['id_gudang', $gudang],
                    ['id_satuan_1', 1],
                    ['id_satuan_2', 2],
                    ['code', 'BBWP']
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

    public function simpan_shuttle(Request $request){
        DB::beginTransaction();
        try {
            $data = $request->all();
            $dataLogRappier['tanggal']         = $data['tanggal'];
            $dataLogRappier['code']            = 'BPR';
            $dataLogRappier['id_gudang']       = 7;
            $dataLogRappier['id_barang']       = $data['id_barang'];
            $dataLogRappier['id_warna']        = $data['id_warna'];
            $dataLogRappier['id_satuan_1']     = 1;
            $dataLogRappier['volume_keluar_1'] = $data['volume_1'];
            $dataLogRappier['id_satuan_2']     = 2;
            $dataLogRappier['volume_keluar_2'] = $data['volume_2'];
            $idLogStokPenerimaanRappier = LogStokPenerimaan::create($dataLogRappier)->id;

            $pakanRappierDetail = unsetMultiKeys(['code', 'volume_keluar_1', 'volume_keluar_2'], $dataLogRappier);
            $pakanRappierDetail['code'] = 'BPRS';
            $pakanRappierDetail['volume_1'] = $data['volume_1'];
            $pakanRappierDetail['volume_2'] = $data['volume_2'];
            $pakanRappierDetail['id_log_stok_penerimaan'] = $idLogStokPenerimaanRappier;
            $pakanRappierDetail['id_pakan'] = $data['id_pakan'];
            $idPakanDetail = PakanDetail::create($pakanRappierDetail)->id;

            $dataLogShuttle = unsetMultiKeys(['code', 'volume_keluar_1', 'id_satuan_1', 'volume_keluar_2'], $dataLogRappier);
            $dataLogShuttle['code'] = 'BPS';
            $dataLogShuttle['volume_masuk_1'] = $data['pcs'];
            $dataLogShuttle['id_satuan_1'] = 4;
            $dataLogShuttle['volume_masuk_2'] = $data['volume_2'];
            $idLogStokPenerimaanShuttle = LogStokPenerimaan::create($dataLogShuttle)->id;

            $pakanShuttleDetail = unsetMultiKeys(['volume_masuk_1', 'volume_masuk_2'], $dataLogShuttle);
            $pakanShuttleDetail['id_parent_detail'] = $idPakanDetail;
            $pakanShuttleDetail['volume_1'] = $data['pcs'];
            $pakanShuttleDetail['volume_2'] = $data['volume_2'];
            $pakanShuttleDetail['id_log_stok_penerimaan'] = $idLogStokPenerimaanShuttle;
            $pakanShuttleDetail['id_pakan'] = $data['id_pakan'];
            PakanDetail::create($pakanShuttleDetail);
            
            DB::commit();
            return response('Data Pakan Shuttle berhasil ditambahkan!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }
}
