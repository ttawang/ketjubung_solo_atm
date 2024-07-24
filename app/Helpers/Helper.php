<?php

use App\Models\Gudang;
use App\Models\LogStokPenerimaan;
use App\Models\Menu;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function getMenus($dataSelected = [])
{

    if (!Auth::check()) return [];

    $dataMenus = [];
    $subQuery = DB::table('menus')->selectRaw('parent_id as id, COALESCE(COUNT(id), 0) as have_child')->groupBy('parent_id');
    Menu::join('mapping_menus', 'menus.id', 'mapping_menus.menus_id')->leftJoinSub($subQuery, 'sq', function ($join) {
        return $join->on('menus.id', 'sq.id');
    })
        ->when(empty($dataSelected), function ($query) {
            return $query->where('mapping_menus.roles_id', Auth::user()->roles_id);
        })
        ->addSelect(['menus.*', 'sq.have_child'])
        ->orderByRaw('menus.sort_prefix ASC, menus.parent_id ASC, menus.name DESC')
        ->each(function ($item, $key) use (&$dataMenus, $dataSelected) {
            $isChild = $item['parent_id'] != '';
            $haveChild = $item['have_child'];
            $name = $item['name'];
            $route = $item['link'];
            $checkRouteWithChild = function ($name, $route, $isChild, $haveChild) {
                if ($route == '') return 'javascript:void(0);';
                if ($isChild) {
                    return route($route, [$name]);
                } else {
                    return ($isChild || !$haveChild) ? route($route) : 'javascript:void(0);';
                }
            };

            $checkRoute = $checkRouteWithChild($name, $route, $isChild, $haveChild);
            $prefixHeader = strtoupper($item['prefix']);
            $idParent = $item['parent_id'] ?? $item['id'];
            $navItem = [
                'id'           => $item['id'],
                'link'         => $checkRoute,
                'icon'         => $item['icon'],
                'name'         => $name,
                'text'         => ucwords(str_replace('_', ' ', $item['name'])),
                'sort_number'  => $item['sort_number'],
                'childs'       => [],
                'selected'     => (empty($dataSelected)) ? '' : in_array($item['id'], $dataSelected),
                'prefix'       => $item['prefix'],
                'prefix_aside' => $item['prefix_aside'],
                'display_name' => $item['display_name'],
                'display_name_full' => $item['display_name_full']
            ];
            if ($isChild) {
                $dataMenus[$prefixHeader][$idParent]['childs'][] = $navItem;
            } else {
                $dataMenus[$prefixHeader][$idParent] = $navItem;
            }
        });

    $dataMenuSorted = array_map(function ($item) {
        $dataMenuChildSorted = array_map(function ($itemChild) {
            if (!empty($itemChild['childs'])) $itemChild['childs'] = collect($itemChild['childs'])->sortBy('sort_number')->toArray();
            return $itemChild;
        }, $item);
        return collect($dataMenuChildSorted)->sortBy('sort_number')->toArray();
    }, $dataMenus);

    return $dataMenuSorted;
}

function getMenusAside()
{

    if (!Auth::check()) return [];

    $dataMenusAside = [];
    $rolesId = Auth::user()->roles_id;
    DB::table('menus')->where('is_main_nav', 'TIDAK')
        // ->where('prefix_aside', '!=', 'inspecting')
        ->where('is_active', 'YA')
        ->orderByRaw('sort_prefix ASC, parent_id ASC, name DESC')
        ->each(function ($item, $key) use (&$dataMenusAside, $rolesId) {
            $item = (array) $item;
            $prefixHeader = strtolower($item['prefix_aside']);

            if ($rolesId == 3 || $rolesId == 4) {
                if (($prefixHeader == 'weaving' && !in_array($item['id'], divideWeavingMenus($rolesId)))) return true;
            }

            $name = $item['name'];
            $route = $item['link'];
            $checkRoute = route($route);
            $idParent = $item['parent_id'] ?? $item['id'];
            $navItem = [
                'id'           => $item['id'],
                'link'         => $checkRoute,
                'icon'         => $item['icon'],
                'name'         => $name,
                'text'         => ucwords(str_replace('_', ' ', $item['name'])),
                'sort_number'  => $item['sort_number'],
                'childs'       => [],
                'prefix'       => $item['prefix'],
                'display_name' => $item['display_name'],
                'display_name_full' => $item['display_name_full']
            ];

            $dataMenusAside[$prefixHeader][$idParent] = $navItem;
        });

    $dataMenuAsideSorted = array_map(function ($item) {
        return collect($item)->sortBy('sort_number')->toArray();
    }, $dataMenusAside);

    return $dataMenuAsideSorted;
}

