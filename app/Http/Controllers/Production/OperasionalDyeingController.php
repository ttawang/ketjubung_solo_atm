<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\LogStokPenerimaan;
use App\Models\OperasionalDyeing;
use App\Models\OperasionalDyeingDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperasionalDyeingController extends Controller
{
    private static $model = 'OperasionalDyeing';
    private static $modelDetail = 'OperasionalDyeingDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Dyeing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Limbah & Cuci Mesin', 'link' => route('production.operasional_dyeing.index'), 'active' => 'active']];
        $menuAssets = menuAssets('dyeing', 'operasional dyeing', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.operasional_dyeing.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $constructor = OperasionalDyeing::when($search, function ($query, $value) {
            return $query->whereRaw("nomor LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $search = $request['search']['value'];
        $constructor = OperasionalDyeingDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where(['id_operasional_dyeing' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ['relBarang', 'relSatuan1', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = OperasionalDyeing::where('id', $idParent)->first();
            $currVolume = 0;
            $response['render'] = view('contents.production.operasional_dyeing.form-detail', compact('data', 'idParent', 'currVolume'))->render();
        } else {
            $response['render'] = view('contents.production.operasional_dyeing.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = OperasionalDyeingDetail::where('id', $id)->first();

            $filter['id_gudang']   = $data->id_gudang;
            $filter['id_barang']   = $data->id_barang;
            $filter['id_satuan_1'] = $data->id_satuan;
            $filter['code']        = 'DW';
            $checkStokUtama = checkStokBarang($filter) + $data->volume;

            $response['selected'] = [
                'select_gudang_2' => [
                    'id'   => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'         => $data->id,
                    'text'       => $data->relBarang()->value('name'),
                    'id_barang'  => $data->id_barang,
                    'id_warna'   => $data->id_warna,
                    'stok_utama' => $checkStokUtama,
                    'volume_1'   => $data->volume
                ]
            ];

            $idParent = $data->id_operasional_dyeing;
            $currVolume = $data->volume;
            $response['render'] = view('contents.production.operasional_dyeing.form-detail', compact('id', 'data', 'idParent', 'currVolume'))->render();
        } else {
            $data = OperasionalDyeing::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.operasional_dyeing.form', compact('data', 'id'))->render();
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
                $input['volume']                      = floatValue($input['volume']);
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan'];
                $logStokPenerimaan['volume_keluar_1'] = $input['volume'];
                $logStokPenerimaan['code']            = 'DW';

                $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                $checkStokBarang = checkStokBarang($filter);
                if ($input['volume'] > $checkStokBarang) throw new Exception("Stok Chemical Dyeing Tidak Cukup", 1);

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
                $input['volume']                      = floatValue($input['volume']);
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan'];
                $logStokPenerimaan['volume_keluar_1'] = $input['volume'];
                $logStokPenerimaan['code']            = 'DW';

                $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                $checkStokBarang = checkStokBarang($filter) + $request['curr_volume'];
                if ($input['volume'] > $checkStokBarang) throw new Exception("Stok Chemical Dyeing Tidak Cukup", 1);

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
            $detailData = OperasionalDyeingDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
