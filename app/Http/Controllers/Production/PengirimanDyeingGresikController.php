<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\PengirimanDyeingGresik;
use App\Models\PengirimanDyeingGresikDetail;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanDyeingGresikController extends Controller
{
    private static $model = 'PengirimanDyeingGresik';
    private static $modelDetail = 'PengirimanDyeingGresikDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Pengiriman Benang Warna ke Gresik', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dyeing', 'link' => route('production.pengiriman_dyeing_gresik.index'), 'active' => 'active']];
        $menuAssets = menuAssets('dyeing', 'pengiriman dyeing gresik', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.pengiriman_dyeing_gresik.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_pengiriman_dyeing_gresik_detail')->selectRaw("id_pengiriman_dyeing_gresik, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_pengiriman_dyeing_gresik');
        $constructor = PengirimanDyeingGresik::leftJoinSub($sub, 'sub', function ($query) {
            return $query->on('tbl_pengiriman_dyeing_gresik.id', 'sub.id_pengiriman_dyeing_gresik');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("nomor LIKE '%$value%'");
        })->selectRaw('tbl_pengiriman_dyeing_gresik.*, sub.count_detail')->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor, [], [], [], ['aksi', 'tipe_custom']);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $search = $request['search']['value'];
        $constructor = PengirimanDyeingGresikDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('relWarna', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where(['id_pengiriman_dyeing_gresik' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ['relBarang', 'relWarna', 'relSatuan', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = PengirimanDyeingGresik::where('id', $idParent)->first();
            $currVolume1 = 0;
            $currVolume2 = 0;
            $response['render'] = view('contents.production.pengiriman_dyeing_gresik.form-detail', compact('data', 'idParent', 'currVolume1', 'currVolume2'))->render();
        } else {
            $response['render'] = view('contents.production.pengiriman_dyeing_gresik.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = PengirimanDyeingGresikDetail::where('id', $id)->first();
            $data->tipe = $data->relPengirimanDyeingGresik()->value('tipe');

            $filter['id_gudang']   = $data->id_gudang;
            $filter['id_barang']   = $data->id_barang;
            if($data->tipe == 'BDG'){
                $filter['id_warna']    = $data->id_warna;
                $filter['id_mesin']    = $data->id_mesin;
                $filter['id_satuan_2'] = $data->id_satuan_2;
            }
            $filter['id_satuan_1'] = $data->id_satuan_1;
            $filter['code']        = $data->tipe;
            $checkStok = checkStokBarang($filter, false);
            $stokUtama = $checkStok->stok_utama + $data->volume_1;
            $stokPilihan = $checkStok->stok_pilihan + $data->volume_2;

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
                    'nama_warna' => $data->relWarna()->value('name'),
                    'stok_utama' => $stokUtama,
                    'stok_pilihan' => $stokPilihan,
                    'volume_1'   => $data->volume_1,
                    'volume_2'   => $data->volume_2
                ]
            ];

            if ($data->id_warna != null) {
                $arrayWarna = [
                    'select_warna' => [
                        'id'   => $data->id_warna,
                        'text' => $data->relWarna()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayWarna);
            }

            $idParent = $data->id_pengiriman_dyeing_gresik;
            $currVolume1 = $data->volume_1;
            $currVolume2 = $data->volume_2;
            $response['render'] = view('contents.production.pengiriman_dyeing_gresik.form-detail', compact('id', 'data', 'idParent', 'currVolume1', 'currVolume2'))->render();
        } else {
            $data = PengirimanDyeingGresik::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.pengiriman_dyeing_gresik.form', compact('data', 'id'))->render();
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
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                $logStokPenerimaan['code']            = $request['code'];

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_satuan_2'])) $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];

                if (isset($input['volume_2'])) {
                    $input['id_mesin'] = 118;
                    $logStokPenerimaan['id_mesin'] = $input['id_mesin'];
                    
                    $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                    $input['volume_2'] = floatValue($input['volume_2']);
                }else{
                    $input['volume_2'] = 0;
                }

                $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaan);
                $checkStok = checkStokBarang($filter, false);
                $checkStok1 = $checkStok->stok_utama;
                $checkStok2 = $checkStok->stok_pilihan;

                if ($input['volume_1'] > $checkStok1 || $input['volume_2'] > $checkStok2) throw new Exception("Stok Benang Warna Tidak Cukup", 1);

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
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                $logStokPenerimaan['code']            = $request['code'];

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_satuan_2'])) $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];

                if (isset($input['volume_2'])) {
                    $input['id_mesin'] = 118;
                    $logStokPenerimaan['id_mesin'] = $input['id_mesin'];
                    
                    $logStokPenerimaan['volume_keluar_2'] = $input['volume_2'];
                    $input['volume_2'] = floatValue($input['volume_2']);
                }else{
                    $input['volume_2'] = 0;
                }

                $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaan);
                $checkStok = checkStokBarang($filter, false);
                $checkStok1 = $checkStok->stok_utama + $request['curr_volume_1'];
                $checkStok2 = $checkStok->stok_pilihan + $request['curr_volume_2'];
                if ($input['volume_1'] > $checkStok1 || $input['volume_2'] > $checkStok2) throw new Exception("Stok Benang Warna Tidak Cukup", 1);

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
            $detailData = PengirimanDyeingGresikDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