function menuAssets($prefix, $name, $breadcumbs = [], $isDatatable = false, $isAside = false, $custom = false, $isMenubarFold = false)
{
    $dataMenu = getMenus();
    $dataMenusAside = getMenusAside();
    $dataMenuTop = unsetMultiKeys(['dyeing', 'penerimaan'], $dataMenusAside);
    $ucwordsName = ucwords($name);
    $ucwordsPrefix = ucwords($prefix);
    $prefixName = ($prefix !== '') ? "{$ucwordsPrefix} {$ucwordsName}" : "{$ucwordsName}";
    $isAdmin = Auth::user()->is('administrator') ?? false;
    return [
        'isAdmin' => $isAdmin,
        'username' => Auth::user()->nickname,
        'rolesName' => Auth::user()->roles_name ?? 'administrator',
        'topNav' => $isAdmin ? '' : 'layout-top-nav ',
        'title' => $ucwordsName,
        'page' => $prefixName,
        'tree' => strtolower($prefix),
        'name' => camelCaseConvert(strtolower($name)),
        'breadcumbs' => $breadcumbs,
        'isDatatable' => $isDatatable,
        'isAside' => $isAside,
        'isMenubarFold' => $isMenubarFold,
        'menus' => $dataMenu,
        'menusAside' => $dataMenusAside,
        'menusTop' => $dataMenuTop,
        'custom' => $custom
    ];
}

function divideWeavingMenus($roleId)
{
    $map = [
        3 => [8, 12, 30, 31, 33, 34, 37, 44], //PREPARATORY
        4 => [13, 46] //TENUN
    ];
    return $map[$roleId] ?? [];
}

function getCurrentRoutes($state = 'index', $id = [], $onlyName = false)
{
    $currentRoute = Route::currentRouteName();
    if ($onlyName) {
        return (strpos($currentRoute, '.') !== false) ? explode('.', $currentRoute)[1] : $currentRoute; //name
    } else {
        return route(str_replace(['index', 'show'], $state, $currentRoute), $id);
    }
}

function toFixed($number = 0, $decimals = 2)
{
    $expo = pow(10, $decimals);
    return intval($number * $expo) / $expo;
}

function CHECK_LOCAL()
{
    return ($_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8080');
}

function floatValue($val, $invert = false)
{
    $val = str_replace(",", ".", $val);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);
    return $invert ? str_replace(".", ",", floatval($val)) : floatval($val);
}

function normalizeDecimal($value1, $value2, $result)
{
    $countDecimal1 = strlen(substr(strrchr($value1, "."), 1));
    $countDecimal2 = strlen(substr(strrchr($value2, "."), 1));
    $decimalPlaces = $countDecimal1 > $countDecimal2 ? $countDecimal1 : $countDecimal2;
    return sprintf("%." . $decimalPlaces . "f", $result);
}

function camelCaseConvert($input)
{
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}

function capitalCaseConvert($input)
{
    return ucwords(str_replace('_', ' ', $input));
}

function paginateArray($data, $perPage = 15)
{
    $page = Paginator::resolveCurrentPage();
    $total = count($data);
    $results = array_slice($data, ($page - 1) * $perPage, $perPage);
    return new LengthAwarePaginator($results, $total, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
    ]);
}

