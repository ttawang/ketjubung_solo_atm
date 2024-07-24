<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\Cucuk;
use App\Models\CucukDetail;
use App\Models\LogStokPenerimaan;
use App\Models\PenomoranBeamRetur;
use App\Models\WarpingDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CucukController extends Controller
{
    private static $model = 'Cucuk';
    private static $modelDetail = 'CucukDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Cucuk', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('weaving', 'cucuk', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.weaving.cucuk.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = strtolower($request['search']['value']) ?? '';
        $constructor = Cucuk::when($search, function ($query, $value) {
            return $query->whereHas('throughNomorBeam', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('throughNomorKikw', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })->orderBy('created_at', 'DESC');
        $attributes = ['customTanggal', 'noBeam', 'noKikw', 'relMesin', 'tipe_beam', 'is_sizing', 'jumlahBeam'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $search = $request['search']['value'];
        $constructor = CucukDetail::when($search, function ($query, $value) {
            return $query->whereHas('relPekerja', function ($query) use ($value) {
                return $query->where('name', 'LIKE', "%$value%");
            });
        })
            ->where('id_cucuk', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['customTanggal', 'relPekerja'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Cucuk::where('id', $idParent)->first();
            $response['render'] = view('contents.production.weaving.cucuk.form-detail', compact('data', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.weaving.cucuk.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = CucukDetail::where('id', $id)->first();
            $response['selected'] = [
                'select_pekerja' => [
                    'id'   => $data->id_pekerja,
                    'text' => $data->relPekerja()->value('name')
                ]
            ];

            $idParent = $data->id_cucuk;
            $response['render'] = view('contents.production.weaving.cucuk.form-detail', compact('id', 'data', 'idParent'))->render();
        } else {
            $data = Cucuk::where('id', $id)->first();
            $response['selected'] = [
                'select_beam' => [
                    'id'        => $data->id_beam,
                    'text'      => $data->throughNomorKikw()->value('name'),
                    'no_beam'   => $data->throughNomorBeam()->value('name'),
                    'id_mesin'  => $data->id_mesin,
                    'mesin'     => $data->relMesin()->value('name'),
                    'is_sizing' => $data->relBeam()->value('is_sizing'),
                ]
            ];
            $response['render'] = view('contents.production.weaving.cucuk.form', compact('data', 'id'))->render();
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
            if (!$isDetail) {
                $input['id_mesin'] = $request['id_mesin'];
                Beam::where('id', $input['id_beam'])->update(['tipe_pra_tenun' => $request['tipe_pra_tenun']]);
                $logStokPenerimaan = [];
                WarpingDetail::where('id_beam', $input['id_beam'])->each(function ($item) use (&$logStokPenerimaan, $request) {
                    $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $item['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $item['id_barang'];
                    $logStokPenerimaan['id_warna']        = $item['id_warna'];
                    $logStokPenerimaan['id_motif']        = $item['id_motif'];
                    $logStokPenerimaan['id_satuan_1']     = $item['id_satuan_1'];
                    $logStokPenerimaan['id_satuan_2']     = $item['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1']  = $item['volume_1'];
                    $logStokPenerimaan['volume_masuk_2']  = $item['volume_2'];
                    $logStokPenerimaan['volume_keluar_1'] = $item['volume_1'];
                    $logStokPenerimaan['volume_keluar_2'] = $item['volume_2'];
                    $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                    $logStokPenerimaan['id_beam']         = $item['id_beam'];
                    $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                    $logStokPenerimaan['code']            = $item['code'];
                });

                if (empty($logStokPenerimaan)) {
                    $dataBeamStokawal = (array) DB::table('log_stok_penerimaan')->where('code', 'BL')->where(['id_beam' => $input['id_beam']])->orderBy('id', 'asc')->first();
                    $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $dataBeamStokawal['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $dataBeamStokawal['id_barang'];
                    $logStokPenerimaan['id_warna']        = $dataBeamStokawal['id_warna'];
                    $logStokPenerimaan['id_motif']        = $dataBeamStokawal['id_motif'];
                    $logStokPenerimaan['id_satuan_1']     = $dataBeamStokawal['id_satuan_1'];
                    $logStokPenerimaan['id_satuan_2']     = $dataBeamStokawal['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1']  = $dataBeamStokawal['volume_masuk_1'];
                    $logStokPenerimaan['volume_masuk_2']  = $dataBeamStokawal['volume_masuk_2'];
                    $logStokPenerimaan['volume_keluar_1'] = $dataBeamStokawal['volume_masuk_1'];
                    $logStokPenerimaan['volume_keluar_2'] = $dataBeamStokawal['volume_masuk_2'];
                    $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                    $logStokPenerimaan['id_beam']         = $dataBeamStokawal['id_beam'];
                    $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                    $logStokPenerimaan['code']            = $dataBeamStokawal['code'];
                }

                if (empty($logStokPenerimaan)) throw new Exception("Beam Tidak Ditemukan", 1);

                /*if (empty($logStokPenerimaan)) {

                    PenomoranBeamRetur::where('id_beam', $input['id_beam'])->each(function ($item) use (&$logStokPenerimaan, $request) {
                        $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                        $logStokPenerimaan['id_gudang']       = $item['id_gudang'];
                        $logStokPenerimaan['id_barang']       = $item['id_barang'];
                        $logStokPenerimaan['id_warna']        = $item['id_warna'];
                        $logStokPenerimaan['id_motif']        = $item['id_motif'];
                        $logStokPenerimaan['id_satuan_1']     = $item['id_satuan_1'];
                        $logStokPenerimaan['id_satuan_2']     = $item['id_satuan_2'];
                        $logStokPenerimaan['volume_masuk_1']  = $item['volume_1'];
                        $logStokPenerimaan['volume_masuk_2']  = $item['volume_2'];
                        $logStokPenerimaan['volume_keluar_1'] = $item['volume_1'];
                        $logStokPenerimaan['volume_keluar_2'] = $item['volume_2'];
                        $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                        $logStokPenerimaan['id_beam']         = $item['id_beam'];
                        $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                        $logStokPenerimaan['code']            = $item['code'];
                    });

                    if (empty($logStokPenerimaan)) throw new Exception("Beam Tidak Ditemukan", 1);
                }*/

                $logStokPenerimaanKeluar = unsetMultiKeys(['volume_masuk_1', 'volume_masuk_2'], $logStokPenerimaan);
                $input['id_log_stok_keluar'] = LogStokPenerimaan::create($logStokPenerimaanKeluar)->id;
                $logStokPenerimaanMasuk = unsetMultiKeys(['volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaan);
                $logStokPenerimaanMasuk['tipe_pra_tenun'] = $request['tipe_pra_tenun'];
                $input['id_log_stok_masuk'] = LogStokPenerimaan::create($logStokPenerimaanMasuk)->id;
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
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        try {
            if (!$isDetail) {
                $input['id_mesin'] = $request['id_mesin'];
                if ($input['id_beam'] != $request['old_id_beam']) Beam::where('id', $request['old_id_beam'])->update(['tipe_pra_tenun' => NULL]);
                Beam::where('id', $input['id_beam'])->update(['tipe_pra_tenun' => $request['tipe_pra_tenun']]);
                $logStokPenerimaan = [];
                WarpingDetail::where('id_beam', $input['id_beam'])->each(function ($item) use (&$logStokPenerimaan, $request) {
                    $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $item['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $item['id_barang'];
                    $logStokPenerimaan['id_warna']        = $item['id_warna'];
                    $logStokPenerimaan['id_motif']        = $item['id_motif'];
                    $logStokPenerimaan['id_satuan_1']     = $item['id_satuan_1'];
                    $logStokPenerimaan['id_satuan_2']     = $item['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1']  = $item['volume_1'];
                    $logStokPenerimaan['volume_masuk_2']  = $item['volume_2'];
                    $logStokPenerimaan['volume_keluar_1'] = $item['volume_1'];
                    $logStokPenerimaan['volume_keluar_2'] = $item['volume_2'];
                    $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                    $logStokPenerimaan['id_beam']         = $item['id_beam'];
                    $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                    $logStokPenerimaan['code']            = $item['code'];
                });

                if (empty($logStokPenerimaan)) {
                    $dataBeamStokawal = (array) DB::table('log_stok_penerimaan')->where('code', 'BL')->where(['id_beam' => $input['id_beam']])->orderBy('id', 'asc')->first();
                    $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                    $logStokPenerimaan['id_gudang']       = $dataBeamStokawal['id_gudang'];
                    $logStokPenerimaan['id_barang']       = $dataBeamStokawal['id_barang'];
                    $logStokPenerimaan['id_warna']        = $dataBeamStokawal['id_warna'];
                    $logStokPenerimaan['id_motif']        = $dataBeamStokawal['id_motif'];
                    $logStokPenerimaan['id_satuan_1']     = $dataBeamStokawal['id_satuan_1'];
                    $logStokPenerimaan['id_satuan_2']     = $dataBeamStokawal['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1']  = $dataBeamStokawal['volume_masuk_1'];
                    $logStokPenerimaan['volume_masuk_2']  = $dataBeamStokawal['volume_masuk_2'];
                    $logStokPenerimaan['volume_keluar_1'] = $dataBeamStokawal['volume_masuk_1'];
                    $logStokPenerimaan['volume_keluar_2'] = $dataBeamStokawal['volume_masuk_2'];
                    $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                    $logStokPenerimaan['id_beam']         = $dataBeamStokawal['id_beam'];
                    $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                    $logStokPenerimaan['code']            = $dataBeamStokawal['code'];
                }

                if (empty($logStokPenerimaan)) throw new Exception("Beam Tidak Ditemukan", 1);

                /*if (empty($logStokPenerimaan)) {

                    PenomoranBeamRetur::where('id_beam', $input['id_beam'])->each(function ($item) use (&$logStokPenerimaan, $request) {
                        $logStokPenerimaan['tanggal']         = $request['input']['tanggal'];
                        $logStokPenerimaan['id_gudang']       = $item['id_gudang'];
                        $logStokPenerimaan['id_barang']       = $item['id_barang'];
                        $logStokPenerimaan['id_warna']        = $item['id_warna'];
                        $logStokPenerimaan['id_motif']        = $item['id_motif'];
                        $logStokPenerimaan['id_satuan_1']     = $item['id_satuan_1'];
                        $logStokPenerimaan['id_satuan_2']     = $item['id_satuan_2'];
                        $logStokPenerimaan['volume_masuk_1']  = $item['volume_1'];
                        $logStokPenerimaan['volume_masuk_2']  = $item['volume_2'];
                        $logStokPenerimaan['volume_keluar_1'] = $item['volume_1'];
                        $logStokPenerimaan['volume_keluar_2'] = $item['volume_2'];
                        $logStokPenerimaan['is_sizing']       = $request['is_sizing'] ?? null;
                        $logStokPenerimaan['id_beam']         = $item['id_beam'];
                        $logStokPenerimaan['id_mesin']        = $request['id_mesin'];
                        $logStokPenerimaan['code']            = $item['code'];
                    });

                    if (empty($logStokPenerimaan)) throw new Exception("Beam Tidak Ditemukan", 1);
                }*/

                $logStokPenerimaanKeluar = unsetMultiKeys(['volume_masuk_1', 'volume_masuk_2'], $logStokPenerimaan);
                LogStokPenerimaan::where('id', $request['id_log_stok_keluar'])->update($logStokPenerimaanKeluar);
                $logStokPenerimaanMasuk = unsetMultiKeys(['volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaan);
                $logStokPenerimaanMasuk['tipe_pra_tenun'] = $request['tipe_pra_tenun'];
                LogStokPenerimaan::where('id', $request['id_log_stok_masuk'])->update($logStokPenerimaanMasuk);
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
        if (!$isDetail) {
            $dataCucuk = Cucuk::where('id', $id)->first();
            $idLogStok[] = $dataCucuk->id_log_stok_keluar;
            $idLogStok[] = $dataCucuk->id_log_stok_masuk;
            LogStokPenerimaan::whereIn('id', $idLogStok)->delete();
            Beam::where('id', $dataCucuk->id_beam)->update(['tipe_pra_tenun' => NULL]);
        }
        return Define::delete($id, $usingModel);
    }
}
