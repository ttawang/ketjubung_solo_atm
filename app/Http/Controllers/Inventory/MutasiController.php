<?php

namespace App\Http\Controllers\Inventory;

use App\Exports\ExportExcelFromView;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Kualitas;
use App\Models\MappingKualitas;
use App\Models\Mesin;
use App\Models\Motif;
use App\Models\ProductionCode;
use App\Models\TenunDetail;
use App\Models\TipePengiriman;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MutasiController extends Controller
{
    public function index()
    {
        $data['breadcumbs'] = [['nama' => 'Inventory', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Mutasi', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $data['menuAssets'] = menuAssets('', 'mutasi', $data['breadcumbs'], true, false, true, true);
        return view('contents.inventory.mutasi.index', $data);
    }
    public function view($mode)
    {
        if ($mode == 'produksi') {
            return view('contents.inventory.mutasi.produksi');
        } else if ($mode == 'rekap') {
            $data['tipe_pengiriman'] = TipePengiriman::orderBy('id')->get();
            $data['warna_benang'] = Warna::where('jenis', 'SINGLE')->get();
            $data['barang_benang'] = Barang::where('owner', 'SOLO')->get();
            $data['mesin_dyeing'] = Mesin::where('jenis', 'DYEING')->get();
            return view('contents.inventory.mutasi.rekap', $data);
        } else if ($mode == 'pemotongan_sarung') {
            $data['mesin'] = $this->getMesin($mode);
            $data['warna'] = $this->getWarna($mode);
            $data['motif'] = $this->getMotif($mode);
            return view('contents.inventory.mutasi.pemotongan', $data);
        }
    }
    public function table(Request $request)
    {
        if ($request->mode == 'produksi') {
            $tgl_awal = $request->tgl_awal ?? date('Y-m-d');
            $tgl_akhir = $request->tgl_akhir ?? date('Y-m-d');
            $sql = mutasiProduksi($request);
            $data['data'] = DB::table(DB::raw("({$sql}) as data"))
                ->leftJoin('tbl_beam as beam', 'beam.id', 'data.id_beam')
                ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
                ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
                ->selectRaw("
                    data.*,
                    nomor_kikw.name nomor_kikw,
                    nomor_beam.name nomor_beam
                ")
                ->when($tgl_awal, function ($q) use ($tgl_awal) {
                    return $q->where('tanggal', '>=', $tgl_awal);
                })->when($tgl_akhir, function ($q) use ($tgl_akhir) {
                    return $q->where('tanggal', '<=', $tgl_akhir);
                })->when($request->has('proses'), function ($q) use ($request) {
                    if ($request->proses != 'semua') {
                        return $q->where('sort', $request->proses);
                    };
                })->when($request->has('id_spk'), function ($q) use ($request) {
                    return $q->where('id_spk', $request->id_spk);
                })->when($request->has('status'), function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->get();
            return view('contents.inventory.mutasi.produksi-table', $data);
        } else if ($request->mode == 'rekap') {
            $data['proses'] = str_replace('_', '-', $request->proses);
            $sql = mutasiProduksi($request);
            $data['data'] = DB::table(DB::raw("({$sql}) as data"))->get();
            $data['tgl_awal'] = $request->tgl_awal ?? date('Y-m-d');
            $data['tgl_akhir'] = $request->tgl_akhir ?? date('Y-m-d');
            $data['file'] = 'contents.inventory.mutasi.rekap.' . $data['proses'] . '-table';

            $data['cetak'] = $request->has('cetak') ? true : false;

            if ($data['cetak']) {
                $judul = 'Rekap ' . ucwords(str_replace('-', ' ', $data['proses'])) . '.xlsx';
                return Excel::download(new ExportExcelFromView($data), $judul);
            }
            return view($data['file'], $data);
        }
    }
    function getProductionCode($mode)
    {
        $data = ProductionCode::all();
        return $data;
    }
    function getBarang($mode)
    {
        $data = Barang::all();
        return $data;
    }
    function getMesin($mode)
    {
        $data = Mesin::where('jenis', 'LOOM')->get();
        return $data;
    }
    function getWarna($mode)
    {
        $data = Warna::all();
        return $data;
    }
    function getMotif($mode)
    {
        $data = Motif::all();
        return $data;
    }
    function getGrade($mode)
    {
        $data = Kualitas::all();
        return $data;
    }
    function getKualitas($mode)
    {
        $data = MappingKualitas::all();
        return $data;
    }
    function getGudang($mode)
    {
        $data = Gudang::all();
        return $data;
    }
}