function getFIeldGudang($id)
{
    $addField = [];
    if ($id == 2 || $id == 3) {
        $addField['select_warna'] = '<div class="form-group">
            <label>Warna</label>
            <select name="input[id_warna]" id="select_warna" data-placeholder="-- Pilih Warna --"
                data-route="' . route('helper.getWarna') . '" class="form-control select2">
            </select>
        </div>';
    }

    if ($id == 3) {
        $addField['select_motif'] = '<div class="form-group">
            <label>Motif</label>
            <select name="input[id_motif]" id="select_motif" data-placeholder="-- Pilih Motif --"
                data-route="' . route('helper.getMotif') . '" class="form-control select2">
            </select>
        </div>';
    }

    return $addField;
}

function generateCodePengiriman($idTipe, $state, $currentCode = 'PB')
{
    $code = 'PB';
    if ($state == 'input') {
        if ($idTipe == 1) {
            $code = 'PB';
        } else if ($idTipe == 2) {
            $code = 'DO';
        } else if ($idTipe == 3) {
            $code = 'BHD,BHDS';
        } else if ($idTipe == 4) {
            $code = 'BBWS';
        } else if ($idTipe == 5) {
            $code = 'BL';
        } else if ($idTipe == 6) {
            $code = 'BS';
        } else if ($idTipe == 7) {
            $code = 'BG';
        } else if ($idTipe == 8) {
            $code = 'BGID,BGIG';
        } else if ($idTipe == 9) {
            $code = 'DW';
        } else if ($idTipe == 10) {
            $code = 'BBW';
        } else if ($idTipe == 11) {
            $code = 'BBTLT';
        } else if ($idTipe == 12) {
            $code = 'BBTST';
        } else if ($idTipe == 13) {
            $code = 'DPST';
        } else if ($idTipe == 14) {
            $code = 'DPRT';
        } else if ($idTipe == 15) {
            $code = 'BLN';
        } else if ($idTipe == 16) {
            $code = 'BSN';
        } else if ($idTipe == 17) {
            $code = 'BBWSS';
        } else if ($idTipe == 18) {
            $code = 'PB';
        } else if ($idTipe == 19) {
            $code = 'BHD';
        } else if ($idTipe == 20) {
            $code = 'BHDG';
        } else if ($idTipe == 21) {
            $code = 'BHD,BHDG,BHDS';
        }
    } else if ($state == 'output') {
        if ($idTipe == 1) {
            $code = 'BBD';
        } else if ($idTipe == 2) {
            $code = 'BHD';
        } else if ($idTipe == 3) {
            $code = 'BBW';
        } else if ($idTipe == 4) {
            $code = 'BHDS';
        } else if ($idTipe == 5) {
            $code = 'BBTL';
        } else if ($idTipe == 6) {
            $code = 'BBTS';
        } else if ($idTipe == 7) {
            $code = 'BBG';
        } else if ($idTipe == 8) {
            $code = 'BGF';
        } else if ($idTipe == 9) {
            $code = 'CF';
        } else if ($idTipe == 10) {
            $code = 'BHD';
        } else if ($idTipe == 11) {
            $code = 'BBTLR';
        } else if ($idTipe == 12) {
            $code = 'BBTSR';
        } else if ($idTipe == 13) {
            $code = 'BPS';
        } else if ($idTipe == 14) {
            $code = 'BPR';
        } else if ($idTipe == 15) {
            $code = 'BBTL';
        } else if ($idTipe == 16) {
            $code = 'BBTS';
        } else if ($idTipe == 17) {
            $code = 'BHDR';
        } else if ($idTipe == 18) {
            $code = 'BBDG';
        } else if ($idTipe == 19) {
            $code = 'BBDR';
        } else if ($idTipe == 20) {
            $code = 'BBW';
        } else if ($idTipe == 21) {
            $code = 'BBWP';
        }
    }

    return $code;
}

function pengirimanBeamWithoutAsset()
{
    return [7, 8];
}

function motifKhususAsset($proses = 'inspekting')
{
    $data = [
        'inspekting' => [6, 7, 8, 13],
        'finishing' => [4, 5, 19, 20, 23, 25]
    ];
    return $data[$proses];
}

