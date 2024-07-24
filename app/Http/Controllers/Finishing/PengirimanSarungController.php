<?php

namespace App\Http\Controllers\Finishing;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\PengirimanSarung;
use App\Models\PengirimanSarungDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanSarungController extends Controller
{
    private static $model = 'PengirimanSarung';
    private static $modelDetail = 'PengirimanSarungDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Finishing', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pengiriman Sarung', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('finishing', 'pengiriman sarung', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.finishing.pengiriman_sarung.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $search = $request['search']['value'];
        $tipe = $request['tipe'];
        $constructor = PengirimanSarung::withSum('relPengirimanSarungDetail', 'volume_1')
            ->where('tipe', $tipe)
            ->when($search, function ($query, $value) {
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
        $constructor = PengirimanSarungDetail::when($search, function ($query, $param) {
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
            ->where('id_pengiriman_sarung', $id)
            ->orderBy('created_at', 'DESC');
        $attributes = ['relSatuan', 'relWarna', 'relMotif', 'relMesin', 'relGudang', 'relBarang', 'noKikw', 'noKiks', 'tanggal_potong','relGrade'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = PengirimanSarung::where('id', $idParent)->first();
            $attr['idLogStok'] = '';
            $attr['codeSelect'] = 'IP2,JP2';
            if ($data->tipe_selected == 'GRESIK') {
                $attr['codeSelect'] = 'DR';
            } else if ($data->tipe_selected == 'FINISHEDGOODS') {
                $attr['codeSelect'] = 'JCS,DR';
            }

            $response['render'] = view('contents.production.finishing.pengiriman_sarung.form-detail', compact('data', 'attr', 'idParent'))->render();
        } else {
            $tipe = $request['tipe'];
            $response['render'] = view('contents.production.finishing.pengiriman_sarung.form', compact('tipe'))->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = PengirimanSarungDetail::where('id', $id)->first();
            $data['tanggal'] = $data->relPengirimanSarung()->value('tanggal');
            $data['tipe'] = $data->relPengirimanSarung()->value('tipe');
            $response['selected'] = [
                'select_gudang_2' => [
                    'id' => $data->id_gudang,
                    'text' => $data->relGudang()->value('name')
                ],
                'select_barang' => [
                    'id'             => $data->id,
                    'text'           => $data->nama_barang,
                    'id_barang'      => $data->id_barang,
                    'id_warna'       => $data->id_warna,
                    'nama_warna'     => $data->relWarna()->value('name'),
                    'id_motif'       => $data->id_motif,
                    'nama_motif'     => $data->relMotif()->value('name'),
                    'volume_1'       => $data->volume_1,
                    'volume_2'       => $data->volume_2,
                    'id_satuan_1'    => $data->id_satuan_1,
                    'nama_satuan_1'  => $data->relSatuan1()->value('name'),
                    'id_satuan_2'    => $data->id_satuan_2 ?? '',
                    'nama_satuan_2'  => $data->relSatuan2()->value('name'),
                    'id_gudang'      => $data->id_gudang,
                    'id_beam'        => $data->id_beam,
                    'id_songket'        => $data->id_songket,
                    'tanggal_potong'        => $data->tanggal_potong,
                    'no_beam'        => $data->throughNomorBeam()->value('name'),
                    'id_mesin'       => $data->id_mesin,
                    'nama_mesin'     => $data->relMesin()->value('name'),
                    'no_kikw'        => $data->throughNomorKikw()->value('name'),
                    'no_kiks'        => $data->throughNomorKiks()->value('name'),
                    'id_grade'       => $data->id_grade,
                    'nama_grade'     => $data->relGrade()->value('grade') . ' - ' . $data->relGrade()->value('alias'),
                    'id_kualitas'    => $data->id_kualitas,
                    'nama_kualitas'  => $data->relKualitas()->value('kode') . ' - ' . $data->relKualitas()->value('name'),
                    'code'           => $data->code
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

            $idParent = $data->id_pengiriman_sarung;
            $attr['idLogStok'] = $data->id_log_stok;

            $tipeSelected = $data->relPengirimanSarung()->value('tipe_selected');
            $attr['codeSelect'] = 'IP2,JP2';
            if ($tipeSelected == 'GRESIK') {
                $attr['codeSelect'] = 'DR';
            } else if ($tipeSelected == 'FINISHEDGOODS') {
                $attr['codeSelect'] = 'JCS,DR';
            }


            $response['render'] = view('contents.production.finishing.pengiriman_sarung.form-detail', compact('id', 'data', 'attr', 'idParent'))->render();
        } else {
            $data = PengirimanSarung::where('id', $id)->first();
            $response['selected'] = [
                'select_supplier' => [
                    'id'   => $data->id_supplier,
                    'text' => $data->relSupplier()->value('name')
                ]
            ];
            $tipe = $data->tipe;
            $response['render'] = view('contents.production.finishing.pengiriman_sarung.form', compact('data', 'id', 'tipe'))->render();
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
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['code']            = $input['code'];
                $logStokPenerimaan['tanggal']         = $request['tanggal'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_keluar_1'] = floatValue($input['volume_1']);

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_keluar_2'] = floatValue($input['volume_2']);
                }

                if (isset($input['id_warna'])) $logStokPenerimaan['id_warna'] = $input['id_warna'];
                if (isset($input['id_motif'])) $logStokPenerimaan['id_motif'] = $input['id_motif'];
                if (isset($input['id_kualitas'])) $logStokPenerimaan['id_kualitas'] = $input['id_kualitas'];
                if (isset($input['id_grade'])) $logStokPenerimaan['id_grade'] = $input['id_grade'];

                if (isset($input['id_beam'])) $logStokPenerimaan['id_beam'] = $input['id_beam'];
                if (isset($input['id_songket'])) $logStokPenerimaan['id_songket'] = $input['id_songket'];
                if (isset($input['tanggal_potong'])) $logStokPenerimaan['tanggal_potong'] = $input['tanggal_potong'];
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
                $logStokPenerimaan['id_gudang']       = $input['id_gudang'];
                $logStokPenerimaan['code']            = $input['code'];
                $logStokPenerimaan['tanggal']         = $request['tanggal'];
                $logStokPenerimaan['id_barang']       = $input['id_barang'];
                $logStokPenerimaan['id_satuan_1']     = $input['id_satuan_1'];
                $logStokPenerimaan['volume_keluar_1'] = floatValue($input['volume_1']);

                if (isset($input['volume_2'])) {
                    $logStokPenerimaan['id_satuan_2'] = $input['id_satuan_2'];
                    $logStokPenerimaan['volume_keluar_2'] = floatValue($input['volume_2']);
                }

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
                if (isset($input['id_songket'])) {
                    $logStokPenerimaan['id_songket']  = $input['id_songket'];
                }
                if (isset($input['tanggal_potong'])) {
                    $logStokPenerimaan['tanggal_potong']  = $input['tanggal_potong'];
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
            $detailData = PengirimanSarungDetail::where('id', $id)->first();
            LogStokPenerimaan::where('id', $detailData->id_log_stok)->delete();
        }
        return Define::delete($id, $usingModel);
    }
}
