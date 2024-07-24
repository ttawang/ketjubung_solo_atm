<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Doubling;
use App\Models\DoublingDetail;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoublingController extends Controller
{
    private static $model = 'Doubling';
    private static $modelDetail = 'DoublingDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Doubling - Jasa Luar', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('penerimaan', 'doubling', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.doubling.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_doubling_detail')->selectRaw("id_doubling, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_doubling');
        $constructor = Doubling::leftJoinSub($sub, 'sub', function ($query) {
            return $query->on('tbl_doubling.id', 'sub.id_doubling');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(nomor) LIKE '%$value%'");
        })->selectRaw('tbl_doubling.*, sub.count_detail')->orderBy('created_at', 'DESC');
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
        $constructor = DoublingDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('status', $status)
            ->where(['id_doubling' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ['relBarang', 'relSatuan', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $status = $request['status'];
            $data = Doubling::where('id', $idParent)->first();
            $currVolume1 = 0;
            $response['selected'] = [
                'select_gudang_2' => [
                    'id'   => 1,
                    'text' => 'Gudang Logistik'
                ],
                'select_satuan_1' => [
                    'id'   => 2,
                    'text' => 'kg'
                ]
            ];
            $response['render'] = view('contents.production.doubling.form-detail', compact('data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $response['render'] = view('contents.production.doubling.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DoublingDetail::where('id', $id)->first();
            $status = $request['status'];
            $response['selected'] = [
                'select_gudang_2' => [
                    'id'   => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'   => $data->id,
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

            $idParent = $data->id_doubling;
            $currVolume1 = $data->volume_1;
            $response['render'] = view('contents.production.doubling.form-detail', compact('id', 'data', 'idParent', 'status', 'currVolume1'))->render();
        } else {
            $data = Doubling::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $response['render'] = view('contents.production.doubling.form', compact('data', 'id'))->render();
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
                $input['volume_1'] = floatValue($input['volume_1']);
                if ($input['status'] == 'KIRIM') {
                    $logStokPenerimaan['tanggal']         = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                    $logStokPenerimaan['code']            = 'PB';

                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $checkStokBarangBBD = checkStokBarang($filter);
                    if ($input['volume_1'] > $checkStokBarangBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                } else {
                    $logStokPenerimaan['tanggal']             = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']           = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']           = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']         = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_masuk_1']      = $input['volume_1'];
                    $logStokPenerimaan['code']                = 'PB';
                    $logStokPenerimaan['is_doubling']         = 'YA';
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
                $input['volume_1'] = floatValue($input['volume_1']);
                if ($input['status'] == 'KIRIM') {
                    $logStokPenerimaan['tanggal']         = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];

                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $filter['code'] = 'BBD';
                    $checkStokBarangBBD = checkStokBarang($filter) + $request['curr_volume_1'];
                    if ($input['volume_1'] > $checkStokBarangBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                } else {
                    $logStokPenerimaan['tanggal']             = $input['tanggal'];
                    $logStokPenerimaan['id_gudang']           = $input['id_gudang'];
                    $logStokPenerimaan['id_barang']           = $input['id_barang'];
                    $logStokPenerimaan['id_satuan_1']         = $input['id_satuan_1'];
                    $logStokPenerimaan['volume_masuk_1']      = $input['volume_1'];
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
            $detailData = DoublingDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