function getModel($model = '')
{
    $possibleModels = [
        'Gudang'                       => \App\Models\Gudang::class,
        'Barang'                       => \App\Models\Barang::class,
        'Tipe'                         => \App\Models\Tipe::class,
        'TipePengiriman'               => \App\Models\TipePengiriman::class,
        'Satuan'                       => \App\Models\Satuan::class,
        'Users'                        => \App\Models\User::class,
        'Roles'                        => \App\Models\Role::class,
        'Menu'                         => \App\Models\Menu::class,
        'PenerimaanBarang'             => \App\Models\PenerimaanBarang::class,
        'PenerimaanBarangDetail'       => \App\Models\PenerimaanBarangDetail::class,
        'Doubling'                     => \App\Models\Doubling::class,
        'DoublingDetail'               => \App\Models\DoublingDetail::class,
        'PengirimanBarang'             => \App\Models\PengirimanBarang::class,
        'PengirimanBarangDetail'       => \App\Models\PengirimanBarangDetail::class,
        'LogStokPenerimaan'            => \App\Models\LogStokPenerimaan::class,
        'LogStokPenerimaanDyeing'      => \App\Models\LogStokPenerimaanDyeing::class,
        'Dyeing'                       => \App\Models\Dyeing::class,
        'DyeingDetail'                 => \App\Models\DyeingDetail::class,
        'DyeingJasaLuar'               => \App\Models\DyeingJasaLuar::class,
        'DyeingJasaLuarDetail'         => \App\Models\DyeingJasaLuarDetail::class,
        'DyeingGresik'                 => \App\Models\DyeingGresik::class,
        'DyeingGresikDetail'           => \App\Models\DyeingGresikDetail::class,
        'DyeingWarna'                  => \App\Models\DyeingWarna::class,
        'DyeingGresikWarna'            => \App\Models\DyeingGresikWarna::class,
        'PengirimanDyeingGresik'       => \App\Models\PengirimanDyeingGresik::class,
        'PengirimanDyeingGresikDetail' => \App\Models\PengirimanDyeingGresikDetail::class,
        'OperasionalDyeing'            => \App\Models\OperasionalDyeing::class,
        'OperasionalDyeingDetail'      => \App\Models\OperasionalDyeingDetail::class,
        'LogStokPenerimaanWeaving'     => \App\Models\LogStokPenerimaanWeaving::class,
        'Warping'                      => \App\Models\Warping::class,
        'WarpingDetail'                => \App\Models\WarpingDetail::class,
        'Pakan'                        => \App\Models\Pakan::class,
        'PakanDetail'                  => \App\Models\PakanDetail::class,
        'Leno'                         => \App\Models\Leno::class,
        'LenoDetail'                   => \App\Models\LenoDetail::class,
        'Sizing'                       => \App\Models\Sizing::class,
        'SizingDetail'                 => \App\Models\SizingDetail::class,
        'Tenun'                        => \App\Models\Palet::class,
        'Motif'                        => \App\Models\Motif::class,
        'Kualitas'                     => \App\Models\Kualitas::class,
        'MappingKualitas'              => \App\Models\MappingKualitas::class,
        'Warna'                        => \App\Models\Warna::class,
        'Mesin'                        => \App\Models\Mesin::class,
        'Supplier'                     => \App\Models\Supplier::class,
        'InspectGreyDetail'            => \App\Models\InspectGreyDetail::class,
        'InspectingGrey'         => \App\Models\InspectingGrey::class,
        'InspectingGreyDetail'         => \App\Models\InspectingGreyDetail::class,
        'InspectingGreyKualitas'       => \App\Models\InspectingGreyKualitas::class,
        'Inspecting'                   => \App\Models\Inspecting::class,
        'InspectingDetail'             => \App\Models\InspectingDetail::class,
        'Dudulan'                      => \App\Models\Dudulan::class,
        'DudulanDetail'                => \App\Models\DudulanDetail::class,
        'InspectDudulan'               => \App\Models\InspectDudulan::class,
        'InspectDudulanDetail'         => \App\Models\InspectDudulanDetail::class,
        'JahitSambung'                 => \App\Models\JahitSambung::class,
        'JahitSambungDetail'           => \App\Models\JahitSambungDetail::class,
        'Folding'                      => \App\Models\Folding::class,
        'FoldingDetail'                => \App\Models\FoldingDetail::class,
        'FinishingCabut'               => \App\Models\FinishingCabut::class,
        'FinishingCabutDetail'         => \App\Models\FinishingCabutDetail::class,
        'InspectFinishingCabut'        => \App\Models\InspectFinishingCabut::class,
        'InspectFinishingCabutDetail'  => \App\Models\InspectFinishingCabutDetail::class,
        'Jigger'                       => \App\Models\Jigger::class,
        'JiggerDetail'                 => \App\Models\JiggerDetail::class,
        'Drying'                       => \App\Models\Drying::class,
        'DryingDetail'                 => \App\Models\DryingDetail::class,
        'P1'                           => \App\Models\P1::class,
        'P1Detail'                     => \App\Models\P1Detail::class,
        'InspectP1'                    => \App\Models\InspectP1::class,
        'InspectP1Detail'              => \App\Models\InspectP1Detail::class,
        'P2'                           => \App\Models\P2::class,
        'P2Detail'                     => \App\Models\P2Detail::class,
        'InspectP2'                    => \App\Models\InspectP2::class,
        'InspectP2Detail'              => \App\Models\InspectP2Detail::class,
        'JahitP2'                      => \App\Models\JahitP2::class,
        'JahitP2Detail'                => \App\Models\JahitP2Detail::class,
        'Tenun'                        => \App\Models\Tenun::class,
        'TenunDetail'                  => \App\Models\TenunDetail::class,
        'Pekerja'                      => \App\Models\Pekerja::class,
        'Resep'                        => \App\Models\Resep::class,
        'ResepDetail'                  => \App\Models\ResepDetail::class,
        'Group'                        => \App\Models\Group::class,
        'GroupDetail'                  => \App\Models\GroupDetail::class,
        'Cucuk'                        => \App\Models\Cucuk::class,
        'CucukDetail'                  => \App\Models\CucukDetail::class,
        'Tyeing'                       => \App\Models\Tyeing::class,
        'TyeingDetail'                 => \App\Models\TyeingDetail::class,
        'NomorBeam'                    => \App\Models\NomorBeam::class,
        'DistribusiPakan'              => \App\Models\DistribusiPakan::class,
        'DistribusiPakanDetail'        => \App\Models\DistribusiPakanDetail::class,
        'PenerimaanChemical'           => \App\Models\PenerimaanChemical::class,
        'PenerimaanChemicalDetail'     => \App\Models\PenerimaanChemicalDetail::class,
        'PengirimanSarung'             => \App\Models\PengirimanSarung::class,
        'PengirimanSarungDetail'       => \App\Models\PengirimanSarungDetail::class,
        'PenerimaanSarung'             => \App\Models\PenerimaanSarung::class,
        'PenerimaanSarungDetail'       => \App\Models\PenerimaanSarungDetail::class,
        'ChemicalFinishing'            => \App\Models\ChemicalFinishing::class,
        'ChemicalFinishingSarung'      => \App\Models\ChemicalFinishing::class,
        'ChemicalFinishingDetail'      => \App\Models\ChemicalFinishing::class,
        'PenomoranBeamRetur'           => \App\Models\PenomoranBeamRetur::class,
        'SaldoAwal'                    => \App\Models\SaldoAwal::class,
        'AbsensiPekerja'               => \App\Models\AbsensiPekerja::class,
        'Stokopname'                   => \App\Models\Stokopname::class,
        'StokopnameDetail'             => \App\Models\StokopnameDetail::class,
        'ResepChemicalFinishing'       => \App\Models\ResepChemicalFinishing::class,
        'ResepChemicalFinishingDetail' => \App\Models\ResepChemicalFinishingDetail::class,
        'DyeingGrey'                   => \App\Models\DyeingGrey::class,
        'DyeingGreyDetail'             => \App\Models\DyeingGreyDetail::class,
        'Chemical'                     => \App\Models\Chemical::class,
        'ChemicalDetail'               => \App\Models\ChemicalDetail::class,

        // 'Palet'                    => \App\Models\Palet::class,
        // 'PaletDetail'              => \App\Models\PaletDetail::class,
        // 'InspectGrey'              => \App\Models\InspectGrey::class,
    ];

    return ($model != '') ? $possibleModels[$model] : $possibleModels;
}

