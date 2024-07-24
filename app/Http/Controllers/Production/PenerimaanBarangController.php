<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Define;
use App\Models\Barang;
use App\Models\LogStokPenerimaan;
use App\Models\PenerimaanBarang;
use App\Models\PenerimaanBarangDetail;
use Illuminate\Support\Facades\DB;

class PenerimaanBarangController extends Controller
{
    private static $model = 'PenerimaanBarang';
    private static $modelDetail = 'PenerimaanBarangDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pernerimaan Barang', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('penerimaan', 'penerimaan barang', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.penerimaan_barang.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = strtolower($request['search']['value']);
        $sub = DB::table('tbl_penerimaan_barang_detail')->selectRaw("id_penerimaan_barang, COUNT(*) as count_detail")->whereNull('deleted_at')->groupBy('id_penerimaan_barang');
        $constructor = PenerimaanBarang::leftJoinSub($sub, 'sub', function($query){
            return $query->on('tbl_penerimaan_barang.id', 'sub.id_penerimaan_barang');
        })->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(no_po) LIKE '%$value%'")
                ->orwhereRaw("LOWER(no_kendaraan) LIKE '%$value%'")
                ->orwhereRaw("LOWER(supir) LIKE '%$value%'")
                ->orwhereRaw("LOWER(no_ttbm) LIKE '%$value%'")
                ->orwhereHas('relSupplier', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
        })->selectRaw('tbl_penerimaan_barang.*, sub.count_detail')->orderBy('created_at', 'DESC');
        $attributes = ['relSupplier'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $search = strtolower($request['search']['value']);
        $constructor = PenerimaanBarangDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('id_penerimaan_barang', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['relSatuan', 'relGudang'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = PenerimaanBarang::where('id', $idParent)->first();
            $attr['idLogStokPenerimaan'] = '';
            $response['render'] = view('contents.production.penerimaan_barang.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.penerimaan_barang.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = PenerimaanBarangDetail::where('id', $id)->first();
            $data['tanggal_terima'] = $data->relPenerimaanBarang()->value('tanggal_terima');
            $response['selected'] = [
                'select_barang' => [
                    'id'   => $data->id_barang,
                    'text' => $data->relBarang()->value('name')
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

            $idParent = $data->id_penerimaan_barang;
            $attr['idLogStokPenerimaan'] = $data->id_log_stok_penerimaan;
            $response['render'] = view('contents.production.penerimaan_barang.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = PenerimaanBarang::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $response['render'] = view('contents.production.penerimaan_barang.form', compact('data', 'id'))->render();
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
            $input['volume_1'] = floatValue($input['volume_1']);
            $isPewarna = Barang::where('id', $input['id_barang'])->first()->id_tipe == 2;
            if (isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
            try {
                $logStokPenerimaan['code']           = ($isPewarna) ? 'DW' : 'PB';
                $logStokPenerimaan['id_gudang']      = ($isPewarna) ? 2 : 1;
                $logStokPenerimaan['tanggal']        = $request['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                $logStokPenerimaan['id_satuan_1']    = $input['id_satuan_1'];
                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $input['volume_2'] = $input['volume_2'];
                    $input['id_satuan_2'] = $input['id_satuan_2'];
                } else {
                    $input['volume_2'] = null;
                    $input['id_satuan_2'] = null;
                }

                $input['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logStokPenerimaan)->id;

                (isset($input['id_warna'])) ? $input['id_warna'] = $input['id_warna'] : $input['id_warna'] = null;
                (isset($input['id_motif'])) ? $input['id_motif'] = $input['id_motif'] : $input['id_motif'] = null;
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
            $input['volume_1'] = floatValue($input['volume_1']);
            if (isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
            try {
                $logStokPenerimaan['tanggal']        = $request['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                $logStokPenerimaan['id_satuan_1']    = $input['id_satuan_1'];
                (isset($input['id_warna'])) ? $logStokPenerimaan['id_warna'] = $input['id_warna'] : $logStokPenerimaan['id_warna'] = null;
                (isset($input['id_motif'])) ? $logStokPenerimaan['id_motif'] = $input['id_motif'] : $logStokPenerimaan['id_motif'] = null;

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $input['volume_2'] = $input['volume_2'];
                    $input['id_satuan_2'] = $input['id_satuan_2'];
                } else {
                    $logStokPenerimaan['volume_masuk_2'] = null;
                    $logStokPenerimaan['id_satuan_2'] = null;
                    $input['volume_2'] = null;
                    $input['id_satuan_2'] = null;
                }

                LogStokPenerimaan::where('id', $request['id_log_stok_penerimaan'])->update($logStokPenerimaan);

                (isset($input['id_warna'])) ? $input['id_warna'] = $input['id_warna'] : $input['id_warna'] = null;
                (isset($input['id_motif'])) ? $input['id_motif'] = $input['id_motif'] : $input['id_motif'] = null;
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
            $detailData = PenerimaanBarangDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok_penerimaan)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
