<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\PengirimanBarangDetail;
use App\Models\Tenun;
use App\Models\TenunDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenunController extends Controller
{
    private static $model = 'Tenun';
    private static $modelDetail = 'TenunDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $input['usedAction'] = ['detail', 'delete'];
        $breadcumbs = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Tenun', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('weaving', 'tenun', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.weaving.tenun.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = strtolower($request['search']['value']);
        $subJoin = DB::table('tbl_tenun_detail')
        ->join('tbl_warna', 'tbl_tenun_detail.id_warna', 'tbl_warna.id')
        ->select('id_tenun', 'tbl_warna.name as warna')
        ->where('code', 'BBTL');
        $constructor = Tenun::withSum(['relTenunDetail' => fn ($query) => $query->where('code', 'BG')], 'volume_1')
            ->joinSub($subJoin, 'sj', function($join){
                return $join->on('tbl_tenun.id', 'sj.id_tenun');
            })
            ->when($search, function ($query, $value) {
                return $query->whereHas('throughNomorBeam', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('throughNomorKikw', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                })->orwhereHas('relMesinHistoryLatest.relMesin', function ($query) use ($value) {
                    return $query->whereRaw("LOWER(name) LIKE '%$value%'");
                });
            })
            ->selectRaw('tbl_tenun.*, sj.warna')
            ->orderBy('created_at', 'DESC');
        $attributes = ['customTanggal', 'noBeam', 'noKikw', 'relMesinTenun'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['name'] = self::$modelDetail;
        $tab = $request['tab'] ?? 'TenunDetail';
        $form = $request['form'] ?? '';
        $input['isDetail'] = 'true';
        $input['usedAction'] = ($request['isFinish'] == 'true' || $form == 'output') ? 'NOUSED' : ['edit', 'delete'];
        $input['extraData'] = ['form' => $form, 'tab' => $tab];
        $search = $request['search']['value'];
        $permissionTenun = permissionCodeTenun(false);
        $permissionTenun[] = 'BBTL';
        $constructor = TenunDetail::with(['relTenun', 'relBeam', 'relSongketPotong'])->when($search, function ($query, $value) use ($form) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->where('name', 'LIKE', "%$value%");
            })->when($form == 'output', function ($query) use ($value) {
                return $query->orwhereHas('relPekerja', function ($query) use ($value) {
                    return $query->where('name', 'LIKE', "%$value%");
                });
            });
        })->when($form, function ($query, $form) use ($permissionTenun, $id) {
            return $query->when($form == 'input', function ($query) use ($permissionTenun) {
                return $query->whereIn('code', $permissionTenun);
            })->when($form == 'output', function ($query) use($id) {
                return $query->where('code', '=', 'BG')
                ->addSelect([
                    'sisa' => DB::table('tbl_tenun_detail as data')
                    ->selectRaw("beam.volume_2 - (SELECT SUM(volume_1) FROM tbl_tenun_detail AS sub WHERE sub.id <= data.id AND sub.code = 'BG' AND sub.id_tenun = tbl_tenun_detail.id_tenun AND sub.deleted_at IS NULL) as sisa")
                    ->leftJoin('tbl_tenun_detail as beam', function($join){
                        $join->on('beam.id_beam', 'data.id_beam')->on('beam.code', DB::raw("'BBTL'"));
                    })
                    ->whereRaw('data.id = tbl_tenun_detail.id')
                    ->whereRaw('data.id_tenun = tbl_tenun_detail.id_tenun')
                    ->where('data.code', 'BG')
                    ->whereNull('data.deleted_at')
                    ->orderBy('data.id')
                ]);
            })->when($form == 'diturunkan', function ($query) use ($permissionTenun) {
                return $query->whereNotIn('code', $permissionTenun)->where('code', '!=', 'BG');
            });
        })
            ->where(['id_tenun' => $id])
            ->orderBy('id', 'ASC');

        $attributes = ($form == 'input' || $form == 'diturunkan') ? ['relSatuan', 'relMesin'] : ['relMesin', 'relPekerja'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $attr = $request->all();
            $idParent = $request['id'];
            $data = Tenun::where('id', $idParent)->first();
            $detailBeam = TenunDetail::where('id_tenun', $idParent)->where('code', 'BBTL')->first();
            $attr['tanggal'] = date('Y-m-d');
            $response['render'] = view('contents.production.weaving.tenun.form-detail', compact('data', 'idParent', 'attr', 'detailBeam'))->render();
        } else {
            $response['render'] = view('contents.production.weaving.tenun.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $attr = $request->all();
            $data = TenunDetail::where('id', $id)->first();
            $detailBeam = collect([]);
            $idParent = $data->id_tenun;
            if ($attr['form'] == 'input') {
                $response['selected'] = [
                    'select_gudang_2' => [
                        'id'        => $data->id_gudang,
                        'text'      => $data->relGudang()->value('name'),
                    ],

                    'select_barang' => [
                        'id'            => $data->id,
                        'text'          => $data->nama_barang,
                        'id_barang'     => $data->id_barang,
                        'id_warna'      => $data->id_warna,
                        'nama_warna'    => $data->relWarna()->value('name'),
                        'id_motif'      => $data->id_motif,
                        'nama_motif'    => $data->relMotif()->value('name'),
                        'volume_1'      => $data->volume_1,
                        'volume_2'      => $data->volume_2,
                        'id_satuan_1'   => $data->id_satuan_1,
                        'nama_satuan_1' => $data->relSatuan1()->value('name'),
                        'id_satuan_2'   => $data->id_satuan_2 ?? '',
                        'nama_satuan_2' => $data->relSatuan2()->value('name'),
                        'id_gudang'     => $data->id_gudang,
                        'code'          => $data->code,
                        'id_beam'       => $data->id_beam
                    ],
                ];
            } else if ($attr['form'] == 'output') {
                $detailBeam = TenunDetail::where('id_tenun', $idParent)->where('code', 'BBTL')->first();
                $response['selected'] = [
                    'select_group' => [
                        'id'   => $data->id_group,
                        'text' => $data->relGroup()->value('name'),
                    ],
                    'select_pekerja' => [
                        'id'   => $data->id_pekerja,
                        'text' => $data->relPekerja()->value('name'),
                    ],
                    // 'select_gudang_2' => [
                    //     'id'   => $data->id_gudang,
                    //     'text' => $data->relGudang()->value('name'),
                    // ],
                    'select_sarung' => [
                        'id'   => $data->id_barang,
                        'text' => $data->nama_barang
                    ],
                    'select_songket' => [
                        'id'   => $data->id_songket_detail,
                        'text' => $data->songket
                    ],
                ];
            } else if ($attr['form'] == 'diturunkan') {
                $response['selected'] = [
                    'select_barang' => [
                        'id'            => $data->id,
                        'text'          => $data->nama_barang,
                        'id_barang'     => $data->id_barang,
                        'id_warna'      => $data->id_warna,
                        'id_motif'      => $data->id_motif,
                        'volume_1'      => $data->volume_1,
                        'volume_2'      => $data->volume_2,
                        'id_satuan_1'   => $data->id_satuan_1,
                        'nama_satuan_1' => $data->relSatuan1()->value('name'),
                        'id_satuan_2'   => $data->id_satuan_2 ?? '',
                        'nama_satuan_2' => $data->relSatuan2()->value('name'),
                        'code'          => $data->code,
                        'id_beam'       => $data->id_beam
                    ]
                ];
            }
            $attr['tanggal'] = $data->tanggal;
            $response['data'] = $data;
            $response['render'] = view('contents.production.weaving.tenun.form-detail', compact('id', 'data', 'idParent', 'attr', 'detailBeam'))->render();
        } else {
            $data = Tenun::where('id', $id)->first();
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
            $response['render'] = view('contents.production.weaving.tenun.form', compact('id', 'data'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $tab = $request['tab'];
        $form = $request['form'];
        $model = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        try {
            if ($isDetail) {
                $stringModel = strtolower($tab);
                $logstok = unsetMultiKeys(['id_tenun', 'id_group', 'id_pekerja', 'volume_1', 'volume_2', 'id_songket_detail', 'id_lusi_detail'], $input);

                if ($input['id_beam'] != '') {
                    $dataBeam = Beam::where('id', $input['id_beam'])->first();
                    $logstok['id_beam']        = $input['id_beam'];
                    $logstok['id_mesin']       = $input['id_mesin'];
                    if ($form == 'input' || $form == 'diturunkan') {
                        $logstok['tipe_pra_tenun'] = $dataBeam->tipe_pra_tenun;
                        $logstok['is_sizing']      = $dataBeam->is_sizing;
                    }
                }

                if ($form == 'input') {
                    if ($logstok['code'] == 'BO') $logstok['id_mesin'] = null;
                    $logstok['volume_keluar_1'] = floatValue($input['volume_1']);
                    if (isset($input['volume_2'])) $logstok['volume_keluar_2'] = floatValue($input['volume_2']);
                } else if ($form == 'output') {
                    $logstok = unsetMultiKeys(['id_satuan_2'], $logstok);
                    $logstok['id_satuan_1']    = $input['id_satuan_1'];
                    $logstok['volume_masuk_1'] = floatValue($input['volume_1']);
                } else if ($form == 'diturunkan') {

                    if ($input['code'] == 'DPRT') {
                        $checkIdBarang = fixBenangPakan($input['id_barang']);
                        if ($checkIdBarang['id'] == 0) throw new Exception("Error {$checkIdBarang['name']}", 1);
                        $input['id_barang'] = $checkIdBarang['id'];
                    }

                    $logstok['id_barang'] = $input['id_barang'];
                    $logstok['volume_masuk_1'] = floatValue($input['volume_1']);
                    if (isset($input['volume_2'])) $logstok['volume_masuk_2'] = floatValue($input['volume_2']);
                }

                $input['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logstok)->id;
                TenunDetail::create($input);
            } else {
                $stringModel = strtolower($model);
                $dataBeamPengiriman = PengirimanBarangDetail::where('id_beam', $input['id_beam'])->where('status', 'TUJUAN')->first();

                if ($dataBeamPengiriman == null) {
                    $dataBeam = LogStokPenerimaan::where('id_beam', $input['id_beam'])->where('code', 'BBTL')->first();

                    $input['jumlah_beam'] = $dataBeam->volume_masuk_2;
                    $modelId = Tenun::create($input)->id;

                    $detail['tanggal']     = $input['tanggal'];
                    $detail['id_gudang']   = $dataBeam->id_gudang;
                    $detail['id_barang']   = $dataBeam->id_barang;
                    $detail['id_warna']    = $dataBeam->id_warna;
                    $detail['id_motif']    = $dataBeam->id_motif;
                    $detail['id_satuan_1'] = $dataBeam->id_satuan_1;
                    $detail['id_satuan_2'] = $dataBeam->id_satuan_2;
                    $detail['code']        = 'BBTL';
                    $detail['id_beam']     = $input['id_beam'];
                    $detail['id_mesin']    = $dataBeam->id_mesin;

                    $logstok = $detail;
                    $logstok['is_sizing']       = $dataBeam->is_sizing;
                    $logstok['tipe_pra_tenun']  = $dataBeam->tipe_pra_tenun;
                    $logstok['volume_keluar_1'] = $dataBeam->volume_masuk_1;
                    $logstok['volume_keluar_2'] = $dataBeam->volume_masuk_2;
                    $detail['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logstok)->id;

                    $detail['id_tenun']    = $modelId;
                    $detail['volume_1']    = $dataBeam->volume_masuk_1;
                    $detail['volume_2']    = $dataBeam->volume_masuk_2;
                    TenunDetail::create($detail);
                } else {
                    $input['jumlah_beam'] = $dataBeamPengiriman->volume_2;
                    $modelId = Tenun::create($input)->id;

                    $detail['tanggal']     = $input['tanggal'];
                    $detail['id_gudang']   = $dataBeamPengiriman->id_gudang;
                    $detail['id_barang']   = $dataBeamPengiriman->id_barang;
                    $detail['id_warna']    = $dataBeamPengiriman->id_warna;
                    $detail['id_motif']    = $dataBeamPengiriman->id_motif;
                    $detail['id_satuan_1'] = $dataBeamPengiriman->id_satuan_1;
                    $detail['id_satuan_2'] = $dataBeamPengiriman->id_satuan_2;
                    $detail['code']        = 'BBTL';
                    $detail['id_beam']     = $input['id_beam'];
                    $detail['id_mesin']    = $dataBeamPengiriman->id_mesin;

                    $logstok = $detail;
                    $logstok['is_sizing']       = $dataBeamPengiriman->is_sizing;
                    $logstok['tipe_pra_tenun']  = $dataBeamPengiriman->tipe_pra_tenun;
                    $logstok['volume_keluar_1'] = $dataBeamPengiriman->volume_1;
                    $logstok['volume_keluar_2'] = $dataBeamPengiriman->volume_2;
                    $detail['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logstok)->id;

                    $detail['id_tenun']    = $modelId;
                    $detail['volume_1']    = $dataBeamPengiriman->volume_1;
                    $detail['volume_2']    = $dataBeamPengiriman->volume_2;
                    TenunDetail::create($detail);
                }
            }
            activity()->log("Menambah data {$stringModel} {$model}");
            DB::commit();
            return response('Data Successfully Saved!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function update($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $input = $request->all()['input'];
        $tab = $request['tab'];
        $form = $request['form'];
        $model = $isDetail ? self::$modelDetail : self::$model;
        DB::beginTransaction();
        try {
            if ($isDetail) {
                $stringModel = strtolower($tab);
                $logstok = unsetMultiKeys(['id_tenun', 'id_group', 'id_pekerja', 'volume_1', 'volume_2', 'id_songket_detail'], $input);

                if ($input['id_beam'] != '') {
                    $dataBeam = Beam::where('id', $input['id_beam'])->first();
                    $logstok['id_beam']        = $input['id_beam'];
                    $logstok['id_mesin']       = $input['id_mesin'];
                    if ($form == 'input' || $form == 'diturunkan') {
                        $logstok['tipe_pra_tenun'] = $dataBeam->tipe_pra_tenun;
                        $logstok['is_sizing']      = $dataBeam->is_sizing;
                    }
                }

                if ($form == 'input') {
                    if ($logstok['code'] == 'BO') $logstok['id_mesin'] = null;
                    $logstok['volume_keluar_1'] = floatValue($input['volume_1']);
                    if (isset($input['volume_2'])) $logstok['volume_keluar_2'] = floatValue($input['volume_2']);
                } else if ($form == 'output') {
                    $logstok = unsetMultiKeys(['id_satuan_2'], $logstok);
                    $logstok['id_satuan_1']    = $input['id_satuan_1'];
                    $logstok['volume_masuk_1'] = floatValue($input['volume_1']);
                } else if ($form == 'diturunkan') {
                    $input['code'] = substr($input['code'], 0, -1);

                    if ($input['code'] == 'DPRT') {
                        $checkIdBarang = fixBenangPakan($input['id_barang']);
                        if ($checkIdBarang['id'] == 0) throw new Exception("Error {$checkIdBarang['name']}", 1);
                        $input['id_barang'] = $checkIdBarang['id'];
                    }

                    $logstok['code'] = $input['code'];
                    $logstok['volume_masuk_1'] = floatValue($input['volume_1']);
                    if (isset($input['volume_2'])) $logstok['volume_masuk_2'] = floatValue($input['volume_2']);
                }

                LogStokPenerimaan::where('id', $request['id_log_stok_penerimaan'])->update($logstok);
                TenunDetail::where('id', $id)->update($input);
            }
            activity()->log("Merubah data {$stringModel} {$model}");
            DB::commit();
            return response('Data Successfully Saved!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function destroy($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ? self::$modelDetail : self::$model;
        if ($isDetail) {
            $detailData = TenunDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok_penerimaan)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