function permissionCodeTenun($isString = true)
{
    $code = ['DPS', 'DPR', 'BO', 'BBTS'];
    return $isString ? implode(',', $code) : $code;
}

function getModelWarehouse($id)
{
    $possibleLogModels = [
        1 => 'LogStokPenerimaan',
        2 => 'LogStokPenerimaanDyeing',
        3 => 'LogStokPenerimaanWeaving',
    ];

    return $possibleLogModels[$id];
}

function convertGramToKg($value)
{
    return (float) toFixed($value * 0.001);
}

function unsetMultiKeys($keys, $array, $isMultiDimensional = false)
{
    $arrays = $array;
    $multiArrays = [];
    foreach ($keys as $value) {
        if ($isMultiDimensional) {
            foreach ($arrays as $key => $subValue) {
                unset($subValue[$value]);
                $multiArrays[] = $subValue;
            }
        } else {
            unset($arrays[$value]);
        }
    }
    return $isMultiDimensional ? $multiArrays : $arrays;
}

function userTopNav()
{
    $permissionRoleUser = [1, 8, 9];
    return in_array(Auth::user()->roles_id, $permissionRoleUser);
}

function checkValidatedButtons($isValidated, $buttons, $isDetail)
{
    $buttonIfValidated = ['refresh', 'filter'];
    if ($isDetail) $buttonIfValidated = ['refreshDetail'];
    return ($isValidated && !Auth::user()->is('administrator')) ? $buttonIfValidated : $buttons;
}

