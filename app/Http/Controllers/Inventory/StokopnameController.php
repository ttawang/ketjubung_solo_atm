<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\Stokopname;
use App\Models\StokopnameDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokopnameController extends Controller
{
    private static $model = 'Stokopname';
    private static $modelDetail = 'StokopnameDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Inventory', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Stokopname', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('', 'stokopname', $breadcumbs, true, false, false, true);
        if (!$request->ajax()) return view('contents.inventory.stokopname.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $input['usedAction'] = ['detail', 'delete'];
        $search = strtolower($request['search']['value']) ?? '';
        $userId = Auth::id();
        $constructor = Stokopname::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(proses) LIKE '%" . $value . "%'");
        })->when($userId != 1 && $userId != 8, function ($query) use ($userId) {
            return $query->where('created_by', $userId);
        })->orderBy('id', 'DESC');
        return Define::fetch($input, $constructor, ['customTanggal']);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        // $input['usedAction'] = ['delete'];
        $search = strtolower($request['search']['value']);
        $constructor = StokopnameDetail::when($search, function ($query, $value) {
            return $query->where(function($query) use($value){
                return $query->whereHas('relBarang', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('relWarna', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
            });
        })
            ->where('id_stokopname', $id)
            ->orderBy('id');

        $code = $request->code;
        $attributes = [];
        if (checkCodeStokopname($code, 'class1')) {
            $attributes = ['relGudang', 'relBarang', 'relSatuan'];
        } else if (checkCodeStokopname($code, 'class2')) {
            $attributes = ['relGudang', 'relBarang', 'relSatuan', 'relWarna'];
        } else if (checkCodeStokopname($code, 'class3')) {
            $attributes = ['relGudang', 'relBarang', 'relSatuan', 'relWarna', 'relMotif', 'noBeam', 'noKikw', 'relMesin'];
        } else if (checkCodeStokopname($code, 'class4')) {
            $attributes = ['relGudang', 'relBarang', 'relSatuan', 'relWarna', 'relMotif', 'noBeam', 'noKikw', 'relMesin', 'relGrade'];
        }
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Stokopname::where('id', $idParent)->first();
            $attr['tanggal'] = $data->tanggal;
            $attr['code'] = $data->code;
            $attr['idLogStok'] = '';
            $response['render'] = view('contents.inventory.stokopname.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.inventory.stokopname.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = StokopnameDetail::where('id', $id)->first();
            $attr['code'] = $data->relStokopname()->value('code');
            $attr['tanggal'] = $data->relStokopname()->value('tanggal');
            $response['selected'] = [
                'select_gudang_2' => [
                    'id' => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'             => $data->id,
                    'text'           => $data->relBarang()->value('name'),
                    'id_barang'      => $data->id_barang,
                    'id_warna'       => $data->id_warna,
                    'nama_warna'     => $data->relWarna()->value('name'),
                    'id_motif'       => $data->id_motif,
                    'nama_motif'     => $data->relMotif()->value('name'),
                    'volume_1'       => $data->stokopname_1,
                    'volume_2'       => $data->stokopname_2,
                    'id_satuan_1'    => $data->id_satuan_1,
                    'nama_satuan_1'  => $data->relSatuan1()->value('name'),
                    'id_satuan_2'    => $data->id_satuan_2 ?? '',
                    'nama_satuan_2'  => $data->relSatuan2()->value('name'),
                    'id_gudang'      => $data->id_gudang,
                    'is_sizing'      => $data->is_sizing == 'null' ? NULL : $data->is_sizing,
                    'id_beam'        => $data->id_beam,
                    'no_beam'        => $data->throughNomorBeam()->value('name'),
                    'id_mesin'       => $data->id_mesin,
                    'nama_mesin'     => $data->relMesin()->value('name'),
                    'no_kikw'        => $data->throughNomorKikw()->value('name'),
                    'tipe_pra_tenun' => $data->tipe_pra_tenun,
                    'id_grade'       => $data->id_grade,
                    'nama_grade'     => $data->relGrade()->value('grade') . ' - ' . $data->relGrade()->value('alias'),
                    'id_kualitas'    => $data->id_kualitas,
                    'nama_kualitas'  => $data->relKualitas()->value('kode') . ' - ' . $data->relKualitas()->value('name'),
                ]
            ];

            $idParent = $data->id_stokopname;
            $attr['idLogStok']  = $data->id_log_stok;
            $response['render'] = view('contents.inventory.stokopname.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = Stokopname::where('id', $id)->first();
            $response['selected'] = [];
            $response['render'] = view('contents.inventory.stokopname.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function update($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = unsetMultiKeys(['tipe_pra_tenun'], $request->all()['input']);
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $logStokPenerimaan['tanggal']        = $request['tanggal'];
                $logStokPenerimaan['id_gudang']      = $input['id_gudang'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['code']           = $request['code'];

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_beam'])) {
                    $dataBeam = Beam::where('id', $input['id_beam'])->first();
                    $logStokPenerimaan['id_beam']        = $input['id_beam'];
                    $logStokPenerimaan['tipe_pra_tenun'] = $request['input']['tipe_pra_tenun'];
                    $logStokPenerimaan['is_sizing']      = $dataBeam->is_sizing;
                }
                if (isset($input['id_mesin'])) $logStokPenerimaan['id_mesin'] = $input['id_mesin'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];

                $input['stokopname_1']         = floatValue($input['stokopname_1']);

                $checkStokBarangFilter         = unsetMultiKeys(['stokopname_1', 'stokopname_2'], $input);
                $checkStokBarangFilter['code'] = $request['code'];
                // $checkStokTanggal              = date("Y-m-d", strtotime("-1 day", strtotime($request['tanggal'])));
                $input['stok_1']               = checkStokBarang($checkStokBarangFilter, false, $request['tanggal'])->stok_utama ?? 0;
                $selisih_1                     = normalizeDecimal($input['stokopname_1'], $input['stok_1'], $input['stok_1'] - $input['stokopname_1']);

                if ($input['stok_1'] > $input['stokopname_1']) {
                    $logStokPenerimaan['volume_keluar_1'] = $selisih_1;
                    $input['selisih_1'] = $selisih_1;
                } else {
                    $logStokPenerimaan['volume_masuk_1'] = abs($selisih_1 * -1);
                    $input['selisih_1'] = abs($selisih_1 * -1);
                }

                if (isset($input['stokopname_2'])) {
                    $input['stokopname_2'] = floatValue($input['stokopname_2']);
                    $input['stok_2']       = checkStokBarang($checkStokBarangFilter, false, $request['tanggal'])->stok_pilihan;
                    $selisih_1             = normalizeDecimal($input['stokopname_2'], $input['stok_2'], $input['stok_2'] - $input['stokopname_2']);

                    if ($input['stok_2'] > $input['stokopname_2']) {
                        $logStokPenerimaan['volume_keluar_2'] = $selisih_1;
                        $input['selisih_2'] = $selisih_1;
                    } else {
                        $logStokPenerimaan['volume_masuk_2'] = abs($selisih_1 * -1);
                        $input['selisih_2'] = abs($selisih_1 * -1);
                    }
                }

                LogStokPenerimaan::where('id', $request['id_log_stok'])->update($logStokPenerimaan);
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
            $detailData = StokopnameDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->forceDelete();
        } else {
            $detailData = StokopnameDetail::where('id_stokopname', $id);
            LogStokPenerimaan::whereIn('id', $detailData->pluck('id_log_stok'))->forceDelete();
            $detailData->forceDelete();
        }
        return Define::delete($id, $usingModel, true);
    }
}
