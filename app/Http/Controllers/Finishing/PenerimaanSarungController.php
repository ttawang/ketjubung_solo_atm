<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\PenerimaanSarung;
use App\Models\PenerimaanSarungDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenerimaanSarungController extends Controller
{
    private static $model = 'PenerimaanSarung';
    private static $modelDetail = 'PenerimaanSarungDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Penerimaan Sarung Luar', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('finishing', 'penerimaan sarung', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.finishing.penerimaan_sarung.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $constructor = PenerimaanSarung::withSum('relPenerimaanSarungDetail', 'volume_1')->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(nomor) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $input['isDetail'] = 'true';
        $search = strtolower($request['search']['value']);
        $constructor = PenerimaanSarungDetail::when($search, function ($query, $param) {
            return $query->where(function ($query) use ($param) {
                $query->whereHas('relBarang', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('throughNomorKikw', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relWarna', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relMotif', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(alias) LIKE '%$param%'");
                })->orwhereHas('relMesin', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(name) LIKE '%$param%'");
                })->orwhereHas('relGrade', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(grade) LIKE '%$param%'");
                })->orwhereHas('relKualitas', function ($query) use ($param) {
                    return $query->whereRaw("LOWER(kode) LIKE '%$param%'");
                });
            });
        })
            ->where('id_penerimaan_sarung', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['relSatuan', 'relWarna', 'relMotif', 'relMesin', 'relGudang', 'relBarang'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = PenerimaanSarung::where('id', $idParent)->first();
            $attr['idLogStok'] = '';
            $attr['from'] = $data->from;
            $attr['code'] = ($data->from == 'GRESIK') ? 'BGLG' : 'BGLP';
            $response['render'] = view('contents.production.finishing.penerimaan_sarung.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.production.finishing.penerimaan_sarung.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = PenerimaanSarungDetail::where('id', $id)->first();
            $data['tanggal'] = $data->relPenerimaanSarung()->value('tanggal');
            $response['selected'] = [
                'select_barang' => [
                    'id'             => $data->id,
                    'text'           => $data->nama_barang,
                    'id_barang'      => $data->id_barang,
                    'volume_1'       => $data->volume_1,
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

            if ($data->id_motif != null) {
                $arrayMotif = [
                    'select_motif' => [
                        'id'   => $data->id_motif,
                        'text' => $data->relMotif()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayMotif);
            }

            if ($data->id_motif != null) {
                $arrayMotif = [
                    'select_motif' => [
                        'id'   => $data->id_motif,
                        'text' => $data->relMotif()->value('name')
                    ]
                ];
                $response['selected'] = array_merge($response['selected'], $arrayMotif);
            }

            $idParent = $data->id_penerimaan_sarung;
            $attr['idLogStok'] = $data->id_log_stok;
            $attr['code'] = $data->code;
            $attr['from'] = $data->relPenerimaanSarung()->value('from');
            $response['render'] = view('contents.production.finishing.penerimaan_sarung.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = PenerimaanSarung::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $response['render'] = view('contents.production.finishing.penerimaan_sarung.form', compact('data', 'id'))->render();
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
                if (!is_numeric($input['id_barang'])) {
                    $inputBarang['name']       = $input['id_barang'];
                    $inputBarang['alias']      = $input['id_barang'];
                    $inputBarang['id_tipe']    = 7;
                    $inputBarang['created_by'] = Auth::id();
                    $inputBarang['owner']      = $request['owner'];
                    $input['id_barang']        = DB::table('tbl_barang')->insertGetId($inputBarang);
                }

                if (!is_numeric($input['id_motif'])) {
                    $inputMotif['name']       = $input['id_motif'];
                    $inputMotif['alias']      = $input['id_motif'];
                    $inputMotif['created_by'] = Auth::id();
                    $inputMotif['owner']      = $request['owner'];
                    $input['id_motif']        = DB::table('tbl_motif')->insertGetId($inputMotif);
                }

                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['code']            = $input['code'];
                $logStokPenerimaan['tanggal']         = $request['tanggal'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_masuk_1'] = floatValue($input['volume_1']);

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];

                if (isset($input['id_beam'])) $logStokPenerimaan['id_beam'] = $input['id_beam'];
                if (isset($input['id_mesin'])) $logStokPenerimaan['id_mesin']  = $input['id_mesin'];

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
            try {

                if (!is_numeric($input['id_barang'])) {
                    $inputBarang['name']       = $input['id_barang'];
                    $inputBarang['alias']      = $input['id_barang'];
                    $inputBarang['id_tipe']    = 7;
                    $inputBarang['created_by'] = Auth::id();
                    $inputBarang['owner']      = $request['owner'];
                    $input['id_barang']       = DB::table('tbl_motif')->insertGetId($inputBarang);
                }

                if (!is_numeric($input['id_motif'])) {
                    $inputMotif['name']       = $input['id_motif'];
                    $inputMotif['alias']      = $input['id_motif'];
                    $inputMotif['created_by'] = Auth::id();
                    $inputMotif['owner']      = $request['owner'];
                    $input['id_motif']        = DB::table('tbl_motif')->insertGetId($inputMotif);
                }

                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['code']            = $input['code'];
                $logStokPenerimaan['tanggal']         = $request['tanggal'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_masuk_1'] = floatValue($input['volume_1']);

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];

                if (isset($input['id_beam'])) {
                    $dataBeam = Beam::where('id', $input['id_beam'])->first();
                    $logStokPenerimaan['id_beam']   = $input['id_beam'];
                    $logStokPenerimaan['id_mesin']  = $dataBeam->relMesinHistoryLatest()->value('id_mesin');
                } else {
                    if (isset($input['id_mesin'])) $logStokPenerimaan['id_mesin']  = $input['id_mesin'];
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
            $detailData = PenerimaanSarungDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
