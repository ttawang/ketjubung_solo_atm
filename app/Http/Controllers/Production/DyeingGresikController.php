<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\DyeingGresik;
use App\Models\DyeingGresikDetail;
use App\Models\LogStokPenerimaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DyeingGresikController extends Controller
{
    private static $model = 'DyeingGresik';
    private static $modelDetail = 'DyeingGresikDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Dyeing - Gresik', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Dyeing', 'link' => route('production.dyeing_gresik.index'), 'active' => 'active']];
        $menuAssets = menuAssets('dyeing', 'dyeing gresik', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.dyeing_gresik.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $sub = DB::table('tbl_dyeing_gresik_detail')->selectRaw("id_dyeing_gresik, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_dyeing_gresik');
        $constructor = DyeingGresik::leftJoinSub($sub, 'sub', function ($query) {
            return $query->on('tbl_dyeing_gresik.id', 'sub.id_dyeing_gresik');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("nomor LIKE '%$value%'");
        })->selectRaw('tbl_dyeing_gresik.*, sub.count_detail')->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $code = $request['code'];
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $input['extraData'] = ['code' => $code];

        /*if ($request['table'] == 'tableOutput') {
            $input['btnExtras'] = ['<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Detail" onclick="showFormWarna(%id);">
                <i class="icon md-palette" aria-hidden="true"></i>
            </a>'];
        }*/

        $search = $request['search']['value'];
        $constructor = DyeingGresikDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('relWarna', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('code', $code)
            ->where(['id_dyeing_gresik' => $id])
            ->orderBy('id', 'DESC');
        $attributes = ($request['table'] == 'tableInput') ? ['relGudang', 'relBarang', 'relSatuan', 'customTanggal'] : ['relGudang', 'relBarang', 'relWarna', 'relSatuan', 'customTanggal'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $code = $request['code'];
            $data = DyeingGresik::where('id', $idParent)->first();
            $currVolume1 = 0;
            $response['render'] = view('contents.production.dyeing_gresik.form-detail', compact('data', 'idParent', 'code', 'currVolume1'))->render();
        } else {
            $response['render'] = view('contents.production.dyeing_gresik.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = DyeingGresikDetail::where('id', $id)->first();
            $code = $request['code'];

            $filter['id_gudang']       = $data->id_gudang;
            $filter['id_barang']       = $data->id_barang;
            $filter['id_satuan_1']     = $data->id_satuan_1;
            $filter['code']            = 'BBDG';
            $stokUtama = checkStokBarang($filter) + $data->volume_1;

            $response['selected'] = [
                'select_gudang_2' => [
                    'id'   => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'         => ($code == 'BBDG') ? $data->id : $data->id_parent_detail,
                    'text'       => $data->relBarang()->value('name'),
                    'id_barang'  => $data->id_barang,
                    'stok_utama' => $stokUtama,
                    'volume_1'   => $data->volume_1,
                    'volume_2'   => $data->volume_2
                ],
                'select_satuan_1' => [
                    'id'   => $data->id_satuan_1,
                    'text' => $data->relSatuan1()->value('name')
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

            $idParent = $data->id_dyeing_gresik;
            $currVolume1 = $data->volume_1;
            $response['render'] = view('contents.production.dyeing_gresik.form-detail', compact('id', 'data', 'idParent', 'code', 'currVolume1'))->render();
        } else {
            $data = DyeingGresik::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.production.dyeing_gresik.form', compact('data', 'id'))->render();
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
                $input['volume_1']                    = floatValue($input['volume_1']);
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['code']            = $input['code'];
                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];

                if ($input['code'] == 'BBDG') {
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $checkStokBarangBBD = checkStokBarang($filter);
                    if ($input['volume_1'] > $checkStokBarangBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                } else {
                    $input['id_mesin'] = 118;
                    $input['volume_2']                   = floatValue($input['volume_2']);
                    $logStokPenerimaan['id_mesin']       = $input['id_mesin'];
                    $logStokPenerimaan['id_satuan_2']    = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
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
                $input['volume_1']                    = floatValue($input['volume_1']);
                $logStokPenerimaan['tanggal']         = $input['tanggal'];
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['code']            = $input['code'];
                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];

                if ($input['code'] == 'BBDG') {
                    $filter = unsetMultiKeys(['tanggal', 'volume_keluar_1'], $logStokPenerimaan);
                    $checkStokBarangBBD = checkStokBarang($filter) + $request['curr_volume_1'];
                    if ($input['volume_1'] > $checkStokBarangBBD) throw new Exception("Stok Benang Grey Tidak Cukup", 1);
                    $logStokPenerimaan['volume_keluar_1'] = $input['volume_1'];
                } else {
                    $input['id_mesin'] = 118;
                    $input['volume_2']                   = floatValue($input['volume_2']);
                    $logStokPenerimaan['id_mesin']       = $input['id_mesin'];
                    $logStokPenerimaan['id_satuan_2']    = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
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
            $detailData = DyeingGresikDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
