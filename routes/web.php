<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BphtController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CustomHelperController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Database\BarangController;
use App\Http\Controllers\Database\GroupController;
use App\Http\Controllers\Database\GudangController;
use App\Http\Controllers\Database\KualitasController;
use App\Http\Controllers\Database\MesinController;
use App\Http\Controllers\Database\MotifController;
use App\Http\Controllers\Database\NomorBeamController;
use App\Http\Controllers\Database\PekerjaController;
use App\Http\Controllers\Database\ResepChemicalFinishingController;
use App\Http\Controllers\Database\ResepController;
use App\Http\Controllers\Database\SatuanController;
use App\Http\Controllers\Database\SupplierController;
use App\Http\Controllers\Database\TipeController;
use App\Http\Controllers\Database\TipePengirimanController;
use App\Http\Controllers\Database\WarnaController;
use App\Http\Controllers\Finishing\ChemicalFinishingController;
use App\Http\Controllers\Finishing\DryingController;
use App\Http\Controllers\Finishing\FinishingCabutController;
use App\Http\Controllers\Finishing\FoldingController;
use App\Http\Controllers\Finishing\InspectFinishingCabutController;
use App\Http\Controllers\Finishing\InspectP1Controller;
use App\Http\Controllers\Finishing\InspectP2Controller;
use App\Http\Controllers\Finishing\JahitP2Controller;
use App\Http\Controllers\Finishing\JahitSambungController;
use App\Http\Controllers\Finishing\JiggerController;
use App\Http\Controllers\Finishing\P1Controller;
use App\Http\Controllers\Finishing\P2Controller;
use App\Http\Controllers\Finishing\PenerimaanChemicalController;
use App\Http\Controllers\Finishing\PenerimaanSarungController;
use App\Http\Controllers\Finishing\PengirimanSarungController;
use App\Http\Controllers\GantiPasswordController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\Inspecting\DudulanController;
use App\Http\Controllers\Inspecting\InspectDudulanController;
use App\Http\Controllers\Inspecting\InspectingGreyController;
use App\Http\Controllers\InspectingGrey2Controller;
use App\Http\Controllers\Inventory\MutasiController;
use App\Http\Controllers\Inventory\PersediaanController;
use App\Http\Controllers\Inventory\SaldoAwalController;
use App\Http\Controllers\Inventory\StokopnameController;
use App\Http\Controllers\KesalahanBeamController;
use App\Http\Controllers\Management\MenuController;
use App\Http\Controllers\Management\RolesController;
use App\Http\Controllers\Management\UsersController;
use App\Http\Controllers\Production\AbsensiPekerjaController;
use App\Http\Controllers\Production\ChemicalController;
use App\Http\Controllers\Production\CucukController;
use App\Http\Controllers\Production\DistribusiPakanController;
use App\Http\Controllers\Production\DoublingController;
use App\Http\Controllers\Production\PenerimaanBarangController;
use App\Http\Controllers\Production\DyeingController;
use App\Http\Controllers\Production\DyeingGresikController;
use App\Http\Controllers\Production\DyeingGreyController;
use App\Http\Controllers\Production\DyeingJasaLuarController;
use App\Http\Controllers\Production\LenoController;
use App\Http\Controllers\Production\OperasionalDyeingController;
use App\Http\Controllers\Production\PakanController;
use App\Http\Controllers\Production\PenerimaanBarangDoublingController;
use App\Http\Controllers\Production\PenerimaanChemicalDyeingController;
use App\Http\Controllers\Production\PengirimanBarangController;
use App\Http\Controllers\Production\PengirimanDyeingGresikController;
use App\Http\Controllers\Production\PenomoranBeamController;
use App\Http\Controllers\Production\PenomoranBeamReturController;
use App\Http\Controllers\Production\SizingController;
use App\Http\Controllers\Production\TenunController;
use App\Http\Controllers\Production\TyeingController;
use App\Http\Controllers\Production\WarpingController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'helper', 'as' => 'helper.'], function () {
        Route::get('detailForm/{id}', [HelperController::class, 'detailForm']);
        Route::get('detailFormView/{id?}', [HelperController::class, 'detailFormView']);
        Route::get('getWarnaForm/{id}', [HelperController::class, 'warnaForm'])->name('getWarnaForm.form');
        Route::get('getMappingMenuForm/{id}', [HelperController::class, 'MappingMenuForm'])->name('mappingmenu.form');
        Route::get('detailFormDatabase/{id}', [HelperController::class, 'detailFormDatabase'])->name('detailFormDatabase');
        Route::get('acceptFormView/{id}', [HelperController::class, 'acceptFormView'])->name('acceptFormView');
        Route::get('selectedBarangPakan/{idBeam}/{code}', [HelperController::class, 'selectedBarangPakan'])->name('selectedBarangPakan');

        //SELECT2
        Route::get('getEmptySelect', [HelperController::class, 'getEmptySelect'])->name('getEmptySelect');
        Route::get('getRole', [HelperController::class, 'getRole'])->name('getRole');
        Route::get('getGudang', [HelperController::class, 'getGudang'])->name('getGudang');
        Route::get('getSupplier', [HelperController::class, 'getSupplier'])->name('getSupplier');
        Route::get('getMesin', [HelperController::class, 'getMesin'])->name('getMesin');
        Route::get('getBarang', [HelperController::class, 'getBarang'])->name('getBarang');
        Route::get('getBarangDyeing', [HelperController::class, 'getBarangDyeing'])->name('getBarangDyeing');
        Route::get('getBarangPengiriman', [HelperController::class, 'getBarangPengiriman'])->name('getBarangPengiriman');
        Route::get('getBarangWithStok', [HelperController::class, 'getBarangWithStok'])->name('getBarangWithStok');
        Route::get('getBarangWarnaWithStok', [HelperController::class, 'getBarangWarnaWithStok'])->name('getBarangWarnaWithStok');
        Route::get('getBarangStokopname', [HelperController::class, 'getBarangStokopname'])->name('getBarangStokopname');
        Route::get('getSatuan', [HelperController::class, 'getSatuan'])->name('getSatuan');
        Route::get('getResep', [HelperController::class, 'getResep'])->name('getResep');
        Route::get('getWarna', [HelperController::class, 'getWarna'])->name('getWarna');
        Route::get('getTipe', [HelperController::class, 'getTipe'])->name('getTipe');
        Route::get('getMotif', [HelperController::class, 'getMotif'])->name('getMotif');
        Route::get('getGrade', [HelperController::class, 'getGrade'])->name('getGrade');
        Route::get('getKualitas', [HelperController::class, 'getKualitas'])->name('getKualitas');
        Route::get('getTipePengiriman', [HelperController::class, 'getTipePengiriman'])->name('getTipePengiriman');
        Route::get('getBarangWarping', [HelperController::class, 'getBarangWarping'])->name('getBarangWarping');
        Route::get('getBeam', [HelperController::class, 'getBeam'])->name('getBeam');
        Route::get('getNomorBeam', [HelperController::class, 'getNomorBeam'])->name('getNomorBeam');
        Route::get('getBeamSongket', [HelperController::class, 'getBeamSongket'])->name('getBeamSongket');
        Route::get('getGroup', [HelperController::class, 'getGroup'])->name('getGroup');
        Route::get('getPekerja', [HelperController::class, 'getPekerja'])->name('getPekerja');
        Route::get('getBarangTenun', [HelperController::class, 'getBarangTenun'])->name('getBarangTenun');
        Route::get('getPekerjaMesin/{id_mesin}', [HelperController::class, 'getPekerjaMesin'])->name('getPekerjaMesin');
        Route::get('getNomorKikw', [HelperController::class, 'getNomorKikw'])->name('getNomorKikw');
        Route::get('getCode', [HelperController::class, 'getCode'])->name('getCode');
        Route::get('getDyeingJasaLuar', [HelperController::class, 'getDyeingJasaLuar'])->name('getDyeingJasaLuar');
        Route::get('getDyeingGrey', [HelperController::class, 'getDyeingGrey'])->name('getDyeingGrey');
        Route::get('getDyeingGresik', [HelperController::class, 'getDyeingGresik'])->name('getDyeingGresik');
        Route::get('getNotaPengiriman', [HelperController::class, 'getNotaPengiriman'])->name('getNotaPengiriman');
        Route::get('getDoubling', [HelperController::class, 'getDoubling'])->name('getDoubling');
        Route::get('getJasaLuar', [HelperController::class, 'getJasaLuar'])->name('getJasaLuar');
        Route::get('getSelectedBarangInspecting', [HelperController::class, 'getSelectedBarangInspecting'])->name('getSelectedBarangInspecting');
        Route::post('getStokBarang', [HelperController::class, 'getStokBarang'])->name('getStokBarang');
        //SELECT2

        Route::post('checkedMappingMenu', [HelperController::class, 'checkedMappingMenu'])->name('mappingmenu.checked');
        Route::post('checkedBeam', [HelperController::class, 'checkedBeam'])->name('checkedBeam');
        Route::post('validateForm', [HelperController::class, 'validateForm'])->name('validateForm');
        Route::post('applyMesin', [HelperController::class, 'applyMesin'])->name('applyMesin');
        Route::post('storePekerjaMesin', [HelperController::class, 'storePekerjaMesin'])->name('storePekerjaMesin');
        Route::post('storeLembur', [HelperController::class, 'storeLembur'])->name('storeLembur');
        Route::post('accept', [HelperController::class, 'accept'])->name('accept');
        Route::post('acceptAll', [HelperController::class, 'acceptAll'])->name('acceptAll');
        Route::post('reject', [HelperController::class, 'reject'])->name('reject');
        Route::post('sendAll', [HelperController::class, 'sendAll'])->name('sendAll');
        Route::post('cancelSendAll', [HelperController::class, 'cancelSendAll'])->name('cancelSendAll');
        Route::post('inputPakanKirim', [HelperController::class, 'inputPakanKirim'])->name('inputPakanKirim');

        Route::post('import_excel_penerimaan_barang', [HelperController::class, 'importPenerimaanBarang'])->name('importPenerimaanBarang');
        Route::post('import_excel_doubling', [HelperController::class, 'importDoubling'])->name('importDoubling');
        Route::post('import_excel_benang_grey_dyeing', [HelperController::class, 'importBenangGreyDyeing'])->name('importBenangGreyDyeing');
        Route::post('import_excel_overcone', [HelperController::class, 'importDyeingOvercone'])->name('importDyeingOvercone');
        Route::post('import_excel_hasil_dyeing', [HelperController::class, 'importHasilDyeing'])->name('importHasilDyeing');
        Route::post('import_excel_warping', [HelperController::class, 'importBarangWarping'])->name('importBarangWarping');
        Route::post('import_excel_beam_lusi', [HelperController::class, 'importBarangLusi'])->name('importBarangLusi');
        Route::post('import_excel_beam_songket', [HelperController::class, 'importBarangSongket'])->name('importBarangSongket');
        Route::post('import_excel_pakan_shuttle', [HelperController::class, 'importPakanShuttle'])->name('importPakanShuttle');
        Route::post('import_excel_pakan_rappier', [HelperController::class, 'importPakanRappier'])->name('importPakanRappier');
        Route::post('import_excel_pakan_tenun_lusi', [HelperController::class, 'importTenunLusi'])->name('importTenunLusi');
        Route::post('import_excel_pakan_tenun_songket', [HelperController::class, 'importTenunSongket'])->name('importTenunSongket');

        Route::post('import_excel_pakan', [HelperController::class, 'importExcelPakan'])->name('importExcelPakan');
        Route::post('import_excel_inspekting', [HelperController::class, 'importExcelInspekting'])->name('importExcelInspekting');
        Route::post('import_excel_dudulan', [HelperController::class, 'importExcelDudulan'])->name('importExcelDudulan');
        Route::post('import_excel_inspect_dudulan', [HelperController::class, 'importExcelInspectDudulan'])->name('importExcelInspectDudulan');
        Route::post('import_excel_jahit_sambung', [HelperController::class, 'importExcelJahitSambung'])->name('importExcelJahitSambung');
        Route::post('import_excel_p1', [HelperController::class, 'importExcelP1'])->name('importExcelP1');
        Route::post('import_excel_finishing_cabut', [HelperController::class, 'importExcelFinishingCabut'])->name('importExcelFinishingCabut');
        Route::post('import_excel_drying', [HelperController::class, 'importExcelDrying'])->name('importExcelDrying');
        Route::post('import_excel_p2', [HelperController::class, 'importExcelP2'])->name('importExcelP2');
        Route::post('import_excel_inpect_p2', [HelperController::class, 'importExcelInspectP2'])->name('importExcelInspectP2');
        Route::post('import_excel_jahit', [HelperController::class, 'importExcelJahit'])->name('importExcelJahit');
        Route::post('import_excel_jahit_p2', [HelperController::class, 'importExcelJahitP2'])->name('importExcelJahitP2');
        Route::post('import_excel_inspect_p1', [HelperController::class, 'importExcelInpectP1'])->name('importExcelInpectP1');
        Route::post('import_excel_chemical_finishing', [HelperController::class, 'importExcelChemicalFinishing'])->name('importExcelChemicalFinishing');
        Route::post('import_excel_benang_warna_pakan', [HelperController::class, 'importExcelBenangWarnaPakan'])->name('importExcelBenangWarnaPakan');

        Route::post('import_excel_stokopname', [HelperController::class, 'importStokopname'])->name('importStokopname');
        Route::get('export_excel_stokopname', [HelperController::class, 'exportStokopname'])->name('exportStokopname');

        //WARNA
        Route::post('storeWarna/{id?}', [HelperController::class, 'storeWarna'])->name('storeWarna');
        Route::post('storeResepWarna', [HelperController::class, 'storeResepWarna'])->name('storeResepWarna');
        Route::post('storeResepWarnaOD', [HelperController::class, 'storeResepWarnaOD'])->name('storeResepWarnaOD');
        Route::post('returInspect', [HelperController::class, 'returInspect'])->name('returInspect');
        Route::delete('deleteWarna/{id}', [HelperController::class, 'deleteWarna'])->name('deleteWarna');
        //WARNA

        Route::delete('deleteAll', [HelperController::class, 'deleteAll'])->name('deleteAll');
    });

    Route::post('change-password', [GantiPasswordController::class, 'change'])->name('changePassword');

    Route::group(['prefix' => 'cetak', 'as' => 'cetak.'], function () {
        Route::get('penerimaanBarang', [CetakController::class, 'penerimaanBarang'])->name('penerimaanBarang');
        Route::get('pengirimanBarang', [CetakController::class, 'pengirimanBarang'])->name('pengirimanBarang');
        Route::get('dyeing', [CetakController::class, 'dyeing'])->name('dyeing');
        Route::get('tenun', [CetakController::class, 'tenun'])->name('tenun');
        Route::get('distribusiPakan', [CetakController::class, 'distribusiPakan'])->name('distribusiPakan');
        Route::get('pengirimanSarung', [CetakController::class, 'pengirimanSarung'])->name('pengirimanSarung');
    });

    Route::group(['prefix' => 'kesalahan-beam'], function () {
        Route::get('/', [KesalahanBeamController::class, 'custom'])->name('custom');
        Route::get('get-beam', [KesalahanBeamController::class, 'getBeam']);
        Route::get('table', [KesalahanBeamController::class, 'table']);
        Route::get('get-select', [KesalahanBeamController::class, 'getSelect']);
        Route::get('get-data', [KesalahanBeamController::class, 'getData']);
        Route::post('simpan', [KesalahanBeamController::class, 'simpan']);
    });

    Route::middleware('link.valid')->group(function () {
        Route::group(['prefix' => 'database', 'as' => 'database.'], function () {
            Route::resources([
                'gudang'            => GudangController::class,
                'barang'            => BarangController::class,
                'tipe'              => TipeController::class,
                'satuan'            => SatuanController::class,
                'tipe_pengiriman'   => TipePengirimanController::class,
                'motif'             => MotifController::class,
                'kualitas'          => KualitasController::class,
                'warna'             => WarnaController::class,
                'resep'             => ResepController::class,
                'supplier'          => SupplierController::class,
                'mesin'             => MesinController::class,
                'pekerja'           => PekerjaController::class,
                'group'             => GroupController::class,
                'nomor_beam'        => NomorBeamController::class,
                'resep_chemical_finishing' => ResepChemicalFinishingController::class,
            ]);
        });

        Route::group(['prefix' => 'production', 'as' => 'production.'], function () {
            Route::resources([
                'penerimaan_barang'          => PenerimaanBarangController::class,
                'doubling'                   => DoublingController::class,
                'pengiriman_barang'          => PengirimanBarangController::class,
                'distribusi_pakan'           => DistribusiPakanController::class,
                'pengiriman_sarung'          => PengirimanSarungController::class,
                'penerimaan_sarung'          => PenerimaanSarungController::class,
                'penerimaan_chemical'        => PenerimaanChemicalController::class,
                'dyeing'                     => DyeingController::class,
                'dyeing_jasa_luar'           => DyeingJasaLuarController::class,
                'dyeing_gresik'              => DyeingGresikController::class,
                'dyeing_grey'                => DyeingGreyController::class,
                'pengiriman_dyeing_gresik'   => PengirimanDyeingGresikController::class,
                'penerimaan_chemical_dyeing' => PenerimaanChemicalDyeingController::class,
                'operasional_dyeing'         => OperasionalDyeingController::class,
                'penomoran_beam_retur'       => PenomoranBeamReturController::class,
                'cucuk'                      => CucukController::class,
                'tyeing'                     => TyeingController::class,
                'absensi_pekerja'            => AbsensiPekerjaController::class,
                'tenun'                      => TenunController::class,
                'chemical_finishing'         => ChemicalFinishingController::class,
                'chemical'                   => ChemicalController::class
            ]);
            Route::group(['prefix' => 'proses'], function () {
                Route::get('validasi/{model}/{id}/{status}', [CustomHelperController::class, 'validasi']);
                Route::get('cetak', [CustomHelperController::class, 'cetak']);
                Route::get('cetak-distribusi-pakan', [CustomHelperController::class, 'cetakDistribusiPakan']);
                Route::post('simpan', [CustomHelperController::class, 'simpan']);
                Route::get('terima-semua-barang-jasa-luar', [CustomHelperController::class, 'terimaSemuaBarangJasaLuar']);
                Route::get('get-spk-pengiriman', [CustomHelperController::class, 'getSpkPengiriman']);
            });

            Route::group(['prefix' => 'bpht'], function () {
                /* Route::get('kosongkan', [BphtController::class, 'kosongkan']);
                Route::get('undo', [BphtController::class, 'undo']); */
                Route::get('detail', [BphtController::class, 'detail']);
                Route::get('table', [BphtController::class, 'table']);
                Route::post('simpan', [BphtController::class, 'simpan']);
                Route::post('update', [BphtController::class, 'update']);
                Route::get('hapus', [BphtController::class, 'hapus']);
                Route::get('get-data/{id}', [BphtController::class, 'getData']);
                Route::get('get-beam', [BphtController::class, 'getBeam']);
                Route::get('get-songket', [BphtController::class, 'getSongket']);
                Route::get('get-barang', [BphtController::class, 'getBarang']);
            });
            Route::group(['prefix' => 'inspect_grey_2'], function () {
                Route::get('get-barang', [InspectingGrey2Controller::class, 'getBarang']);
                Route::post('simpan', [InspectingGrey2Controller::class, 'simpan']);
                Route::post('update', [InspectingGrey2Controller::class, 'update']);
                Route::get('get-data/{id}', [InspectingGrey2Controller::class, 'getData']);
                Route::get('hapus/{id}', [InspectingGrey2Controller::class, 'hapus']);
                Route::get('table/{id}', [InspectingGrey2Controller::class, 'table']);
                Route::get('view/{id}', [InspectingGrey2Controller::class, 'view']);
                Route::post('simpan-kualitas/{id}', [InspectingGrey2Controller::class, 'simpanKualitas']);
            });

            Route::group(['prefix' => 'warping', 'as' => 'warping.'], function () {
                Route::get('/', [WarpingController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [WarpingController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [WarpingController::class, 'table']);
                Route::post('simpan', [WarpingController::class, 'simpan']);
                Route::get('get-data/{id}/{mode}', [WarpingController::class, 'getData']);
                Route::get('get-barang/{tipe}/{id?}', [WarpingController::class, 'getBarang']);
                Route::get('get-no-kikw/{id}', [WarpingController::class, 'getNoKikw']);
                Route::get('hapus/{id}/{mode}', [WarpingController::class, 'hapus']);
                Route::get('get-stok-barang/{barang}/{warna}/{gudang}/{tipe?}', [WarpingController::class, 'getStokBarang']);
                Route::get('get-nomor-beam', [WarpingController::class, 'getNomorBeam']);
                Route::get('get-gudang/{tipe}/{id?}', [WarpingController::class, 'getGudang']);
                Route::get('get-barang/{tipe}/{id?}', [WarpingController::class, 'getBarang']);
                Route::get('get-warna/{tipe}/{id?}', [WarpingController::class, 'getWarna']);
                Route::get('get-motif/{tipe}/{id?}', [WarpingController::class, 'getMotif']);
                Route::get('get-mesin/{tipe}/{id?}', [WarpingController::class, 'getMesin']);
            });
            Route::group(['prefix' => 'penomoran_beam', 'as' => 'penomoran_beam.'], function () {
                Route::get('/', [PenomoranBeamController::class, 'index'])->name('index');
                Route::get('view', [PenomoranBeamController::class, 'view']);
                Route::get('table', [PenomoranBeamController::class, 'table']);
                Route::get('get-mesin', [PenomoranBeamController::class, 'getMesin']);
                Route::get('get-data/{id}', [PenomoranBeamController::class, 'getData']);
                Route::post('simpan', [PenomoranBeamController::class, 'simpan']);
            });

            /* Route::group(['prefix' => 'palet', 'as' => 'palet.'], function () {
                Route::get('/', [PaletController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [PaletController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [PaletController::class, 'table']);
                Route::post('simpan', [PaletController::class, 'simpan']);
                Route::get('get-data/{id}/{mode}', [PaletController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [PaletController::class, 'hapus']);
                Route::get('get-stok-barang/{barang}/{warna}/{gudang}/{tipe?}', [PaletController::class, 'getStokBarang']);
            }); */

            Route::group(['prefix' => 'pakan', 'as' => 'pakan.'], function () {
                Route::get('/', [PakanController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [PakanController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [PakanController::class, 'table']);
                Route::post('simpan', [PakanController::class, 'simpan']);
                Route::post('simpan_shuttle', [PakanController::class, 'simpan_shuttle'])->name('simpan.shuttle');
                Route::get('get-data/{id}/{mode}', [PakanController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [PakanController::class, 'hapus']);
                Route::get('get-stok-barang/{barang}/{warna}/{gudang}/{tipe?}', [PakanController::class, 'getStokBarang']);
                Route::get('get-gudang/{tipe}/{id?}', [PakanController::class, 'getGudang']);
                Route::get('get-barang/{tipe}/{id?}', [PakanController::class, 'getBarang']);
                Route::get('get-warna/{tipe}/{id?}', [PakanController::class, 'getWarna']);
            });
            Route::group(['prefix' => 'leno', 'as' => 'leno.'], function () {
                Route::get('/', [LenoController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [LenoController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [LenoController::class, 'table']);
                Route::post('simpan', [LenoController::class, 'simpan']);
                Route::get('get-data/{id}/{mode}', [LenoController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [LenoController::class, 'hapus']);
                Route::get('get-stok-barang/{barang}/{warna}/{gudang}/{tipe?}', [LenoController::class, 'getStokBarang']);
                Route::get('get-gudang/{tipe}/{id?}', [LenoController::class, 'getGudang']);
                Route::get('get-barang/{tipe}/{id?}', [LenoController::class, 'getBarang']);
                Route::get('get-warna/{tipe}/{id?}', [LenoController::class, 'getWarna']);
            });
            Route::group(['prefix' => 'sizing', 'as' => 'sizing.'], function () {
                Route::get('/', [SizingController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [SizingController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [SizingController::class, 'table']);
                Route::post('simpan', [SizingController::class, 'simpan']);
                Route::get('get-data/{id}/{mode}', [SizingController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [SizingController::class, 'hapus']);
                Route::get('get-stok-barang/{barang}/{warna}/{gudang}/{tipe?}', [SizingController::class, 'getStokBarang']);
                Route::get('get-barang/{tipe}/{id?}', [SizingController::class, 'getBarang']);
                Route::get('get-supplier', [SizingController::class, 'getSupplier']);
            });
            Route::group(['prefix' => 'inspect_grey', 'as' => 'inspect_grey.'], function () {
                Route::get('/', [InspectingGreyController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}', [InspectingGreyController::class, 'view']);
                Route::get('table/{mode}/{id?}', [InspectingGreyController::class, 'table']);
                Route::post('simpan', [InspectingGreyController::class, 'simpan']);
                Route::get('get-barang', [InspectingGreyController::class, 'getBarang']);
                Route::get('get-beam', [InspectingGreyController::class, 'getBeam']);
                Route::get('hapus/{mode}/{id}', [InspectingGreyController::class, 'hapus']);
                Route::get('get-stok/{id}', [InspectingGreyController::class, 'getStok']);
                Route::get('get-data/{id}', [InspectingGreyController::class, 'getData']);
                Route::get('get-stok-potong/{id}/{id_group}', [InspectingGreyController::class, 'getStokPotong']);
                Route::get('cetak', [InspectingGreyController::class, 'cetak']);
            });
            Route::group(['prefix' => 'dudulan', 'as' => 'dudulan.'], function () {
                Route::get('/', [DudulanController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [DudulanController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [DudulanController::class, 'table']);
                Route::post('simpan', [DudulanController::class, 'simpan']);
                Route::get('get-data', [DudulanController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [DudulanController::class, 'hapus']);
                Route::get('get-kualitas', [DudulanController::class, 'getKualitas']);
                Route::get('get-supplier', [DudulanController::class, 'getSupplier']);
                Route::get('get-gudang', [DudulanController::class, 'getGudang']);
                Route::get('get-barang', [DudulanController::class, 'getBarang']);
                Route::get('get-stok-barang', [DudulanController::class, 'getStokBarang']);
            });
            Route::group(['prefix' => 'jahit_sambung', 'as' => 'jahit_sambung.'], function () {
                Route::get('/', [JahitSambungController::class, 'index'])->name('index');
                Route::get('view', [JahitSambungController::class, 'view']);
                Route::get('table', [JahitSambungController::class, 'table']);
                Route::get('cetak', [JahitSambungController::class, 'cetak']);
                Route::get('get-barang', [JahitSambungController::class, 'getBarang']);
                Route::get('get-stok-barang', [JahitSambungController::class, 'getStokBarang']);
                Route::get('get-kualitas', [JahitSambungController::class, 'getKualitas']);
                Route::get('get-gudang', [JahitSambungController::class, 'getGudang']);
                Route::get('get-barang', [JahitSambungController::class, 'getBarang']);
                Route::get('get-stok-barang', [JahitSambungController::class, 'getStokBarang']);
                Route::post('simpan', [JahitSambungController::class, 'simpan']);
                Route::get('hapus/{id}', [JahitSambungController::class, 'hapus']);
                Route::get('get-data', [JahitSambungController::class, 'getData']);
            });
            Route::group(['prefix' => 'folding', 'as' => 'folding.'], function () {
                Route::get('/', [FoldingController::class, 'index'])->name('index');
                Route::get('view', [FoldingController::class, 'view']);
                Route::get('table', [FoldingController::class, 'table']);
                Route::get('get-barang', [FoldingController::class, 'getBarang']);
                Route::get('get-stok-barang', [FoldingController::class, 'getStokBarang']);
                Route::get('get-kualitas', [FoldingController::class, 'getKualitas']);
                Route::get('get-gudang', [FoldingController::class, 'getGudang']);
                Route::get('get-barang/{gudang}', [FoldingController::class, 'getBarang']);
                Route::get('get-stok-barang/{mesin}/{barang}/{warna}/{gudang}/{motif}/{beam}/{grade}', [FoldingController::class, 'getStokBarang']);
                Route::post('simpan', [FoldingController::class, 'simpan']);
                Route::get('hapus/{id}', [FoldingController::class, 'hapus']);
                Route::get('get-data/{id}', [FoldingController::class, 'getData']);
            });
            Route::group(['prefix' => 'inspect_dudulan', 'as' => 'inspect_dudulan.'], function () {
                Route::get('/', [InspectDudulanController::class, 'index'])->name('index');
                Route::get('view', [InspectDudulanController::class, 'view']);
                Route::get('table', [InspectDudulanController::class, 'table']);
                Route::get('get-barang', [InspectDudulanController::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectDudulanController::class, 'getStokBarang']);
                Route::get('get-kualitas', [InspectDudulanController::class, 'getKualitas']);
                Route::get('get-gudang', [InspectDudulanController::class, 'getGudang']);
                Route::get('get-barang', [InspectDudulanController::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectDudulanController::class, 'getStokBarang']);
                Route::post('simpan', [InspectDudulanController::class, 'simpan']);
                Route::get('hapus/{id}', [InspectDudulanController::class, 'hapus']);
                Route::get('get-data', [InspectDudulanController::class, 'getData']);
                Route::get('get-spk', [InspectDudulanController::class, 'getSpk']);
            });
            /* Route::group(['prefix' => 'inspect_dudulan', 'as' => 'inspect_dudulan.'], function () {
                Route::get('/', [InspectDudulanController::class, 'index'])->name('index');
                Route::get('view', [InspectDudulanController::class, 'view']);
                Route::get('table', [InspectDudulanController::class, 'table']);
                Route::get('get-barang', [InspectDudulanController::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectDudulanController::class, 'getStokBarang']);
                Route::get('get-kualitas', [InspectDudulanController::class, 'getKualitas']);
                Route::get('get-gudang', [InspectDudulanController::class, 'getGudang']);
                Route::get('get-barang/{gudang}', [InspectDudulanController::class, 'getBarang']);
                Route::get('get-stok-barang/{mesin}/{barang}/{warna}/{gudang}/{motif}/{beam}/{grade}/{kualitas}', [InspectDudulanController::class, 'getStokBarang']);
                Route::post('simpan', [InspectDudulanController::class, 'simpan']);
                Route::get('hapus/{id}', [InspectDudulanController::class, 'hapus']);
                Route::get('get-data/{id}', [InspectDudulanController::class, 'getData']);
            }); */
            Route::group(['prefix' => 'p1', 'as' => 'p1.'], function () {
                Route::get('/', [P1Controller::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [P1Controller::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [P1Controller::class, 'table']);
                // Route::post('simpan', [P1Controller::class, 'simpan']);
                Route::get('get-data', [P1Controller::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [P1Controller::class, 'hapus']);
                Route::get('get-kualitas', [P1Controller::class, 'getKualitas']);
                Route::get('get-supplier', [P1Controller::class, 'getSupplier']);
                Route::get('get-gudang', [P1Controller::class, 'getGudang']);
                Route::get('get-barang', [P1Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [P1Controller::class, 'getStokBarang']);
            });
            Route::group(['prefix' => 'inspect_p1', 'as' => 'inspect_p1.'], function () {
                Route::get('/', [InspectP1Controller::class, 'index'])->name('index');
                Route::get('view', [InspectP1Controller::class, 'view']);
                Route::get('table', [InspectP1Controller::class, 'table']);
                Route::get('get-barang', [InspectP1Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectP1Controller::class, 'getStokBarang']);
                Route::get('get-kualitas', [InspectP1Controller::class, 'getKualitas']);
                Route::get('get-gudang', [InspectP1Controller::class, 'getGudang']);
                Route::get('get-barang', [InspectP1Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectP1Controller::class, 'getStokBarang']);
                Route::post('simpan', [InspectP1Controller::class, 'simpan']);
                Route::get('hapus/{id}', [InspectP1Controller::class, 'hapus']);
                Route::get('get-data', [InspectP1Controller::class, 'getData']);
                Route::get('get-spk', [InspectP1Controller::class, 'getSpk']);
            });
            Route::group(['prefix' => 'finishing_cabut', 'as' => 'finishing_cabut.'], function () {
                Route::get('/', [FinishingCabutController::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [FinishingCabutController::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [FinishingCabutController::class, 'table']);
                // Route::post('simpan', [FinishingCabutController::class, 'simpan']);
                Route::get('get-data', [FinishingCabutController::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [FinishingCabutController::class, 'hapus']);
                Route::get('get-kualitas', [FinishingCabutController::class, 'getKualitas']);
                Route::get('get-supplier', [FinishingCabutController::class, 'getSupplier']);
                Route::get('get-gudang', [FinishingCabutController::class, 'getGudang']);
                Route::get('get-barang', [FinishingCabutController::class, 'getBarang']);
                Route::get('get-stok-barang', [FinishingCabutController::class, 'getStokBarang']);
            });
            Route::group(['prefix' => 'inspect_finishing_cabut', 'as' => 'inspect_finishing_cabut.'], function () {
                Route::get('/', [InspectFinishingCabutController::class, 'index'])->name('index');
                Route::get('view', [InspectFinishingCabutController::class, 'view']);
                Route::get('table', [InspectFinishingCabutController::class, 'table']);
                Route::get('get-barang', [InspectFinishingCabutController::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectFinishingCabutController::class, 'getStokBarang']);
                Route::get('get-kualitas', [InspectFinishingCabutController::class, 'getKualitas']);
                Route::get('get-gudang', [InspectFinishingCabutController::class, 'getGudang']);
                Route::get('get-barang', [InspectFinishingCabutController::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectFinishingCabutController::class, 'getStokBarang']);
                Route::post('simpan', [InspectFinishingCabutController::class, 'simpan']);
                Route::get('hapus/{id}', [InspectFinishingCabutController::class, 'hapus']);
                Route::get('get-data', [InspectFinishingCabutController::class, 'getData']);
                Route::get('get-spk', [InspectFinishingCabutController::class, 'getSpk']);
            });
            Route::group(['prefix' => 'jigger', 'as' => 'jigger.'], function () {
                Route::get('/', [JiggerController::class, 'index'])->name('index');
                Route::get('view', [JiggerController::class, 'view']);
                Route::get('table', [JiggerController::class, 'table']);
                Route::get('get-barang', [JiggerController::class, 'getBarang']);
                Route::get('get-stok-barang', [JiggerController::class, 'getStokBarang']);
                Route::get('get-kualitas', [JiggerController::class, 'getKualitas']);
                Route::get('get-gudang', [JiggerController::class, 'getGudang']);
                Route::get('get-barang', [JiggerController::class, 'getBarang']);
                Route::get('get-stok-barang', [JiggerController::class, 'getStokBarang']);
                Route::post('simpan', [JiggerController::class, 'simpan']);
                Route::get('hapus/{id}', [JiggerController::class, 'hapus']);
                Route::get('get-data', [JiggerController::class, 'getData']);
            });
            /*Route::group(['prefix' => 'chemical_finishing', 'as' => 'chemical_finishing.'], function () {
                Route::get('view/{menu}/{id}', [ChemicalFinishingController::class, 'view']);
                Route::get('table/{menu}/{id}', [ChemicalFinishingController::class, 'table']);
                Route::get('get-barang/{menu}', [ChemicalFinishingController::class, 'getBarang']);
                Route::get('get-gudang/{menu}', [ChemicalFinishingController::class, 'getGudang']);
                Route::get('get-stok-barang/{menu}/{barang}/{gudang}', [ChemicalFinishingController::class, 'getStokBarang']);
                Route::get('get-data/{id}', [ChemicalFinishingController::class, 'getData']);
                Route::post('simpan', [ChemicalFinishingController::class, 'simpan']);
                Route::get('hapus/{id}', [ChemicalFinishingController::class, 'hapus']);
            });*/
            Route::group(['prefix' => 'drying', 'as' => 'drying.'], function () {
                Route::get('/', [DryingController::class, 'index'])->name('index');
                Route::get('view', [DryingController::class, 'view']);
                Route::get('table', [DryingController::class, 'table']);
                Route::get('get-barang', [DryingController::class, 'getBarang']);
                Route::get('get-stok-barang', [DryingController::class, 'getStokBarang']);
                Route::get('get-kualitas', [DryingController::class, 'getKualitas']);
                Route::get('get-gudang', [DryingController::class, 'getGudang']);
                Route::get('get-barang', [DryingController::class, 'getBarang']);
                Route::get('get-stok-barang', [DryingController::class, 'getStokBarang']);
                Route::post('simpan', [DryingController::class, 'simpan']);
                Route::get('hapus/{id}', [DryingController::class, 'hapus']);
                Route::get('get-data', [DryingController::class, 'getData']);
            });
            Route::group(['prefix' => 'p2', 'as' => 'p2.'], function () {
                Route::get('/', [P2Controller::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [P2Controller::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [P2Controller::class, 'table']);
                // Route::post('simpan', [P2Controller::class, 'simpan']);
                Route::get('get-data', [P2Controller::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [P2Controller::class, 'hapus']);
                Route::get('get-kualitas', [P2Controller::class, 'getKualitas']);
                Route::get('get-supplier', [P2Controller::class, 'getSupplier']);
                Route::get('get-gudang', [P2Controller::class, 'getGudang']);
                Route::get('get-barang', [P2Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [P2Controller::class, 'getStokBarang']);
            });
            Route::group(['prefix' => 'inspect_p2', 'as' => 'inspect_p2.'], function () {
                Route::get('/', [InspectP2Controller::class, 'index'])->name('index');
                Route::get('view', [InspectP2Controller::class, 'view']);
                Route::get('table', [InspectP2Controller::class, 'table']);
                Route::get('get-barang', [InspectP2Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectP2Controller::class, 'getStokBarang']);
                Route::get('get-kualitas', [InspectP2Controller::class, 'getKualitas']);
                Route::get('get-gudang', [InspectP2Controller::class, 'getGudang']);
                Route::get('get-barang', [InspectP2Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [InspectP2Controller::class, 'getStokBarang']);
                Route::post('simpan', [InspectP2Controller::class, 'simpan']);
                Route::get('hapus/{id}', [InspectP2Controller::class, 'hapus']);
                Route::get('get-data', [InspectP2Controller::class, 'getData']);
                Route::get('get-spk', [InspectP2Controller::class, 'getSpk']);
            });
            Route::group(['prefix' => 'jahit_p2', 'as' => 'jahit_p2.'], function () {
                Route::get('/', [JahitP2Controller::class, 'index'])->name('index');
                Route::get('view/{mode}/{id?}/{tipe?}', [JahitP2Controller::class, 'view']);
                Route::get('table/{mode}/{id?}/{tipe?}', [JahitP2Controller::class, 'table']);
                Route::post('simpan', [JahitP2Controller::class, 'simpan']);
                Route::get('get-data', [JahitP2Controller::class, 'getData']);
                Route::get('hapus/{id}/{mode}', [JahitP2Controller::class, 'hapus']);
                Route::get('get-kualitas', [JahitP2Controller::class, 'getKualitas']);
                Route::get('get-supplier', [JahitP2Controller::class, 'getSupplier']);
                Route::get('get-gudang', [JahitP2Controller::class, 'getGudang']);
                Route::get('get-barang', [JahitP2Controller::class, 'getBarang']);
                Route::get('get-stok-barang', [JahitP2Controller::class, 'getStokBarang']);
            });
        });

        Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
            Route::resources([
                'saldoawal'  => SaldoAwalController::class,
                'stokopname' => StokopnameController::class,
            ]);
            Route::group(['prefix' => 'persediaan', 'as' => 'persediaan.'], function () {
                Route::get('/', [PersediaanController::class, 'index'])->name('index');
                Route::get('view/{mode}', [PersediaanController::class, 'view']);
                Route::get('table', [PersediaanController::class, 'table']);
            });
            Route::group(['prefix' => 'mutasi', 'as' => 'mutasi.'], function () {
                Route::get('/', [MutasiController::class, 'index'])->name('index');
                Route::get('view/{mode}', [MutasiController::class, 'view']);
                Route::get('table', [MutasiController::class, 'table']);
            });
        });

        Route::group(['prefix' => 'management', 'as' => 'management.'], function () {
            Route::resources([
                'users' => UsersController::class,
                'roles' => RolesController::class,
                'menu'  => MenuController::class
            ]);
        });
    });
});
