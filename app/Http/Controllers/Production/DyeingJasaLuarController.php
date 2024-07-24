<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\DyeingJasaLuar;
use App\Models\DyeingJasaLuarDetail;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DyeingJasaLuarController extends Controller
{
    private static $model = 'DyeingJasaLuar';
    private static $modelDetail = 'DyeingJasaLuarDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dyeing - Jasa Luar', 'link' => route('production.dyeing_jasa_luar.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'dyeing jasa luar', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.production.dyeing_jasa_luar.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_dyeing_jasa_luar_detail')->selectRaw("id_dyeing_jasa_luar, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_dyeing_jasa_luar');
        $constructor = DyeingJasaLuar::leftJoinSub($sub, 'sub', function ($query) {
            return $query->on('tbl_dyeing_jasa_luar.id', 'sub.id_dyeing_jasa_luar');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("nomor LIKE '%$value%'");
        })->selectRaw('tbl_dyeing_jasa_luar.*, sub.count_detail')->orderBy('created_at', 'DESC');
        $attributes = ['relSupplier'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $status = $request['status'];
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $input['extraData'] = ['status' => $status];
        $search = $request['search']['value'];
        $constructor = DyeingJasaLuarDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('relWarna', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('status', $status)
            ->where(['id_dyeing_jasa_luar' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ($request['table'] == 'tableKirim') ? ['relBarang', 'relSatuan', 'customTanggal'] : ['relBarang', 'relWarna', 'relSatuan', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $status = $request['status'];
            $data = DyeingJasaLuar::where('id', $idParent)->first();
            $currVolume1 = 0;
            $response['render'] = view('contents.production.dyeing_jasa_luar.form-detail', compact('data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $response['render'] = view('contents.production.dyeing_jasa_luar.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DyeingJasaLuarDetail::where('id', $id)->first();
            $status = $request['status'];
            $response['selected'] = [
                'select_gudang_2' => [
                    'id'   => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'   => ($status == 'KIRIM') ? $data->id : $data->id_parent_detail,
                    'text' => $data->relBarang()->value('name'),
                    'id_barang' => $data->id_barang
                ],
                'select_satuan_1' => [
                    'id'   => $data->id_satuan_1,
                    'text' => $data->relSatuan1()->value('name')
                ]
            ];

            if ($data->id_satuan_2 != null) {
                $arraySatuan2 = [
                    'select_satuan_2' => [
                        'id'   => $data->id_satuan_2,
                        'text' => $data->relSatuan2()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arraySatuan2);
            }

            if ($data->id_warna != null) {
                $arrayWarna = [
                    'select_warna' => [
                        'id'   => $data->id_warna,
                        'text' => $data->relWarna()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayWarna);
            }

            $idParent = $data->id_dyeing_jasa_luar;
            $currVolume1 = $data->volume_1;
            $response['render'] = view('contents.production.dyeing_jasa_luar.form-detail', compact('id', 'data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $data = DyeingJasaLuar::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $response['render'] = view('contents.production.dyeing_jasa_luar.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        try {
            if ($isDetail) {
                if ($input['status'] == 'KIRIM') {
                    $input['volume_1']                    = floatValue($input['volume_1']);
                    $logStokPenerimaan['tanggal']         = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                    $logStokPenerimaan['code']            = 'PB';

                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $checkStokBarangPB = checkStokBarang($filter);
                    if ($input['volume_1'] > $checkStokBarangPB) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                } else {
                    $input['volume_1']                        = floatValue($input['volume_1']);
                    $input['volume_2']                        = floatValue($input['volume_2']);
                    $logStokPenerimaan['tanggal']             = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']           = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']           = $input['id_barang'];
                    $logStokPenerimaan['id_warna']            = $input['id_warna'];
                    $logStokPenerimaan['id_satuan_1']         = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_masuk_1']      = $input['volume_1'];
                    $logStokPenerimaan['id_satuan_2']         = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_2']      = $input['volume_2'];
                    $logStokPenerimaan['code']                = 'BHD';
                    $logStokPenerimaan['is_dyeing_jasa_luar'] = 'YA';
                }

                $input['id_log_stok'] = LogStokPenerimaan::create($logStokPenerimaan)->id;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
        return Define::store($input, $usingModel);
    }

    public function update($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        try {
            if ($isDetail) {
                if ($input['status'] == 'KIRIM') {
                    $input['volume_1']                    = floatValue($input['volume_1']);
                    $logStokPenerimaan['tanggal']         = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];

                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $filter['code'] = 'PB';
                    $checkStokBarangPB = checkStokBarang($filter) + $request['curr_volume_1'];
                    if ($input['volume_1'] > $checkStokBarangPB) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                } else {
                    $input['volume_1']                        = floatValue($input['volume_1']);
                    $input['volume_2']                        = floatValue($input['volume_2']);
                    $logStokPenerimaan['tanggal']             = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']           = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']           = $input['id_barang'];
                    $logStokPenerimaan['id_warna']            = $input['id_warna'];
                    $logStokPenerimaan['id_satuan_1']         = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_masuk_1']      = $input['volume_1'];
                    $logStokPenerimaan['id_satuan_2']         = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_2']      = $input['volume_2'];
                }

                LogStokPenerimaan::where('id', $request['id_log_stok'])->update($logStokPenerimaan);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
        return Define::update($input, $usingModel, $id);
    }

    public function destroy($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        if ($isDetail) {
            $detailData = DyeingJasaLuarDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
