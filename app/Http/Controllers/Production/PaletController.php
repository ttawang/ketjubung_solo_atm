<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Date;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\LogStokPenerimaan;
use App\Models\Palet;
use App\Models\PaletDetail;
use App\Models\Warna;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PaletController extends Controller
{
    private static $model = 'Palet';
    private static $modelDetail = 'PaletDetail';

    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pakan', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('weaving', 'pakan', $data['breadcumbs'], true, true, true, true);
        return view('contents.production.weaving.palet.index', $data);
    }
    public function view($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            return view('contents.production.weaving.palet.parent');
        } else {
            if (!$tipe) {
                $data['data'] = Palet::find($id);
                return view('contents.production.weaving.palet.detail', $data);
            } else {
                $data['barang'] = $this->getBarang($tipe, $id);
                $data['gudang'] = $this->getGudang($tipe, $id);
                $data['warna'] = $this->getWarna($tipe, $id);
                if ($tipe == 'input') {
                    return view('contents.production.weaving.palet.input', $data);
                } else if ($tipe == 'output') {
                    return view('contents.production.weaving.palet.output', $data);
                }
            }
        }
    }
    public function table($mode, $id = null, $tipe = null)
    {
        if ($mode == 'parent') {
            $temp = Palet::orderBy('created_at', 'desc');
            return DataTables::of($temp)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($i) {
                    return Date::format($i->tanggal, 98);
                })
                ->addColumn('action', function ($i) {
                    $action = actionBtn($i->id, true, true, true);
                    return $action;
                })
                ->rawColumns(['action'])
                ->make('true');
        } else {
            if ($tipe == 'input') {
                $temp = PaletDetail::where([['id_palet', $id], ['code', 'BBW']])->orderBy('created_at', 'desc');
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
                        $action = actionBtn($i->id, false, false, true);
                        return $action;
                    })
                    ->rawColumns(['action'])
                    ->make('true');
            } elseif ($tipe == 'output') {
                $temp = PaletDetail::where('id_palet', $id)->whereIn('code',['BPR','BPS'])->orderBy('created_at', 'desc');
                return DataTables::of($temp)
                    ->addIndexColumn()
                    ->addColumn('tanggal', function ($i) {
                        return Date::format($i->tanggal, 98);
                    })
                    ->addColumn('proses', function ($i) {
                        if($i->code == 'BPR'){
                            return 'Rapier';
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
                    ->addColumn('action', function ($i) {
                        $action = actionBtn($i->id, false, false, true);
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
            $data = $request->except(['id', '_token', 'mode', 'tipe','proses']);
            $dataLog = $request->except(['id', '_token', 'mode', 'tipe', 'id_palet', 'volume_1', 'volume_2', 'nomor', 'proses']);

            $rule = $this->cekRequest($request, $mode, $tipe);

            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                if (!$id) {
                    if ($mode == 'parent') {
                        Palet::create($data);
                        logHistory(self::$model, 'create');
                    } else {
                        if ($tipe == 'input') {
                            $data['code'] = 'BBW';
                            $dataLog['volume_keluar_1'] = $data['volume_1'];
                            $dataLog['volume_keluar_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        } elseif ($tipe == 'output') {
                            if ($request->proses === 'rapier') {
                                $data['code'] = 'BPR';
                            } else {
                                $data['code'] = 'BPS';
                            }
                            $dataLog['volume_masuk_1'] = $data['volume_1'];
                            // $dataLog['volume_masuk_2'] = $data['volume_2'];
                            $dataLog['code'] = $data['code'];
                        }
                        $logId = LogStokPenerimaan::create($dataLog)->id;
                        $data['id_log_stok_penerimaan'] = $logId;
                        PaletDetail::create($data);
                        logHistory(self::$modelDetail, 'create');
                    }
                } else {
                    if ($mode == 'parent') {
                        $data['updated_by'] = Auth::id();
                        Palet::find($id)->update($data);
                        logHistory(self::$model, 'update');
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
            if ($tipe == 'input') {
                $rules['volume_2'] = 'required|numeric|gt:0|not_in:0';
                $messages['volume_2.required'] = 'volume 2 harus diisi';
                $messages['volume_2.numeric'] = 'volume 2 hanya berupa angka';
                $messages['volume_2.not_in'] = 'volume 2 tidak boleh 0';
                $messages['volume_2.gt'] = 'volume 2 harus lebih besar dari 0';
            } else {
                $rules['proses'] = 'required|not_in:0';
                $messages['proses.required'] = 'proses harus diisi';
                $messages['proses.not_in'] = 'proses harus diisi';
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
                Palet::find($id)->delete();
                logHistory(self::$model, 'delete');
            } else {
                $logId = PaletDetail::find($id)->id_log_stok_penerimaan;
                PaletDetail::find($id)->delete();
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
            $data = Palet::find($id);
        } else {
            $data = PaletDetail::find($id);
        }

        return response()->json($data);
    }
    function getBarang($tipe)
    {
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::selectRaw('id_barang')->where('code', 'BBW')->groupBy('id_barang')->get();
        } else if ($tipe == 'output') {
            $data = Barang::whereIn('id_tipe', [5])->get();
        }
        return $data;
    }
    function getWarna($tipe, $id)
    {
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::selectRaw('id_warna')->where('code', 'BBW')->groupBy('id_warna')->get();
        } else if ($tipe == 'output') {
            $data = PaletDetail::where('id_palet', $id)->get();
        }
        return $data;
    }
    function getGudang($tipe)
    {
        if ($tipe == 'input') {
            $data = LogStokPenerimaan::selectRaw('id_gudang')->where('code', 'BBW')->groupBy('id_gudang')->get();
        } else if ($tipe == 'output') {
            $data = Gudang::all();
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