function SaldoAwalCodeText($code = null)
{
    $arr['PB']   = 'Benang Grey';
    $arr['BBD']  = 'Benang Grey untuk Dyeing';
    $arr['DO']   = 'Benang Warna Hasil Dyeing';
    $arr['BHD']  = 'Benang Warna di Logistik';
    $arr['BBW']  = 'Benang Warna untuk Gudang Warping atau Gudang Pakan (Sudah Dikirim)';
    $arr['BBWS'] = 'Benang Warna Sisa Warping';
    $arr['BL']   = 'Beam Lusi';
    $arr['BS']   = 'Beam Songket';
    $arr['BBWP']  = 'Benang Warna untuk Pakan';
    $arr['BPR']  = 'Benang Pakan (Rappier)';
    $arr['BPS']  = 'Benang Pakan (Shuttle)';
    $arr['BO']   = 'Benang Leno';
    $arr['BBTL'] = 'Beam Lusi Akan Naik ke Mesin';
    $arr['BBTS'] = 'Beam Songket Akan Naik ke Mesin';
    $arr['BGIG'] = 'Inspekting Grey';
    $arr['BGD']  = 'Dudulan';
    $arr['BGID'] = 'Inspekting Dudulan';
    $arr['JS']   = 'Finishing Jahit Sambung';
    $arr['P1']   = 'Finishing P1';
    $arr['IP1']  = 'Finishing Inspect P1';
    $arr['FC']   = 'Finishing Cabut';
    $arr['IFC']  = 'Inspect Finishing Cabut';
    $arr['JCS']  = 'Finishing Jigger & Cuci Sarung';
    $arr['DR']   = 'Drying';
    $arr['P2']   = 'Finishing P2';
    $arr['IP2']  = 'Finishing Inspect P2';
    $arr['JP2']  = 'Finishing Jahit P2';
    $arr['DW']   = 'Chemical Dyeing';
    $arr['CF']   = 'Chemical Finishing';
    return $code ? $arr[$code] : $arr;
}

