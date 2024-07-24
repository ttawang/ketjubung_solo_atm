<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\DyeingGrey;
use App\Models\DyeingGreyDetail;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DyeingGreyController extends Controller
{
    private static $model = 'DyeingGrey';
    private static $modelDetail = 'DyeingGreyDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dyeing Grey', 'link' => route('production.dyeing_grey.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'dyeing grey', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.production.dyeing_grey.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $constructor = DyeingGrey::when($search, function ($query, $value) {
            return $query->whereRaw("nomor LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
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
        $constructor = DyeingGreyDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('relWarna', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('status', $status)
            ->where(['id_dyeing_grey' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ($request['table'] == 'tableKirim') ? ['relBarang', 'relSatuan', 'customTanggal'] : ['relBarang', 'relWarna', 'relSatuan', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $status = $request['status'];
            $data = DyeingGrey::where('id', $idParent)->first();
            $currVolume1 = 0;
            $response['render'] = view('contents.production.dyeing_grey.form-detail', compact('data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $response['render'] = view('contents.production.dyeing_grey.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DyeingGreyDetail::where('id', $id)->first();
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

            $idParent = $data->id_dyeing_grey;
            $currVolume1 = $data->volume_1;
            $response['render'] = view('contents.production.dyeing_grey.form-detail', compact('id', 'data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $data = DyeingGrey::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.dyeing_grey.form', compact('data', 'id'))->render();
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
                    $logStokPenerimaan['code']                = 'BHDG';
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
                    $filter['status'] = 'PB';
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
            $detailData = DyeingGreyDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
