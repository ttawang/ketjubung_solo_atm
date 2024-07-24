<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\DistribusiPakan;
use App\Models\DistribusiPakanDetail;
use App\Models\LogStokPenerimaan;
use App\Models\TenunDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistribusiPakanController extends Controller
{
    private static $model = 'DistribusiPakan';
    private static $modelDetail = 'DistribusiPakanDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Distribusi Pakan', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('', 'distribusi pakan', $breadcumbs, true, false, false);
        if (!$request->ajax()) return view('contents.production.distribusi_pakan.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_distribusi_pakan_detail')->selectRaw("id_distribusi_pakan, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_distribusi_pakan');
        $constructor = DistribusiPakan::leftJoinSub($sub, 'sub', function($query){
            return $query->on('tbl_distribusi_pakan.id', 'sub.id_distribusi_pakan');
        })
        ->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(nomor) LIKE '%$value%'")
                ->orwhereRaw("LOWER(tipe) LIKE '%$value%'");
        })->selectRaw('tbl_distribusi_pakan.*, sub.count_detail')->orderBy('tanggal', 'DESC');
        $attributes = ['customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $search = strtolower($request['search']['value']);
        $constructor = DistribusiPakanDetail::when($search, function ($query, $value) {
            return $query->where(function ($query) use ($value) {
                $query->whereHas('relBarang', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('relMesin', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('throughNomorKikw', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
            });
        })
            ->where('id_distribusi_pakan', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['noKikw', 'relMesin', 'relSatuan'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = DistribusiPakan::where('id', $idParent)->first();
            $attr['idLogStokKeluar'] = '';
            $attr['idLogStokMasuk'] = '';
            $attr['code'] = ($data->tipe == 'shuttle') ? 'BPS' : 'BPR';
            $attr['code'] = ($data->tipe == 'warna') ? 'BHD' : $attr['code'];
            $attr['tipe'] = $data->tipe;
            $response['render'] = view('contents.production.distribusi_pakan.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.distribusi_pakan.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DistribusiPakanDetail::where('id', $id)->first();
            $response['selected'] = [
                'select_beam' => [
                    'id'             => $data->id_beam,
                    'text'           => $data->throughNomorKikw()->value('name'),
                    'no_beam'        => $data->throughNomorBeam()->value('name'),
                    'id_mesin'       => $data->id_mesin,
                    'mesin'          => $data->relMesin()->value('name'),
                    'is_sizing'      => $data->relBeam()->value('is_sizing'),
                    'tipe_pra_tenun' => $data->relBeam()->value('tipe_pra_tenun'),
                ],

                'select_barang' => [
                    'id'            => $data->id,
                    'text'          => $data->relBarang()->value('name'),
                    'id_barang'     => $data->id_barang,
                    'id_satuan_1'   => $data->id_satuan_1,
                    'nama_satuan_1' => $data->relSatuan1()->value('name'),
                    'id_satuan_2'   => $data->id_satuan_2,
                    'nama_satuan_2' => $data->relSatuan2()->value('name'),
                    'id_warna'      => $data->id_warna,
                    'nama_warna'    => $data->relWarna()->value('name'),
                    'volume_1'      => $data->volume_1,
                    'volume_2'      => $data->volume_2,
                    'id_gudang'     => $data->id_gudang,
                    'code'          => $data->code
                ]
            ];

            $idParent = $data->id_distribusi_pakan;
            $attr['idLogStokKeluar'] = $data->id_log_stok_keluar;
            $attr['idLogStokMasuk'] = $data->id_log_stok_masuk;
            $attr['idLogStokTenun'] = $data->relTenunDetail()->value('id_log_stok_penerimaan');
            $attr['tipe'] = $data->relDistribusiPakan()->value('tipe');
            $attr['code'] = $data->code;
            $response['render'] = view('contents.production.distribusi_pakan.form-detail-edit', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = DistribusiPakan::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.distribusi_pakan.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $detail = $detailTenun = [];
                data_fill($input, '*.tanggal', $request['tanggal']);
                data_fill($input, '*.id_beam', $request['id_beam']);
                data_fill($input, '*.id_mesin', $request['id_mesin']);
                foreach ($input as $key => $value) {

                    if ($value['code'] == 'undefined') throw new Exception("Simpan Error", 1);

                    $usedCode = ($value['code'] == 'BPS') ? 'DPS' : 'DPR';
                    $volume2 = floatValue($value['volume_2']);

                    $logStokKeluar                    = unsetMultiKeys(['volume_1', 'volume_2', 'id_beam', 'id_mesin'], $value);
                    $logStokKeluar['code']            = $value['code'];
                    $logStokKeluar['volume_keluar_1'] = $value['volume_1'];
                    if (isset($value['volume_2'])) $logStokKeluar['volume_keluar_2'] = $volume2;

                    $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2', 'id_gudang'], $value);
                    $logStokMasuk['code']           = $usedCode;
                    $logStokMasuk['id_gudang']      = 7;
                    $logStokMasuk['tipe_pra_tenun'] = $request['tipe_pra_tenun'];
                    $logStokMasuk['is_sizing']      = $request['is_sizing'] ?? null;
                    $logStokMasuk['volume_masuk_1'] = $value['volume_1'];
                    if (isset($value['volume_2'])) $logStokMasuk['volume_masuk_2'] = $volume2;

                    $detail[$key] = $value;
                    $detail[$key]['id_log_stok_keluar'] = LogStokPenerimaan::create($logStokKeluar)->id;
                    $detail[$key]['id_log_stok_masuk']  = LogStokPenerimaan::create($logStokMasuk)->id;

                    // =========TENUN==========
                    $logStokTenun = unsetMultiKeys(['volume_masuk_1', 'volume_masuk_2'], $logStokMasuk);
                    $logStokTenun['volume_keluar_1'] = $value['volume_1'];
                    if (isset($value['volume_2'])) $logStokTenun['volume_keluar_2'] = $volume2;

                    $detailTenun = unsetMultiKeys(['code', 'id_gudang'], $value);
                    $detailTenun['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logStokTenun)->id;
                    $detailTenun['id_gudang']              = 7;
                    $detailTenun['code']                   = $usedCode;
                    $detailTenun['id_tenun']               = $request['id_tenun'];

                    $detail[$key]['id_tenun_detail'] = TenunDetail::create($detailTenun)->id;
                }

                data_fill($detail, '*.id_distribusi_pakan', $request['id_distribusi_pakan']);
                // data_fill($detail, '*.code', $request['code']);
                data_fill($detail, '*.created_by', Auth::id());
                data_fill($detail, '*.created_at', now());
                DistribusiPakanDetail::insert($detail);

                activity()->log("Menambah data Distribusi Pakan");
                DB::commit();
                return response($request['id_distribusi_pakan'], 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        }
        return Define::store($input, $usingModel);
    }

    public function update($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                
                if ($input['code'] == 'undefined') throw new Exception("Simpan Error", 1);

                $usedCode = ($input['code'] == 'BPS') ? 'DPS' : 'DPR';
                $input['id_mesin'] = $request['id_mesin'];
                $logStokPenerimaan['tanggal']        = $input['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['id_warna']       = $input['id_warna'];

                if (isset($input['volume_2'])) {
                    $input['volume_2'] = floatValue($input['volume_2']);
                    $logStokPenerimaan['id_satuan_2']     = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                    $logStokPenerimaan['volume_masuk_2']  = $input['volume_2'];
                } else {
                    $logStokPenerimaan['id_satuan_2']     = null;
                    $logStokPenerimaan['volume_keluar_2'] = null;
                    $logStokPenerimaan['volume_masuk_2']  = null;
                }

                $logStokKeluar = unsetMultiKeys(['volume_masuk_2'], $logStokPenerimaan);
                $logStokKeluar['id_gudang']       = $input['id_gudang'];
                $logStokKeluar['code']            = $input['code'];
                $logStokKeluar['volume_keluar_1'] = $input['volume_1'];
                $logStokKeluar['id_satuan_1']     = $input['id_satuan_1'];
                LogStokPenerimaan::where('id', $request['id_log_stok_keluar'])->update($logStokKeluar);

                $logStokMasuk = unsetMultiKeys(['volume_keluar_2'], $logStokPenerimaan);
                $logStokMasuk['id_gudang']      = 7;
                $logStokMasuk['code']           = $usedCode;
                $logStokMasuk['volume_masuk_1'] = $input['volume_1'];
                $logStokMasuk['id_satuan_1']    = $input['id_satuan_1'];
                $logStokMasuk['id_beam']        = $input['id_beam'];
                $logStokMasuk['id_mesin']       = $request['id_mesin'];
                $logStokMasuk['tipe_pra_tenun'] = $request['tipe_pra_tenun'];
                $logStokMasuk['is_sizing']      = $request['is_sizing'];
                LogStokPenerimaan::where('id', $request['id_log_stok_masuk'])->update($logStokMasuk);

                $logStokTenun = unsetMultiKeys(['volume_masuk_2'], $logStokPenerimaan);
                $logStokTenun['id_gudang']        = 7;
                $logStokTenun['code']             = $usedCode;
                $logStokTenun['volume_keluar_1']  = $input['volume_1'];
                $logStokTenun['id_satuan_1']      = $input['id_satuan_1'];
                $logStokTenun['id_beam']          = $input['id_beam'];
                $logStokTenun['id_mesin']         = $request['id_mesin'];
                $logStokTenun['tipe_pra_tenun']   = $request['tipe_pra_tenun'];
                $logStokTenun['is_sizing']        = $request['is_sizing'];
                LogStokPenerimaan::where('id', $request['id_log_stok_tenun'])->update($logStokTenun);

                $tenunDetail = unsetMultiKeys(['id_gudang', 'id_distribusi_pakan'], $input);
                $tenunDetail['id_gudang'] = 7;
                $tenunDetail['code'] = $usedCode;
                TenunDetail::where('id', $request['id_tenun_detail'])->update($tenunDetail);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        }

        return Define::update($input, $usingModel, $id);
    }

    public function destroy($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        if ($isDetail) {
            $detailData = DistribusiPakanDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok_keluar)->delete();
            LogStokPenerimaan::where('id', $detailData->id_log_stok_masuk)->delete();
            LogStokPenerimaan::where('id', $detailData->relTenunDetail()->value('id_log_stok_penerimaan'))->delete();
            TenunDetail::where('id', $detailData->id_tenun_detail)->delete();
        }
        return Define::delete($id, $usingModel);
    }


    public function store_old(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $input['id_mesin'] = $request['id_mesin'];
                $logStokPenerimaan['id_gudang']      = 7;
                $logStokPenerimaan['tanggal']        = $input['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['id_warna']       = $input['id_warna'];

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                }

                $logStokKeluar = unsetMultiKeys(['volume_masuk_2'], $logStokPenerimaan);
                $logStokKeluar['code']            = $input['code'];
                $logStokKeluar['id_gudang']       = $input['id_gudang'];
                $logStokKeluar['volume_keluar_1'] = $input['volume_1'];
                $logStokKeluar['id_satuan_1']     = $input['id_satuan_1'];
                $input['id_log_stok_keluar'] = LogStokPenerimaan::create($logStokKeluar)->id;

                $logStokMasuk = unsetMultiKeys(['volume_keluar_2'], $logStokPenerimaan);
                $logStokMasuk['code']           = ($input['code'] == 'BPR') ? 'DPR' : 'DPS';
                $logStokMasuk['id_gudang']      = 7;
                $logStokMasuk['volume_masuk_1'] = $input['volume_1'];
                $logStokMasuk['id_satuan_1']    = $input['id_satuan_1'];
                $logStokMasuk['id_beam']        = $input['id_beam'];
                $logStokMasuk['id_mesin']       = $request['id_mesin'];
                $logStokMasuk['tipe_pra_tenun'] = $request['tipe_pra_tenun'];
                $logStokMasuk['is_sizing']      = $request['is_sizing'];
                $input['id_log_stok_masuk'] = LogStokPenerimaan::create($logStokMasuk)->id;
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        }
        return Define::store($input, $usingModel);
    }
}