function StokopnameCodeText($code = null)
{
    $arr['PB']   = 'Benang Grey';
    $arr['BBD']  = 'Benang Grey untuk Dyeing';
    // $arr['DO']   = 'Benang Warna Hasil Dyeing';
    $arr['BHD']  = 'Benang Warna di Logistik';
    $arr['BBW']  = 'Benang Warna untuk Gudang Warping atau Gudang Pakan (Sudah Dikirim)';
    $arr['BBWS'] = 'Benang Warna Sisa Warping';
    $arr['BL']   = 'Beam Lusi Hasil Warping';
    $arr['BS']   = 'Beam Songket Hasil Warping';
    $arr['BBWP'] = 'Benang Warna untuk Pakan';
    $arr['BPR']  = 'Benang Pakan (Rappier)';
    $arr['BPS']  = 'Benang Pakan (Shuttle)';
    $arr['BO']   = 'Benang Leno';
    $arr['BGIG'] = 'Inspekting Grey';
    $arr['BGD']  = 'Dudulan';
    $arr['BGID'] = 'Inspekting Dudulan';
    $arr['JS']   = 'Finishing Jahit Sambung';
    $arr['P1']   = 'Finishing P1';
    $arr['IP1']  = 'Finishing Inspect P1';
    $arr['FC']   = 'Finishing Cabut';
    $arr['IFC']  = 'Inspect Finishing Cabut';
    $arr['JCS']  = 'Finishing Jigger & Cuci Sarung';
    $arr['DR']   = 'Drying';
    $arr['P2']   = 'Finishing P2';
    $arr['IP2']  = 'Finishing Inspect P2';
    $arr['JP2']  = 'Finishing Jahit P2';
    // $arr['BBTL'] = 'Beam Lusi Akan Naik ke Mesin';
    // $arr['BBTS'] = 'Beam Songket Akan Naik ke Mesin';
    $arr['DW']   = 'Chemical Dyeing';
    $arr['CF']   = 'Chemical Finishing';
    return $code ? $arr[$code] : $arr;
}

function changeShift($currentShift)
{
    $ruleShift = [
        'PAGI'  => 'MALAM',
        'MALAM' => 'SIANG',
        'SIANG' => 'PAGI'
    ];
    return $ruleShift[$currentShift];
}

function checkCodeStokopname($code, $key)
{
    $array['class1'] = ['PB', 'BBD', 'BO', 'DW', 'CF']; //TIDAK ADA WARNA
    $array['class2'] = ['DO', 'BHD', 'BBW', 'BBWS', 'BPR', 'BPS', 'BBWP']; //ADA WARNA
    $array['class3'] = ['BL', 'BS']; //ADA WARNA ADA BEAM
    $array['class4'] = ['BGIG', 'BGD', 'BGID', 'JS', 'P1', 'IP1', 'FC', 'IFC', 'DR', 'JG', 'P2', 'IP2', 'JP2']; //ADA WARNA ADA BEAM ADA GRADE ADA KUALITAS
    return in_array($code, $array[$key]);
}

