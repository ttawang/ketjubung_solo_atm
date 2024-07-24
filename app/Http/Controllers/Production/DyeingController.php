<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Dyeing;
use App\Models\DyeingDetail;
use App\Models\DyeingWarna;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DyeingController extends Controller
{
    private static $model = 'Dyeing';
    private static $modelDetail = 'DyeingDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Dyeing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dyeing', 'link' => route('production.dyeing.index'), 'active' => 'active']];
        $menuAssets = menuAssets('dyeing', 'dyeing', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.dyeing.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_dyeing_detail')->selectRaw("id_dyeing, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_dyeing');
        $constructor = Dyeing::leftJoinSub($sub, 'sub', function($query){
            return $query->on('tbl_dyeing.id', 'sub.id_dyeing');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(no_kikd) LIKE '%$value%'");
        })->selectRaw('tbl_dyeing.*, sub.count_detail')->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $input['extraData'] = ['status' => $request['status']];

        /*if ($request['status'] == 'DYEOVEN' || $request['status'] == 'RETURN') {
            $input['btnExtras'] = ['<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Detail" onclick="showFormWarna(%id, \'' . $request['status'] . '\');">
                <i class="icon md-palette" aria-hidden="true"></i>
            </a>'];
        }*/

        $search = $request['search']['value'];
        $constructor = DyeingDetail::with(['relDyeingWarna', 'relDyeingWarna.relWarna'])
            ->when($search, function ($query, $value) {
                return $query->whereHas('relBarang', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
            })
            ->where(['id_dyeing' => $id, 'status' => $input['status']])
            ->orderBy('id', 'DESC');
        $attributes = ['relSatuan', 'customTanggal', 'relMesin'];
        return Define::fetch($input, $constructor, $attributes, []);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Dyeing::where('id', $idParent)->first();
            $attr['idLogStokMasuk'] = '';
            $attr['idLogStokKeluar'] = '';
            $attr['status'] = $request['status'];
            $attr['code'] = ($request['retur'] == 'YA') ? 'BBDR' : 'BBD';
            if ($attr['status'] == 'DYEOVEN') {
                $attr['code'] = 'DS';
            } else if ($attr['status'] == 'OVERCONE') {
                $attr['code'] = 'DD';
            } else if ($attr['status'] == 'RETURN') {
                $attr['code'] = 'DO';
            }

            $attr['nama_satuan_1'] = '';
            $attr['nama_satuan_2'] = '';
            $attr['curr_volume_2'] = '';
            $attr['key'] = DyeingDetail::where(['id_dyeing' => $idParent, 'status' => $attr['status']])->count() + 1;
            $response['render'] = view('contents.production.dyeing.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.dyeing.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DyeingDetail::where('id', $id)->first();
            $dataParent = DyeingDetail::where('id', $data->id_parent)->first();
            $attr['key'] = $data->key;
            $stokUtama = 0;
            $stokPilihan = 0;
            $code = $data->relLogStokKeluar()->value('code');

            if ($request['status'] == 'SOFTCONE') {
                $filter['id_gudang']   = $data->id_gudang;
                $filter['id_barang']   = $data->id_barang;
                $filter['id_satuan_1'] = $data->id_satuan_2;
                $filter['code']        = $code;
                $stokUtama = checkStokBarang($filter) + $data->volume_2;
                $stokPilihan = 0;
            } else {
                $stokUtama = $dataParent->volume_1;
                $stokPilihan = $dataParent->volume_2;
            }

            $response['selected'] = [
                'select_gudang_2' => [
                    'id' => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_mesin' => [
                    'id' => $data->id_mesin,
                    'text' => $data->relMesin()->value('name')
                ],
                'select_barang' => [
                    'id'               => $data->id,
                    'text'             => ($request['status'] == 'SOFTCONE') ? $data->nama_barang : $dataParent->jenis_benang,
                    'id_barang'        => $data->id_barang,
                    'id_warna'         => $data->id_warna,
                    'id_mesin'         => ($request['status'] == 'SOFTCONE') ? $data->id_mesin : $dataParent->id_mesin,
                    'nama_warna'       => $data->relWarna()->value('name'),
                    'id_satuan_1'      => $data->id_satuan_1,
                    'id_satuan_2'      => $data->id_satuan_2,
                    'nama_satuan_1'    => $data->relSatuan1()->value('name'),
                    'nama_satuan_2'    => $data->relSatuan2()->value('name'),
                    'volume_1'         => $data->volume_1,
                    'volume_2'         => $data->volume_2,
                    'stok_utama'       => $stokUtama,
                    'stok_pilihan'     => $stokPilihan,
                    'id_gudang'        => $data->id_gudang,
                    'id_dyeing_detail' => $data->id_parent,
                    'code'             => $code
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

            $idParent = $data->id_dyeing;
            $attr['idLogStokMasuk'] = $data->id_log_stok_masuk;
            $attr['idLogStokKeluar'] = $data->id_log_stok_keluar;
            $attr['status'] = $request['status'];
            $attr['code'] = $code;
            $attr['nama_satuan_1'] = $data->relSatuan1()->value('name');
            $attr['nama_satuan_2'] = $data->relSatuan2()->value('name');
            if ($attr['status'] == 'DYEOVEN') {
                $attr['code'] = 'DS';
            } else if ($attr['status'] == 'OVERCONE') {
                $attr['code'] = 'DD';
            } else if ($attr['status'] == 'RETURN') {
                $attr['code'] = 'DO';
            }
            $attr['curr_volume_2'] = $data->volume_2;
            $response['render'] = view('contents.production.dyeing.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = Dyeing::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.dyeing.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $input['volume_1'] = floatValue($input['volume_1']);
                $input['volume_2'] = floatValue($input['volume_2']);
                $logStokPenerimaanDyeingKeluar['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaanDyeingKeluar['code']            = ($request['code_retur'] == 'BBDR') ? 'BBDR' : $this->generateCodeDyeing($input['status'], true);
                $logStokPenerimaanDyeingKeluar['tanggal']         = $input['tanggal'];
                $logStokPenerimaanDyeingKeluar['id_barang']       = $input['id_barang'];

                if ($input['status'] == 'SOFTCONE') {
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $input['id_satuan_2'];
                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaanDyeingKeluar);
                    $checkStokBarangBBD = checkStokBarang($filter);
                    if ($input['volume_2'] > $checkStokBarangBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $input['volume_2'];
                } else if ($input['status'] == 'DYEOVEN') {
                    $logStokPenerimaanDyeingKeluar['id_mesin']        = $request['current_id_mesin'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $input['volume_1'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_2']     = $input['id_satuan_2'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_2'] = $input['volume_2'];
                } else if ($input['status'] == 'OVERCONE' || $input['status'] == 'RETURN') {
                    $logStokPenerimaanDyeingKeluar['id_mesin']        = $request['current_id_mesin'];
                    $logStokPenerimaanDyeingKeluar['id_warna']        = $input['id_warna'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $request['satuan_kirim'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $request['volume_kirim'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_2']     = $request['satuan_2_kirim'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_2'] = $request['volume_2_kirim'];
                }

                $input['id_log_stok_keluar'] = LogStokPenerimaan::create($logStokPenerimaanDyeingKeluar)->id;

                $logStokPenerimaanDyeingMasuk['id_gudang']      = $input['id_gudang'];
                $logStokPenerimaanDyeingMasuk['code']           = $this->generateCodeDyeing($input['status']);
                $logStokPenerimaanDyeingMasuk['tanggal']        = $input['tanggal'];
                $logStokPenerimaanDyeingMasuk['id_barang']      = $input['id_barang'];
                $logStokPenerimaanDyeingMasuk['id_mesin']       = $input['id_mesin'];
                if (isset($input['id_warna'])) $logStokPenerimaanDyeingMasuk['id_warna'] = $input['id_warna'];
                $logStokPenerimaanDyeingMasuk['id_satuan_1']    = $input['id_satuan_1'];
                $logStokPenerimaanDyeingMasuk['volume_masuk_1'] = $input['volume_1'];
                if (isset($input['id_satuan_2'])) $logStokPenerimaanDyeingMasuk['id_satuan_2'] = $input['id_satuan_2'];
                $logStokPenerimaanDyeingMasuk['volume_masuk_2'] = $input['volume_2'];

                $input['id_log_stok_masuk']  = LogStokPenerimaan::create($logStokPenerimaanDyeingMasuk)->id;
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
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;

        DB::beginTransaction();
        if ($isDetail) {
            try {

                $input['volume_1'] = floatValue($input['volume_1']);
                $input['volume_2'] = floatValue($input['volume_2']);
                $logStokPenerimaanDyeingKeluar['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaanDyeingKeluar['code']            = ($request['code_retur'] == 'BBDR') ? 'BBDR' : $this->generateCodeDyeing($input['status'], true);
                $logStokPenerimaanDyeingKeluar['tanggal']         = $input['tanggal'];
                $logStokPenerimaanDyeingKeluar['id_barang']       = $input['id_barang'];

                if ($input['status'] == 'SOFTCONE') {
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $input['id_satuan_2'];
                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1', 'volume_keluar_2'], $logStokPenerimaanDyeingKeluar);
                    $checkStokBarangBBD = checkStokBarang($filter);
                    $currentStokBBD = $checkStokBarangBBD + $request['curr_volume_2'];
                    if ($input['volume_2'] > $currentStokBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $input['volume_2'];
                } else if ($input['status'] == 'DYEOVEN') {
                    $logStokPenerimaanDyeingKeluar['id_mesin']        = $request['current_id_mesin'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $input['id_satuan_1'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $input['volume_1'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_2']     = $input['id_satuan_2'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_2'] = $input['volume_2'];
                } else if ($input['status'] == 'OVERCONE' || $input['status'] == 'RETURN') {
                    $logStokPenerimaanDyeingKeluar['id_mesin']        = $request['current_id_mesin'];
                    $logStokPenerimaanDyeingKeluar['id_warna']        = $input['id_warna'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_1']     = $request['satuan_kirim'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_1'] = $request['volume_kirim'];
                    $logStokPenerimaanDyeingKeluar['id_satuan_2']     = $request['satuan_2_kirim'];
                    $logStokPenerimaanDyeingKeluar['volume_keluar_2'] = $request['volume_2_kirim'];
                }

                LogStokPenerimaan::where('id', $request['id_log_stok_keluar'])->update($logStokPenerimaanDyeingKeluar);

                $logStokPenerimaanDyeingMasuk['id_mesin']       = $input['id_mesin'];
                $logStokPenerimaanDyeingMasuk['id_gudang']      = $input['id_gudang'];
                $logStokPenerimaanDyeingMasuk['code']           = $this->generateCodeDyeing($input['status']);
                $logStokPenerimaanDyeingMasuk['tanggal']        = $input['tanggal'];
                $logStokPenerimaanDyeingMasuk['id_barang']      = $input['id_barang'];
                if (isset($input['id_warna'])) $logStokPenerimaanDyeingMasuk['id_warna'] = $input['id_warna'];
                $logStokPenerimaanDyeingMasuk['id_satuan_1']    = $input['id_satuan_1'];
                $logStokPenerimaanDyeingMasuk['volume_masuk_1'] = $input['volume_1'];
                if (isset($input['id_satuan_2'])) $logStokPenerimaanDyeingMasuk['id_satuan_2'] = $input['id_satuan_2'];
                $logStokPenerimaanDyeingMasuk['volume_masuk_2'] = $input['volume_2'];

                LogStokPenerimaan::where('id', $request['id_log_stok_masuk'])->update($logStokPenerimaanDyeingMasuk);
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
            $dyeingDetail = DyeingDetail::where('id', $id)->first();
            $idLogStok[] = $dyeingDetail->id_log_stok_keluar;
            $idLogStok[] = $dyeingDetail->id_log_stok_masuk;
            LogStokPenerimaan::whereIn('id', $idLogStok)->delete();

            if ($dyeingDetail->status == 'DYEOVEN' || $dyeingDetail->status == 'RETURN') {
                $dyeingWarna = DyeingWarna::where('id_dyeing_detail', $id);
                LogStokPenerimaan::whereIn('id', $dyeingWarna->pluck('id_log_stok'))->delete();
                $dyeingWarna->delete();
            }
        }
        return Define::delete($id, $usingModel);
    }

    function generateCodeDyeing($input, $isInput = false)
    {
        if ($isInput) {
            $arrayCode = [
                'SOFTCONE' => 'BBD',
                'DYEOVEN'  => 'DS',
                'OVERCONE' => 'DD',
                'RETURN'   => 'DO',
            ];
        } else {
            $arrayCode = [
                'SOFTCONE' => 'DS',
                'DYEOVEN'  => 'DD',
                'OVERCONE' => 'DO',
                'RETURN'   => 'DO',
            ];
        }
        return $arrayCode[$input];
    }
}
