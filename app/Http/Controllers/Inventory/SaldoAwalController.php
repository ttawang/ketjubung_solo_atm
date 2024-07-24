<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\MesinHistory;
use App\Models\NomorKikw;
use App\Models\SaldoAwal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaldoAwalController extends Controller
{
    private static $model = 'SaldoAwal';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Inventory', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Saldo Awal', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('', 'saldoawal', $breadcumbs, true, false, false, true);
        if (!$request->ajax()) return view('contents.inventory.saldoawal.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $input['usedAction'] = ['delete'];
        $input['btnExtras'] = ['<button type="button" onclick="showFormView(%id, true);"  class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-eye mr-2"></i>
            </button><button type="button" onclick="showFormView(%id);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
            <i class="icon md-edit mr-2"></i>
        </button>'];
        $search = strtolower($request['search']['value']) ?? '';
        $code = $request['code'] ?? '';
        $constructor = SaldoAwal::when($search, function ($query, $value) {
            return $query->where(function ($query) use ($value) {
                return $query->whereHas('relBarang', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%" . $value . "%'");
                })->orwhereHas('relWarna', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('relMotif', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('throughNomorBeam', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('throughNomorKikw', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
            });
        })->when($code, function ($query, $value) {
            return $query->where('code', $value);
        })->orderBy('id', 'DESC');
        $attributes = ['customTanggal', 'relGudang', 'relBarang', 'noBeam', 'noKikw', 'relMesin', 'relWarna', 'relMotif', 'relSatuan', 'relProductionCode'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        $data = collect([]);
        $response['render'] = view('contents.inventory.saldoawal.form', compact('data'))->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = SaldoAwal::where('id', $id)->first();
        $response['selected'] = [
            'select_proses' => [
                'id' => $data->code,
                'text' => SaldoAwalCodeText($data->code)
            ],
            'select_gudang' => [
                'id' => $data->id_gudang,
                'text' => $data->relGudang()->value('name')
            ],
            'select_barang' => [
                'id' => $data->id_barang,
                'text' => $data->relBarang()->value('name')
            ]
        ];
        $response['render'] = view('contents.inventory.saldoawal.form', compact('data'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $inputBeam = $request->all()['beam'] ?? [];
        DB::beginTransaction();
        try {
            // $input['tanggal'] = '2023-12-31';
            $input['volume_1'] = floatValue($input['volume_1']);

            $logStok = unsetMultiKeys(['volume_1', 'id_satuan_2', 'volume_2'], $input);

            if (!empty($inputBeam)) {
                $logStok['tipe_pra_tenun'] = $inputBeam['tipe_pra_tenun'];
                if ($request['txt_nomor_kikw'] != '') $inputBeam['id_nomor_kikw'] = NomorKikw::create(['name' => $request['txt_nomor_kikw']])->id;
                $input['id_beam'] = $logStok['id_beam'] = Beam::create($inputBeam)->id;
                if (isset($input['id_mesin'])) MesinHistory::create(['id_mesin' => $input['id_mesin'], 'id_beam' => $input['id_beam']]);
            }

            $logStok['is_saldoawal']   = 'YA';
            $logStok['volume_masuk_1'] = $input['volume_1'];

            if (isset($input['volume_2'])) {
                $input['volume_2'] = floatValue($input['volume_2']);
                if ($input['volume_2'] != null && $input['id_satuan_2'] != null) {
                    $logStok['id_satuan_2']    = $input['id_satuan_2'];
                    $logStok['volume_masuk_2'] = $input['volume_2'];
                } else {
                    $input = unsetMultiKeys(['id_satuan_2', 'volume_2'], $input);
                }
            }

            $input['id_log_stok'] = LogStokPenerimaan::create($logStok)->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
        return Define::store($input, self::$model);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        $inputBeam = $request->all()['beam'] ?? [];
        DB::beginTransaction();
        try {
            // $input['tanggal'] = '2023-12-31';
            $input['volume_1'] = floatValue($input['volume_1']);

            $logStok = unsetMultiKeys(['volume_1', 'id_satuan_2', 'volume_2'], $input);

            if (isset($inputBeam['tipe_pra_tenun'])) $logStok['tipe_pra_tenun'] = $inputBeam['tipe_pra_tenun'];
            if ($request['id_nomor_kikw'] != null) {
                if (($request['txt_nomor_kikw'] != '')) NomorKikw::where('id', $request['id_nomor_kikw'])->update(['name' => $request['txt_nomor_kikw']]);
            } else {
                $inputBeam['id_nomor_kikw'] = ($request['txt_nomor_kikw'] != '') ? NomorKikw::create(['name' => $request['txt_nomor_kikw']])->id : null;
            }
            Beam::where('id', $request['id_beam'])->update($inputBeam);
            if (isset($input['id_mesin'])) {
                if ($request['id_history_mesin'] != null) {
                    MesinHistory::where('id', $request['id_history_mesin'])->update(['id_mesin' => $input['id_mesin']]);
                } else {
                    MesinHistory::create(['id_beam' => $request['id_beam'], 'id_mesin' => $input['id_mesin']]);
                }
            } else {
                $input['id_mesin'] = null;
                $logStok['id_mesin'] = null;
                MesinHistory::where('id', $request['id_history_mesin'])->delete();
            }

            $logStok['is_saldoawal']   = 'YA';
            $logStok['volume_masuk_1'] = $input['volume_1'];

            if (isset($input['volume_2'])) {
                $input['volume_2'] = floatValue($input['volume_2']);
                if ($input['volume_2'] != null && $input['id_satuan_2'] != null) {
                    $logStok['id_satuan_2']    = $input['id_satuan_2'];
                    $logStok['volume_masuk_2'] = $input['volume_2'];
                } else {
                    $input = unsetMultiKeys(['id_satuan_2', 'volume_2'], $input);
                }
            }

            LogStokPenerimaan::where('id', $request['id_log_stok'])->update($logStok);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage() . ' ' . $th->getLine(), 401);
        }
        return Define::update($input, self::$model, $id);
    }

    public function destroy($id)
    {
        $dataSaldoawal = SaldoAwal::where('id', $id)->first();
        LogStokPenerimaan::where('id', $dataSaldoawal->id_log_stok)->delete();
        Beam::where('id', $dataSaldoawal->id_beam)->delete();
        return Define::delete($id, self::$model);
    }
}