function checkStokBarang($filter, $isFirst = true, $tanggal = '')
{
    $select         = 'id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, is_sizing, id_beam, id_mesin, tipe_pra_tenun,
                       SUM(COALESCE(volume_masuk_1, 0) - COALESCE(volume_keluar_1, 0))::decimal as stok_utama, SUM(COALESCE(volume_masuk_2, 0) - COALESCE(volume_keluar_2, 0))::decimal as stok_pilihan';
    $groupBy        = 'id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, is_sizing, id_beam, id_mesin, tipe_pra_tenun';
    // $having         = 'SUM(COALESCE(volume_masuk_1, 0) - COALESCE(volume_keluar_1, 0)) > 0 OR SUM(COALESCE(volume_masuk_2, 0) - COALESCE(volume_keluar_2, 0)) > 0';
    $filter = array_filter($filter);
    $queryStok = LogStokPenerimaan::when(isset($filter['id_gudang']), function ($query) use ($filter) {
        return $query->where('id_gudang', $filter['id_gudang']);
    }, function ($query) {
        return $query->whereNull('id_gudang');
    })->when(isset($filter['id_barang']), function ($query) use ($filter) {
        return $query->where('id_barang', $filter['id_barang']);
    }, function ($query) {
        return $query->whereNull('id_barang');
    })->when(isset($filter['id_satuan_1']), function ($query) use ($filter) {
        return $query->where('id_satuan_1', $filter['id_satuan_1']);
    }, function ($query) {
        return $query->whereNull('id_satuan_1');
    })->when(isset($filter['id_satuan_2']), function ($query) use ($filter) {
        return $query->where('id_satuan_2', $filter['id_satuan_2']);
    }, function ($query) {
        return $query->whereNull('id_satuan_2');
    })->when(isset($filter['id_warna']), function ($query) use ($filter) {
        return $query->where('id_warna', $filter['id_warna']);
    }, function ($query) {
        return $query->whereNull('id_warna');
    })->when(isset($filter['id_motif']), function ($query) use ($filter) {
        return $query->where('id_motif', $filter['id_motif']);
    }, function ($query) {
        return $query->whereNull('id_motif');
    })->when(isset($filter['id_grade']), function ($query) use ($filter) {
        return $query->where('id_grade', $filter['id_grade']);
    }, function ($query) {
        return $query->whereNull('id_grade');
    })->when(isset($filter['id_kualitas']), function ($query) use ($filter) {
        return $query->where('id_kualitas', $filter['id_kualitas']);
    }, function ($query) {
        return $query->whereNull('id_kualitas');
    })->when(isset($filter['code']), function ($query) use ($filter) {
        return $query->where('code', $filter['code']);
    }, function ($query) {
        return $query->whereNull('code');
    })->when(isset($filter['is_sizing']), function ($query) use ($filter) {
        return $query->where('is_sizing', $filter['is_sizing']);
    }, function ($query) {
        return $query->whereNull('is_sizing');
    })->when(isset($filter['id_beam']), function ($query) use ($filter) {
        return $query->where('id_beam', $filter['id_beam']);
    }, function ($query) {
        return $query->whereNull('id_beam');
    })->when(isset($filter['id_mesin']), function ($query) use ($filter) {
        return $query->where('id_mesin', $filter['id_mesin']);
    }, function ($query) {
        return $query->whereNull('id_mesin');
    })->when(isset($filter['tipe_pra_tenun']), function ($query) use ($filter) {
        return $query->where('tipe_pra_tenun', $filter['tipe_pra_tenun']);
    }, function ($query) {
        return $query->whereNull('tipe_pra_tenun');
    })
        ->when($tanggal, function ($query, $value) {
            return $query->where('tanggal', '<', $value);
        })
        ->selectRaw($select)
        ->groupByRaw($groupBy);

    if ($isFirst) {
        $response = $queryStok->first()->stok_utama ?? 0;
    } else {
        $response = $queryStok->first() ?? (object) collect(['stok_utama' => 0, 'stok_pilihan' => 0])->all();
    }
    return $response;
}

function getGroupSumArray($data, $optional)
{
    $groups = array();
    foreach ($data as $item) {
        $key = $item['id_barang'] . '' . $item['id_warna'] . '' . $item['id_motif'] . '' . $item['id_grade'] . '' . $item[$optional];
        if (!array_key_exists($key, $groups)) {
            $groups[$key] = $item;
        } else {
            $groups[$key]['jml'] += $item['jml'];
        }
    }
    return $groups;
}

function fixBenangPakan($idBarang)
{
    $namaBenang = DB::table('tbl_barang')->where('id', $idBarang)->value('name');
    $param = str_replace('\'', '', strtolower($namaBenang)) ?? '';
    $checkPakan = DB::table('tbl_barang')->whereRaw("LOWER(REPLACE(name, $$'$$, '')) LIKE '%" . $param . "%'")->where('id_tipe', 5);
    if ($checkPakan->count() > 0) {
        return ['id' => $checkPakan->first()->id, 'name' => $namaBenang];
    } else {
        return ['id' => 0, 'name' => $namaBenang];
    }
};
