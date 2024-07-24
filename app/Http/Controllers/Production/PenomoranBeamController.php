<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Beam;
use App\Models\LogStokPenerimaan;
use App\Models\Mesin;
use App\Models\MesinHistory;
use App\Models\NomorKikw;
use App\Models\SaldoAwal;
use App\Models\WarpingDetail;
use Exception;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PenomoranBeamController extends Controller
{
    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Penomoran Beam', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('weaving', 'penomoran beam', $data['breadcumbs'], true, true, true, true);

        return view('contents.production.weaving.penomoran_beam.index', $data);
    }
    public function view()
    {
        return view('contents.production.weaving.penomoran_beam.parent');
    }
    public function table()
    {
        $temp = Beam::
        where(function ($q) {
            $q->whereHas('relLogStokPenerimaanBL', function ($q) {
                $q->whereNotNull('id_warna')->whereNotNull('id_motif');
            });
        })->where(function ($q) {
            $q->doesnthave('relMesinHistory')->orWhereNull('id_nomor_kikw');
        })
            ->orderBy('id', 'desc');

        return DataTables::of($temp)
            ->addIndexColumn()
            ->addColumn('barang', function ($i) {
                return $i->relLogStokPenerimaanBL->relBarang->name . ' | ' . $i->relLogStokPenerimaanBL->relBarang->relTipe->name;
            })
            ->addColumn('motif', function ($i) {
                $motif = ($i->relLogStokPenerimaanBL->relMotif) ? $i->relLogStokPenerimaanBL->relMotif->alias : '';
                return $motif;
            })
            ->addColumn('warna', function ($i) {
                $warna = ($i->relLogStokPenerimaanBL->relWarna) ? $i->relLogStokPenerimaanBL->relWarna->alias : '';
                return $warna;
            })
            ->addColumn('mesin', function ($i) {
                $mesin = MesinHistory::where('id_beam', $i->id)->orderBy('id', 'desc')->first();
                if ($mesin) {
                    return $mesin->relMesin->name;
                } else {
                    return '';
                }
            })
            ->addColumn('no_beam', function ($i) {
                return $i->relNomorBeam ? $i->relNomorBeam->alias : '';
            })
            ->addColumn('no_kikw', function ($i) {
                if ($i->id_nomor_kikw) {
                    return $i->relNomorKikw->name;
                } else {
                    return '';
                }
            })
            ->addColumn('volume_1', function ($i) {
                return $i->relLogStokPenerimaanBL->volume_masuk_1;
            })
            ->addColumn('volume_2', function ($i) {
                return $i->relLogStokPenerimaanBL->volume_masuk_2;
            })
            ->addColumn('action', function ($i) {
                $validasi = [
                    'status' => false,
                    'data' => null,
                    'model' => 'Beam'
                ];
                $action = actionBtn($i->id, false, true, false, $validasi);
                return $action;
            })
            ->rawColumns(['action'])
            ->make('true');
    }

    function getMesin()
    {
        return Mesin::where('jenis', 'LOOM')->get();
    }
    function getData($id)
    {
        $data = Beam::with('relNomorKikw', 'relMesinHistoryLatest', 'relMesinHistoryLatest.relMesin')->where('id', $id)->first();
        return $data;
    }

    public function simpan(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $id = $request->id;
            // dd($id);
            $rule = $this->cekRequest($request);
            if ($rule['success'] == false) {
                return response()->json($rule);
            } else {
                $beam_kikw = Beam::find($id)->id_nomor_kikw;
                if (!$beam_kikw) {
                    $id_nomor_kikw = NomorKikw::create(['name' => $request->no_kikw])->id;
                    Beam::find($id)->update(['id_nomor_kikw' => $id_nomor_kikw, 'updated_by' => Auth::id()]);
                } else {
                    NomorKikw::find($beam_kikw)->update(['name' => $request->no_kikw, 'updated_by' => Auth::id()]);
                }
                $mesin = MesinHistory::where('id_beam', $id)->orderBy('id', 'desc')->first();
                if (!$mesin) {
                    $warping = WarpingDetail::where('id_beam', $id)->first();
                    if ($warping) {
                        $warping->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                        MesinHistory::create(['id_beam' => $id, 'id_mesin' => $request->id_mesin]);
                        LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update(['id_mesin' => $request->id_mesin]);
                    } else {
                        $saldoawal = SaldoAwal::where('id_beam', $id)->first();
                        if ($saldoawal) {
                            $saldoawal->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                            $log = LogStokPenerimaan::where('id_beam', $id)->orderBy('id', 'desc')->first();
                            MesinHistory::create(['id_beam' => $id, 'id_mesin' => $request->id_mesin]);
                            $log->update(['id_mesin' => $request->id_mesin]);
                        } else {
                            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
                        }
                    }
                } else {
                    $mesin->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                    $warping = WarpingDetail::where('id_beam', $id)->first();
                    if ($warping) {
                        $warping->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                        LogStokPenerimaan::find($warping->id_log_stok_penerimaan)->update(['id_mesin' => $request->id_mesin]);
                    } else {
                        $saldoawal = SaldoAwal::where('id_beam', $id)->first();
                        if ($saldoawal) {
                            $saldoawal->update(['id_mesin' => $request->id_mesin, 'updated_by' => Auth::id()]);
                            $log = LogStokPenerimaan::where('id_beam', $id)->orderBy('id', 'desc')->first();
                            $log->update(['id_mesin' => $request->id_mesin]);
                        } else {
                            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
                        }
                    }
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    function cekRequest($request)
    {
        $rules = [];
        $beam = Beam::find($request->id);
        if ($beam->id_nomor_kikw) {
            $id_nomor_kikw = $beam->id_nomor_kikw;
            $rules['no_kikw'] = [
                'required',
                Rule::unique('tbl_nomor_kikw', 'name')->whereNull('deleted_at')->ignore($id_nomor_kikw),
            ];
        } else {
            $rules['no_kikw'] = [
                'required',
                Rule::unique('tbl_nomor_kikw', 'name')->whereNull('deleted_at')
            ];
        }

        $rules['id_mesin'] = 'required|not_in:0';

        $messages['id_mesin.required'] = 'mesin harus diisi';
        $messages['id_mesin.not_in'] = 'mesin harus diisi';
        $messages['no_kikw.required'] = 'no kikw harus diisi';
        $messages['no_kikw.unique'] = 'no kikw tidak boleh sama';

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $data['success'] = false;
            $data['messages'] = $validator->getMessageBag()->toArray();
        } else {
            $data['success'] = true;
            $data['messages'] = '';
        }
        return $data;
    }
}
