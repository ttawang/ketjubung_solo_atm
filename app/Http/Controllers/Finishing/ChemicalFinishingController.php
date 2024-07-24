<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChemicalFinishingDetail;
use App\Models\ChemicalFinishingSarung;
use App\Models\DryingDetail;
use App\Models\LogStokPenerimaan;

class ChemicalFinishingController extends Controller
{
    private static $model = 'ChemicalFinishingSarung';
    private static $modelDetail = 'ChemicalFinishingDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Chemical Finishing', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('finishing', 'chemical finishing', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.finishing.chemical_finishing.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $input['extraData']['row'] = ['id_barang', 'id_motif'];
        $search = $request['search']['value'];
        $constructor = DryingDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('relMotif', function ($query) use ($value) {
                return $query->whereRaw("LOWER(alias) LIKE '%$value%'");
            });
        })
            ->selectRaw('CONCAT(id_barang, id_motif) as id, id_barang, id_motif, SUM(volume_1) as volume')
            ->groupBy('id_barang', 'id_motif');
        $attributes = ['relBarang', 'relMotif'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $search = strtolower($request['search']['value']);
        $constructor = ChemicalFinishingDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where('id_chemical_finishing_sarung', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['relSatuan', 'relGudang', 'relBarang'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = ChemicalFinishingSarung::where('id', $idParent)->first();
            $attr['idLogStok'] = '';
            $response['selected'] = [
                'select_gudang' => [
                    'id' => 6,
                    'text' => 'Gudang Finishing'
                ],
                'select_satuan_1' => [
                    'id' => 2,
                    'text' => 'kg'
                ]
            ];
            $response['render'] = view('contents.production.finishing.chemical_finishing.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.finishing.chemical_finishing.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = ChemicalFinishingDetail::where('id', $id)->first();
            $data['tanggal'] = $data->relChemicalFinishingSarung()->value('tanggal');
            $response['selected'] = [
                'select_proses' => [
                    'id'   => $data->code,
                    'text' => ($data->code == 'CJ') ? 'Jigger & Cuci Sarung' : 'Drying (Pengeringan)'
                ],
                'select_gudang' => [
                    'id' => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
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

            $idParent = $data->id_penerimaan_chemical;
            $attr['idLogStok'] = $data->id_log_stok;
            $response['render'] = view('contents.production.finishing.chemical_finishing.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = ChemicalFinishingSarung::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $response['render'] = view('contents.production.finishing.chemical_finishing.form', compact('data', 'id'))->render();
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
            if (isset($input['volume_2'])) $input['volume_2'] = floatValue($input['volume_2']);
            try {
                $logStokPenerimaan['code']           = $input['code'];
                $logStokPenerimaan['id_gudang']      = $input['id_gudang'];
                $logStokPenerimaan['tanggal']        = $request['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                $logStokPenerimaan['id_satuan_1']    = $input['id_satuan_1'];

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $input['volume_2'] = $input['volume_2'];
                    $input['id_satuan_2'] = $input['id_satuan_2'];
                } else {
                    $input['volume_2'] = null;
                    $input['id_satuan_2'] = null;
                }

                $input['id_log_stok'] = LogStokPenerimaan::create($logStokPenerimaan)->id;
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
                $logStokPenerimaan['code']           = $input['code'];
                $logStokPenerimaan['id_gudang']      = $input['id_gudang'];
                $logStokPenerimaan['tanggal']        = $request['tanggal'];
                $logStokPenerimaan['id_barang']      = $input['id_barang'];
                $logStokPenerimaan['volume_masuk_1'] = $input['volume_1'];
                $logStokPenerimaan['id_satuan_1']    = $input['id_satuan_1'];

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['volume_masuk_2'] = $input['volume_2'];
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $input['volume_2'] = $input['volume_2'];
                    $input['id_satuan_2'] = $input['id_satuan_2'];
                } else {
                    $input['volume_2'] = null;
                    $input['id_satuan_2'] = null;
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
            $detailData = ChemicalFinishingDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
