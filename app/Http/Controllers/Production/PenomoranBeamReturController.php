<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\MesinHistory;
use App\Models\NomorKikw;
use App\Models\PenomoranBeamRetur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenomoranBeamReturController extends Controller
{
    private static $model = 'PenomoranBeamRetur';

    public function index(Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Production', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Penomoran Beam Retur', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('weaving', 'penomoran beam retur', $breadcumbs, true, true, false, true);
        if (!$request->ajax()) return view('contents.production.weaving.penomoran_beam_retur.index', compact('menuAssets'));
        $input['name'] = self::$model;
        $input['usedAction'] = ['delete'];
        $input['btnExtras'] = ['<button type="button" onclick="showFormView(%id);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-edit mr-2"></i>
            </button></button>'];
        $search = strtolower($request['search']['value']) ?? '';
        $sub = DB::table('tbl_pengiriman_barang_detail')->selectRaw("id_beam as sub_id_beam, COUNT(*) as count_beam")->whereNotNull('id_beam')->whereNull('deleted_at')->groupBy('id_beam');
        $constructor = PenomoranBeamRetur::leftJoinSub($sub, 'sub', function($query){
            return $query->on('tbl_beam_retur.id_beam', 'sub.sub_id_beam');
        })->when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('throughNomorBeam', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            })->orwhereHas('throughNomorKikw', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })->whereNotNull('id_beam_retur')->orderBy('created_at', 'DESC');
        $attributes = ['customTanggal', 'relPenomoranBeamReturDetail', 'tipe_beam'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $inputBaru = $request->all()['inputBaru'];
        DB::beginTransaction();
        try {
            $input['tanggal']         = $request['tanggal'];
            $input['is_sizing']       = $input['is_sizing'] == 'TIDAK' ? null : 'YA';

            $logStokKeluar = unsetMultiKeys(['volume_1', 'volume_2'], $input);
            $logStokKeluar['volume_keluar_1'] = $input['volume_1'];
            $logStokKeluar['volume_keluar_2'] = $input['volume_2'];
            $input['id_log_stok_penerimaan']  = LogStokPenerimaan::create($logStokKeluar)->id;

            $dataBeam = Beam::where('id', $input['id_beam'])->first();
            $beam['tipe_beam']     = $dataBeam->tipe_beam;
            $beam['id_nomor_beam'] = $dataBeam->id_nomor_beam;
            $beam['id_nomor_kikw'] = NomorKikw::create(['name' => $request['nomor_kikw']])->id;

            $inputBaru['id_beam']     = Beam::create($beam)->id;
            $inputBaru['id_barang']   = $input['id_barang'];
            $inputBaru['id_warna']    = $input['id_warna'];
            $inputBaru['tanggal']     = $request['tanggal'];
            $inputBaru['code']        = ($beam['tipe_beam'] == 'LUSI') ? 'BLN' : 'BSN';
            $inputBaru['id_gudang']   = $input['id_gudang'];
            $inputBaru['id_satuan_1'] = $input['id_satuan_1'];
            $inputBaru['id_satuan_2'] = $input['id_satuan_2'];

            MesinHistory::create(['id_beam' => $inputBaru['id_beam'], 'id_mesin' => $inputBaru['id_mesin']]);

            $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2', 'id_nomor_beam'], $inputBaru);
            $logStokMasuk['volume_masuk_1'] = $input['volume_1'];
            $logStokMasuk['volume_masuk_2'] = $input['volume_2'];
            $inputBaru['id_log_stok_penerimaan'] = LogStokPenerimaan::create($logStokMasuk)->id;

            $inputDetail = unsetMultiKeys(['is_sizing'], $input);
            $inputBaru['id_beam_retur'] = PenomoranBeamRetur::create($inputDetail)->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }

        return Define::store($inputBaru, self::$model);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        $inputBaru = $request->all()['inputBaru'];
        DB::beginTransaction();
        try {
            $input['tanggal']   = $request['tanggal'];
            $input['is_sizing'] = $input['is_sizing'] == 'TIDAK' ? null : 'YA';
            $input['catatan']   = $request['catatan'];

            $logStokKeluar = unsetMultiKeys(['volume_1', 'volume_2', 'catatan'], $input);
            $logStokKeluar['volume_keluar_1'] = $input['volume_1'];
            $logStokKeluar['volume_keluar_2'] = $input['volume_2'];
            LogStokPenerimaan::where('id', $request['id_log_stok_keluar'])->update($logStokKeluar);

            $dataBeam = Beam::where('id', $input['id_beam'])->first();
            $beam['tipe_beam']     = $dataBeam->tipe_beam;
            $beam['id_nomor_beam'] = $dataBeam->id_nomor_beam;

            $checkNoKikw = NomorKikw::whereRaw("LOWER(name) = '" . strtolower($request['nomor_kikw']) . "'");
            if ($checkNoKikw->count() == 0) {
                NomorKikw::where('id', $request['id_nomor_kikw'])->delete();
                $beam['id_nomor_kikw'] = NomorKikw::create(['name' => $request['nomor_kikw']])->id;
            }

            Beam::where('id', $request['id_beam_baru'])->update($beam);

            $inputBaru['id_barang']   = $input['id_barang'];
            $inputBaru['id_warna']    = $input['id_warna'];
            $inputBaru['tanggal']     = $request['tanggal'];
            $inputBaru['code']        = ($beam['tipe_beam'] == 'LUSI') ? 'BLN' : 'BSN';
            $inputBaru['id_gudang']   = $input['id_gudang'];
            $inputBaru['id_satuan_1'] = $input['id_satuan_1'];
            $inputBaru['id_satuan_2'] = $input['id_satuan_2'];
            $inputBaru['catatan']     = $request['catatan'];

            $checkNoMesin = MesinHistory::where(['id_beam' => $request['id_beam_baru'], 'id_mesin' => $inputBaru['id_mesin']]);
            if ($checkNoMesin->count() == 0) {
                MesinHistory::where(['id_beam' => $request['id_beam_baru'], 'id_mesin' => $request['id_mesin_current']])->delete();
                MesinHistory::create(['id_beam' => $request['id_beam_baru'], 'id_mesin' => $inputBaru['id_mesin']]);
            }

            $logStokMasuk = unsetMultiKeys(['volume_1', 'volume_2', 'id_nomor_beam', 'catatan'], $inputBaru);
            $logStokMasuk['volume_masuk_1'] = $input['volume_1'];
            $logStokMasuk['volume_masuk_2'] = $input['volume_2'];
            LogStokPenerimaan::where('id', $request['id_log_stok_masuk'])->update($logStokMasuk);

            $inputDetail = unsetMultiKeys(['is_sizing'], $input);
            $inputDetail['updated_by'] = Auth::id();
            PenomoranBeamRetur::where('id', $request['id_penomoran_baru_retur'])->update($inputDetail);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }

        return Define::update($inputBaru, self::$model, $id);
    }

    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $detail = PenomoranBeamRetur::where('id', $id)->first();
            $detailRef = PenomoranBeamRetur::where('id', $detail->id_beam_retur)->first();

            MesinHistory::where('id', $detail->id_beam)->delete();
            NomorKikw::where('id', $detail->relBeam()->value('id_nomor_kikw'))->delete();
            Beam::where('id', $detail->id_beam)->delete();
            LogStokPenerimaan::whereIn('id', [$detail->id_log_stok_penerimaan, $detailRef->id_log_stok_penerimaan])->delete();
            PenomoranBeamRetur::where('id', $detail->id_beam_retur)->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
        return Define::delete($id, self::$model);
    }
}
