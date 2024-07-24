<?php

use App\Helpers\Date;
use App\Models\FinishingCabutDetail;
use App\Models\InspectFinishingCabutDetail;
use App\Models\InspectP1Detail;
use App\Models\InspectP2Detail;
use App\Models\P1Detail;
use App\Models\P2Detail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

function getBulan($val)
{
    if ($val == 1) {
        return "Januari";
    } elseif ($val == 2) {
        return "Februari";
    } elseif ($val == 3) {
        return "Maret";
    } elseif ($val == 4) {
        return "April";
    } elseif ($val == 5) {
        return "Mei";
    } elseif ($val == 6) {
        return "Juni";
    } elseif ($val == 7) {
        return "Juli";
    } elseif ($val == 8) {
        return "Agustus";
    } elseif ($val == 9) {
        return "September";
    } elseif ($val == 10) {
        return "Oktober";
    } elseif ($val == 11) {
        return "November";
    } elseif ($val == 12) {
        return "Desember";
    }
}

function logHistory($model, $mode = null)
{
    $stringModel = strtolower($model);
    $model = getModel($model);
    $ket = "";
    if ($mode == 'create') {
        $ket = "Menambah data";
    } else if ($mode == 'update') {
        $ket = "Merubah data";
    } else if ($mode == 'delete') {
        $ket = "Menghapus data";
    } else if ($mode == 'validasi') {
        $ket = "Validasi data";
    } else if ($mode == 'batal_validasi') {
        $ket = "Batal Validasi data";
    } else {
        $ket = "Aktifitas data";
    }
    activity()->log("$ket {$stringModel} {$model}");
}
function getDataTable($data, $column = [], $proses = '', $position = [])
{
    // dd($column);
    $table = DataTables::of($data);
    $table->addIndexColumn();
    if (in_array('tanggal', $column)) {
        $raw[] = 'tanggal';
        $table->addColumn('tanggal', function ($i) {
            return Date::format($i->tanggal, 98);
        });
    }
    if (in_array('tanggal_potong', $column)) {
        $raw[] = 'tanggal_potong';
        $table->addColumn('tanggal_potong', function ($i) {
            return Date::format($i->tanggal_potong, 98);
        });
    }
    if (in_array('spk', $column)) {
        $raw[] = 'spk';
        $table->addColumn('spk', function ($i) use ($proses) {
            $parent = getModelByProses($proses)['parent_name'];
            $relName = "rel{$parent}";
            return $i->$relName->nomor;
        });
    }
    if (in_array('vendor', $column)) {
        $raw[] = 'vendor';
        $table->addColumn('vendor', function ($i) {
            return $i->relSupplier ? $i->relSupplier->name : '';
        });
    }
    if (in_array('mesin', $column)) {
        $raw[] = 'mesin';
        $table->addColumn('mesin', function ($i) {
            return $i->relMesin ? $i->relMesin->name : '';
        });
    }
    if (in_array('no_kikw', $column)) {
        $raw[] = 'no_kikw';
        $table->addColumn('no_kikw', function ($i) {
            return $i->relBeam ? $i->relBeam->no_kikw : '';
        });
    }
    if (in_array('no_kiks', $column)) {
        $raw[] = 'no_kiks';
        $table->addColumn('no_kiks', function ($i) {
            return $i->relSongket ? $i->relSongket->no_kikw : '';
        });
    }
    if (in_array('barang', $column)) {
        $raw[] = 'barang';
        $table->addColumn('barang', function ($i) {
            return $i->relBarang ? $i->relBarang->name : '';
        });
    }
    if (in_array('warna', $column)) {
        $raw[] = 'warna';
        $table->addColumn('warna', function ($i) {
            return $i->relWarna ? $i->relWarna->alias : '';
        });
    }
    if (in_array('motif', $column)) {
        $raw[] = 'motif';
        $table->addColumn('motif', function ($i) {
            return $i->relMotif ? $i->relMotif->alias : '';
        });
    }
    if (in_array('gudang', $column)) {
        $raw[] = 'gudang';
        $table->addColumn('gudang', function ($i) {
            return $i->relGudang ? $i->relGudang->name : '';
        });
    }
    if (in_array('grade', $column)) {
        $raw[] = 'grade';
        $table->addColumn('grade', function ($i) {
            return $i->relGrade ? $i->relGrade->grade : '';
        });
    }
    if (in_array('kualitas', $column)) {
        $raw[] = 'kualitas';
        $table->addColumn('kualitas', function ($i) use ($proses) {
            $model = getModelByProses($proses)['kualitas'];
            $temp = $model::where("id_{$proses}_detail", $i->id)->selectRaw('id_kualitas')->get();
            $kualitas = '';
            if ($temp->count() > 0) {
                foreach ($temp as $i) {
                    $kualitas .= $i->relKualitas->kode . ', ';
                }
                $kualitas = rtrim($kualitas, ', ');
            }
            return $kualitas;
        });
    }
    if (array_key_exists('action', $column)) {
        $raw[] = 'action';
        $table->addColumn('action', function ($i) use ($column, $proses, $position) {
            return myButton($i, $column['action'], $proses, $position);
        });
    }
    $table->rawColumns($raw);
    return $table->make(true);
}
function getModelByProses($proses)
{
    $model = [
        'dudulan' => [
            'parent_name' => 'Dudulan',
            'parent' => \App\Models\Dudulan::class,
            'detail_name' => 'DudulanDetail',
            'detail' => \App\Models\DudulanDetail::class,
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'BGIG',
            'code_output' => 'BGD',
            'code_hilang' => 'BGDH',
        ],
        'inspect_dudulan' => [
            'parent_name' => 'Dudulan',
            'parent' => \App\Models\Dudulan::class,
            'detail_name' => 'InspectDudulanDetail',
            'detail' => \App\Models\InspectDudulanDetail::class,
            'kualitas_name' => 'InspectDudulanKualitas',
            'kualitas' => \App\Models\InspectDudulanKualitas::class,
            'code_input' => 'BGD',
            'code_output' => 'BGID',
            'code_hilang' => '',
        ],
        'jahit_sambung' => [
            'parent_name' => 'JahitSambungDetail',
            'parent' => \App\Models\JahitSambungDetail::class,
            'detail_name' => '',
            'detail' => '',
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'BGF',
            'code_output' => 'JS',
            'code_hilang' => '',
        ],
        'p1' => [
            'parent_name' => 'P1',
            'parent' => \App\Models\P1::class,
            'detail_name' => 'P1Detail',
            'detail' => \App\Models\P1Detail::class,
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'JS',
            'code_output' => 'P1',
            'code_hilang' => 'P1H',
        ],
        'inspect_p1' => [
            'parent_name' => 'P1',
            'parent' => \App\Models\P1::class,
            'detail_name' => 'InspectP1Detail',
            'detail' => \App\Models\InspectP1Detail::class,
            'kualitas_name' => 'InspectP1Kualitas',
            'kualitas' => \App\Models\InspectP1Kualitas::class,
            'code_input' => 'P1',
            'code_output' => 'IP1',
            'code_hilang' => '',
        ],
        'finishing_cabut' => [
            'parent_name' => 'FinishingCabut',
            'parent' => \App\Models\FinishingCabut::class,
            'detail_name' => 'FinishingCabutDetail',
            'detail' => \App\Models\FinishingCabutDetail::class,
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'IP1',
            'code_output' => 'FC',
            'code_hilang' => 'FCH',
        ],
        'inspect_finishing_cabut' => [
            'parent_name' => 'FinishingCabut',
            'parent' => \App\Models\FinishingCabut::class,
            'detail_name' => 'InspectFinishingCabutDetail',
            'detail' => \App\Models\InspectFinishingCabutDetail::class,
            'kualitas_name' => 'InspectFinishingCabutKualitas',
            'kualitas' => \App\Models\InspectFinishingCabutKualitas::class,
            'code_input' => 'FC',
            'code_output' => 'IFC',
            'code_hilang' => '',
        ],
        'jigger' => [
            'parent_name' => 'Jigger',
            'parent' => \App\Models\JiggerDetail::class,
            'detail_name' => '',
            'detail' => '',
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'IFC',
            'code_output' => 'JCS',
            'code_hilang' => '',
        ],
        'drying' => [
            'parent_name' => 'Drying',
            'parent' => \App\Models\DryingDetail::class,
            'detail_name' => '',
            'detail' => '',
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'JCS',
            'code_output' => 'DR',
            'code_hilang' => '',
        ],
        'p2' => [
            'parent_name' => 'P2',
            'parent' => \App\Models\P2::class,
            'detail_name' => 'P2Detail',
            'detail' => \App\Models\P2Detail::class,
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'DR',
            'code_output' => 'P2',
            'code_hilang' => 'P2H',
        ],
        'inspect_p2' => [
            'parent_name' => 'P2',
            'parent' => \App\Models\P2::class,
            'detail_name' => 'InspectP2Detail',
            'detail' => \App\Models\InspectP2Detail::class,
            'kualitas_name' => 'InspectP2Kualitas',
            'kualitas' => \App\Models\InspectP2Kualitas::class,
            'code_input' => 'P2',
            'code_output' => 'IP2',
            'code_hilang' => '',
        ],
        'jahit_p2' => [
            'parent_name' => 'JahitP2',
            'parent' => \App\Models\JahitP2::class,
            'detail_name' => 'JahitP2Detail',
            'detail' => \App\Models\JahitP2Detail::class,
            'kualitas_name' => '',
            'kualitas' => '',
            'code_input' => 'IP2',
            'code_output' => 'JP2',
            'code_hilang' => 'JP2H',
        ],
    ];

    return $model[$proses];
}
function myButton($data, $listBtn = [], $proses = '', $position = [])
{
    $btn = '';
    if (in_array('inspect', $position)) {
        $modelParentName = getModelByProses($proses)['detail_name'];
        $modelParent = getModelByProses($proses)['detail'];
    } else {
        $modelParentName = getModelByProses($proses)['parent_name'];
        $modelParent = getModelByProses($proses)['parent'];
        $modelDetail = getModelByProses($proses)['detail'];
    }
    $codeInput = getModelByProses($proses)['code_input'];
    $detail =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="detail($(this));" data-id="' . $data->id . '">
            <i class="icon md-menu" aria-hidden="true"></i>
        </a>';
    $edit   =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="edit($(this));" data-id="' . $data->id . '">
            <i class="icon md-edit" aria-hidden="true"></i>
        </a>';
    $hapus  =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="hapus($(this))" data-id="' . $data->id . '">
            <i class="icon md-delete" aria-hidden="true"></i>
        </a>';
    $cetak =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic" 
            data-id="' . $data->id . '" data-proses="' . $proses . '" onclick="cetak($(this));">
            <i class="icon md-print" aria-hidden="true"></i>
        </a>';
    $simpanValidasi =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-success on-default waves-effect waves-classic"
            onclick="validasi($(this))" data-id="' . $data->id . '" data-model="' . $modelParentName . '" data-status="simpan">
            <i class="icon md-shield-check" aria-hidden="true"></i>
        </a>';
    $batalValidasi =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-danger on-default waves-effect waves-classic"
            onclick="validasi($(this))" data-id="' . $data->id . '"  data-model="' . $modelParentName . '" data-status="batal">
            <i class="icon md-close-circle" aria-hidden="true"></i>
        </a>';

    $role = Auth::user()->roles_name;
    $btnDetail = (in_array('detail', $listBtn)) ? $detail : '';
    $btnEdit = (in_array('edit', $listBtn)) ? $edit : '';
    $btnHapus = (in_array('hapus', $listBtn)) ? $hapus : '';
    $btnCetak = (in_array('cetak', $listBtn)) ? $cetak : '';
    $btnValidasi = '';
    $btnTerima = '';

    if (in_array('parent', $position)) {
        $is_validated = $data->validated_at;
        if (in_array('jasa_luar', $position)) {
            $count = $modelDetail::where("id_{$proses}", $data->id)->count();
            $btnEdit = ($count <= 0) ? $btnEdit : '';
            $btnHapus = ($count <= 0) ? $btnHapus : '';
        }
    } else if (in_array('detail', $position)) {
        $parent = "id_{$proses}";
        $dataParent = $modelParent::where('id', $data->$parent)->first();
        $is_validated = $dataParent->validated_at;
        if (in_array('jasa_luar', $position)) {
            $count = $modelDetail::where('id_parent', $data->id)->count();
            if ($data->code == $codeInput) {
                $btnTerima = ($count > 0) ? '<span class="badge badge-outline badge-success">Diterima</span>' : '';
                $btnEdit = ($count <= 0) ? $btnEdit : '';
                $btnHapus = ($count <= 0) ? $btnHapus : '';
            }
        }
    }

    if ($is_validated) {
        $btnValidasi = $batalValidasi;
        $btnEdit = '';
        $btnHapus = '';
    } else {
        $btnValidasi = $simpanValidasi;
    }
    if (in_array('detail', $position)) {
        $btnValidasi = '';
    }

    if ($role == 'validator') {
        $btn .= $btnCetak . $btnDetail . $btnValidasi;
    } else if ($role == 'administrator') {
        $btn .= $btnCetak . $btnDetail . $btnEdit . $btnHapus . $btnTerima . $btnValidasi;
    } else {
        $btn .= $btnCetak . $btnDetail . $btnEdit . $btnHapus . $btnTerima;
    }
    return $btn;
}
function actionBtn($id, $btnDetail = false, $btnEdit = false, $btnHapus = false, $validasi = null, $addCustom = null, $addData = null)
{
    $btn = '';
    if ($addCustom) {
        $btn .= $addCustom;
    }
    $role = Auth::user()->roles_name;

    $detail =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="detail($(this));" data-id="' . $id . '" ' . $addData . '>
            <i class="icon md-menu" aria-hidden="true"></i>
        </a>';
    $edit   =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="edit($(this));" data-id="' . $id . '" ' . $addData . '>
            <i class="icon md-edit" aria-hidden="true"></i>
        </a>';
    $hapus  =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
            onclick="hapus($(this))" data-id="' . $id . '" ' . $addData . '>
            <i class="icon md-delete" aria-hidden="true"></i>
        </a>';
    $simpanValidasi =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-success on-default waves-effect waves-classic"
            onclick="validasi($(this))" data-id="' . $id . '" data-model="' . $validasi['model'] . '" data-status="simpan">
            <i class="icon md-shield-check" aria-hidden="true"></i>
        </a>';
    $batalValidasi =
        '<a href="javascript:void(0);"
            class="btn btn-sm btn-icon btn-pure btn-danger on-default waves-effect waves-classic"
            onclick="validasi($(this))" data-id="' . $id . '"  data-model="' . $validasi['model'] . '" data-status="batal">
            <i class="icon md-close-circle" aria-hidden="true"></i>
        </a>';

    if ($validasi['status'] == true) {
        if ($validasi['data']) {
            if ($btnDetail == true) {
                $btn .= $detail;
            }
            if ($role === 'administrator' || $role === 'validator') {
                $btn .= $batalValidasi;
            }
        } else {
            if ($btnDetail == true) {
                $btn .= $detail;
            }
            if ($role !== 'validator') {
                if ($btnEdit == true) {
                    $btn .= $edit;
                }
                if ($btnHapus == true) {
                    $btn .= $hapus;
                }
            }
            if ($role === 'administrator' || $role === 'validator') {
                $btn .= $simpanValidasi;
            }
        }
    } else {
        if ($validasi['data'] === null) {
            if ($btnDetail == true) {
                $btn .= $detail;
            }
            if ($role !== 'validator') {
                if ($btnEdit == true) {
                    $btn .= $edit;
                }
                if ($btnHapus == true) {
                    $btn .= $hapus;
                }
            }
        }
    }
    return $btn;
}

function tglIndo($dateString)
{
    $tanggal = Carbon::parse($dateString);
    $tanggal->locale('id');
    return $tanggal->isoFormat('D MMMM YYYY');
}
function tglCustom($dateString, $style = 'd-m-Y')
{
    $carbonDate = Carbon::parse($dateString);
    $result = $carbonDate->format($style);

    return $result;
}
function tglIndoFull($dateString)
{
    $newDate = date('d-m-Y H:i:s', strtotime($dateString));
    return $newDate;
}

function getModelByCode($code = '')
{
    $model =
        [
            'P1' => ['model' => new P1Detail(), 'table' => 'tbl_p1_detail'],
            'IP1' => ['model' => new InspectP1Detail(), 'table' => 'tbl_inspect_p1_detail'],
            'FC' => ['model' => new FinishingCabutDetail(), 'table' => 'tbl_finishing_cabut_detail'],
            'IFC' => ['model' => new InspectFinishingCabutDetail(), 'table' => 'tbl_inspect_finishing_cabut_detail'],
            'P2' => ['model' => new P2Detail(), 'table' => 'tbl_p2_detail'],
            'IP2' => ['model' => new InspectP2Detail(), 'table' => 'tbl_inspect_p2_detail'],
        ];
    return ($code != '') ? $model[$code] : $model;
}

function getStok($code, $parent, $proses, $condition = [])
{
    $temp = getModelByCode($code);
    $model = $temp['model'];
    $table = $temp['table'];
    $data = null;

    if ($proses == 'inspecting') {
        $sub = $model::selectRaw("
                {$table}.{$parent},
                coalesce({$table}.id_beam,0) id_beam,
                coalesce({$table}.id_mesin,0) id_mesin,
                {$table}.id_barang,
                {$table}.id_warna,
                {$table}.id_gudang,
                {$table}.id_motif,
                log.id_grade id_grade_awal,
                SUM(coalesce(volume_1,0)) volume_1
            ")
            ->leftJoin("log_stok_penerimaan as log", "log.id", "{$table}.id_log_stok_penerimaan_keluar")
            ->when(isset($condition['id_parent']), function ($query) use ($condition, $table, $parent) {
                return $query->where("{$table}.$parent", $condition['id_parent']);
            }, function ($query) use ($table, $parent) {
                return $query->whereNull("{$table}.{$parent}");
            })->when(isset($condition['id_beam']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_beam", $condition['id_beam']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_beam");
            })->when(isset($condition['id_mesin']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_mesin", $condition['id_mesin']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_mesin");
            })->when(isset($condition['id_barang']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_barang", $condition['id_barang']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_barang");
            })->when(isset($condition['id_gudang']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_gudang", $condition['id_gudang']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_gudang");
            })->when(isset($condition['id_motif']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_motif", $condition['id_motif']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_motif");
            })->when(isset($condition['id_warna']), function ($query) use ($condition, $table) {
                return $query->where("{$table}.id_warna", $condition['id_warna']);
            }, function ($query) use ($table) {
                return $query->whereNull("{$table}.id_warna");
            })->when(isset($condition['id_grade']), function ($query) use ($condition) {
                return $query->where("log.id_grade", $condition['id_grade']);
            }, function ($query) {
                return $query->whereNull("log.id_grade");
            })->groupByRaw("{$table}.{$parent}, {$table}.id_beam, {$table}.id_mesin, {$table}.id_barang, {$table}.id_gudang, {$table}.id_warna, {$table}.id_motif, log.id_grade");

        $code2 = str_replace("I", "", $code);
        $temp2 = getModelByCode($code2);
        $model2 = $temp2['model'];
        $table2 = $temp2['table'];
        $sub2 = $model2::selectRaw("
                    {$table2}.{$parent},
                    coalesce({$table2}.id_beam,0) id_beam,
                    coalesce({$table2}.id_mesin,0) id_mesin,
                    {$table2}.id_barang,
                    {$table2}.id_warna,
                    {$table2}.id_gudang,
                    {$table2}.id_motif,
                    {$table2}.id_grade,
                    code,
                    SUM(coalesce({$table2}.volume_1,0)) volume_1
                ")
            ->when(isset($condition['id_parent']), function ($query) use ($condition, $table2, $parent) {
                return $query->where("{$table2}.$parent", $condition['id_parent']);
            }, function ($query) use ($table2, $parent) {
                return $query->whereNull("{$table2}.{$parent}");
            })->when(isset($condition['id_beam']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_beam", $condition['id_beam']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_beam");
            })->when(isset($condition['id_mesin']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_mesin", $condition['id_mesin']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_mesin");
            })->when(isset($condition['id_barang']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_barang", $condition['id_barang']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_barang");
            })->when(isset($condition['id_gudang']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_gudang", $condition['id_gudang']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_gudang");
            })->when(isset($condition['id_motif']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_motif", $condition['id_motif']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_motif");
            })->when(isset($condition['id_warna']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_warna", $condition['id_warna']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_warna");
            })->when(isset($condition['id_grade']), function ($query) use ($condition, $table2) {
                return $query->where("{$table2}.id_grade", $condition['id_grade']);
            }, function ($query) use ($table2) {
                return $query->whereNull("{$table2}.id_grade");
            })->where("code", $code2)
            ->groupByRaw("{$table2}.{$parent}, {$table2}.id_beam, {$table2}.id_mesin, {$table2}.id_barang, {$table2}.id_warna, {$table2}.id_gudang, {$table2}.id_motif, {$table2}.id_grade, code");
        // dd($sub->get(), $sub2->get());
        $data = DB::table(DB::raw("({$sub2->toSql()}) as sub2"))
            ->mergeBindings($sub2->getQuery())
            ->leftJoin(DB::raw("({$sub->toSql()}) as sub"), function ($join)  use ($parent) {
                $join->on("sub.{$parent}", "sub2.{$parent}")
                    ->on('sub.id_beam', 'sub2.id_beam')
                    ->on('sub.id_mesin', 'sub2.id_mesin')
                    ->on('sub.id_barang', 'sub2.id_barang')
                    ->on('sub.id_warna', 'sub2.id_warna')
                    ->on('sub.id_gudang', 'sub2.id_gudang')
                    ->on('sub.id_motif', 'sub2.id_motif')
                    ->on('sub.id_grade_awal', 'sub2.id_grade');
            })
            ->mergeBindings($sub->getQuery())
            ->selectRaw('SUM(coalesce(sub2.volume_1,0)) - SUM(coalesce(sub.volume_1,0)) as stok_1')
            ->first();


        if ($data) {
            $result = $data;
        } else {
            $result = [
                'stok_1' => 0,
            ];
        }

        return $result;
    }
}

function getBarangJasaLuar($from = 'log_stok', $tableName = 'log_stok_penerimaan', $condition = [], $search = null)
{
    $parts = ($search == '') ? [] : explode(" | ", $search);
    if ($from == 'log_stok') {
        $data = DB::table("$tableName as log")
            ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'log.id_gudang')
            ->leftJoin('tbl_barang as barang', 'barang.id', 'log.id_barang')
            ->leftJoin('tbl_warna as warna', 'warna.id', 'log.id_warna')
            ->leftJoin('tbl_motif as motif', 'motif.id', 'log.id_motif')
            ->leftJoin('tbl_kualitas as grade', 'grade.id', 'log.id_grade')
            ->leftJoin('tbl_mapping_kualitas as kualitas', 'kualitas.id', 'log.id_kualitas')
            ->leftJoin('tbl_beam as beam', 'beam.id', 'log.id_beam')
            ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
            ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
            ->leftJoin('tbl_beam as songket', 'songket.id', 'log.id_songket')
            ->leftJoin('tbl_nomor_kikw as nomor_kiks', 'nomor_kiks.id', 'songket.id_nomor_kikw')
            ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'log.id_mesin')
            ->leftJoin('tbl_satuan as satuan_1', 'satuan_1.id', 'log.id_satuan_1')
            ->leftJoin('tbl_satuan as satuan_2', 'satuan_2.id', 'log.id_satuan_2')
            ->selectRaw("
                log.id_gudang,
                gudang.name nama_gudang,
                log.id_barang,
                barang.name nama_barang,
                log.id_warna,
                warna.alias nama_warna,
                log.id_motif,
                motif.alias nama_motif,
                log.id_grade,
                grade.grade nama_grade,
                log.id_kualitas,
                kualitas.kode nama_kualtias,
                log.id_beam,
                nomor_kikw.name nomor_kikw,
                nomor_beam.name nomor_beam,
                log.id_songket,
                nomor_kiks.name nomor_kiks,
                log.id_mesin,
                mesin.name nama_mesin,
                log.id_satuan_1,
                satuan_1.name nama_satuan_1,
                log.id_satuan_2,
                satuan_2.name nama_satuan_2,
                log.code,
                log.tanggal_potong,
                TO_CHAR(log.tanggal_potong, 'DD-MM-YYYY') tanggal_potong_text,
                SUM(COALESCE(volume_masuk_1,0)) - SUM(COALESCE(volume_keluar_1,0)) as volume_1,
                SUM(COALESCE(volume_masuk_2,0)) - SUM(COALESCE(volume_keluar_2,0)) as volume_2
            ")
            ->when(isset($condition['id_gudang']), function ($q) use ($condition) {
                $q->where('log.id_gudang', $condition['id_gudang']);
            })
            ->when(isset($condition['id_barang']), function ($q) use ($condition) {
                $q->where('log.id_barang', $condition['id_barang']);
            })
            ->when(isset($condition['id_warna']), function ($q) use ($condition) {
                $q->where('log.id_warna', $condition['id_warna']);
            })
            ->when(isset($condition['id_motif']), function ($q) use ($condition) {
                $q->where('log.id_motif', $condition['id_motif']);
            })
            ->when(isset($condition['id_grade']), function ($q) use ($condition) {
                $q->where('log.id_grade', $condition['id_grade']);
            })
            ->when(isset($condition['id_kualitas']), function ($q) use ($condition) {
                $q->where('log.id_kualitas', $condition['id_kualitas']);
            })
            ->when(isset($condition['id_beam']), function ($q) use ($condition) {
                $q->where('log.id_beam', $condition['id_beam']);
            })
            ->when(isset($condition['id_songket']), function ($q) use ($condition) {
                $q->where('log.id_songket', $condition['id_songket']);
            })
            ->when(isset($condition['id_mesin']), function ($q) use ($condition) {
                $q->where('log.id_mesin', $condition['id_mesin']);
            })
            ->when(isset($condition['id_satuan_1']), function ($q) use ($condition) {
                $q->where('log.id_satuan_1', $condition['id_satuan_1']);
            })
            ->when(isset($condition['id_satuan_2']), function ($q) use ($condition) {
                $q->where('log.id_satuan_2', $condition['id_satuan_2']);
            })
            ->when(isset($condition['code_custom']), function ($q) use ($condition) {
                $q->whereRaw("{$condition['code_custom']}");
            }, function ($q) use ($condition) {
                $q->when(isset($condition['code']), function ($q) use ($condition) {
                    if (is_array($condition['code'])) {
                        $q->whereIn('log.code', $condition['code']);
                    } else {
                        $q->where('log.code', $condition['code']);
                    }
                });
            })
            ->whereNull('log.deleted_at')
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('gudang.name', $parts)
                        ->orWhereIn('barang.name', $parts)
                        ->orWhereIn('warna.alias', $parts)
                        ->orWhereIn('motif.alias', $parts)
                        ->orWhereIn('grade.grade', $parts)
                        ->orWhereIn('kualitas.kode', $parts)
                        ->orWhereIn('mesin.name', $parts)
                        ->orWhereIn('nomor_kikw.name', $parts)
                        ->orWhereIn('nomor_kiks.name', $parts)
                        ->orWhereIn('nomor_beam.name', $parts)
                        ->orWhereIn(DB::raw("TO_CHAR(log.tanggal_potong, 'DD-MM-YYYY')"), $parts);
                });
            })
            ->groupByRaw('
                log.id_gudang,
                gudang.name,
                log.id_barang,
                barang.name,
                log.id_warna,
                warna.alias,
                log.id_motif,
                motif.alias,
                log.id_grade,
                grade.grade,
                log.id_kualitas,
                kualitas.kode,
                log.id_beam,
                nomor_kikw.name,
                nomor_beam.name,
                log.id_songket,
                nomor_kiks.name,
                log.id_mesin,
                mesin.name,
                log.id_satuan_1,
                satuan_1.name,
                log.id_satuan_2,
                satuan_2.name,
                log.code,
                log.tanggal_potong
            ')
            ->havingRaw('
                (SUM(COALESCE(volume_masuk_1, 0)) - SUM(COALESCE(volume_keluar_1, 0)) != 0) OR
                (SUM(COALESCE(volume_masuk_2, 0)) - SUM(COALESCE(volume_keluar_2, 0)) != 0)
            ')
            // ->toSql();
            ->paginate(5);
    } else if ($from == 'detail') {
        $temp = DB::table("$tableName")
            ->selectRaw("
                id_parent,
                id_gudang,
                id_barang,
                id_warna,
                id_motif,
                id_beam,
                id_songket,
                id_mesin,
                id_satuan_1,
                tanggal_potong,
                TO_CHAR(tanggal_potong, 'DD-MM-YYYY') tanggal_potong_text,
                SUM(COALESCE(volume_1,0)) volume_1
            ")->when(isset($condition['code']), function ($q) use ($condition) {
                $q->whereIn('code', $condition['code_terima']);
            })->whereNull('deleted_at')->groupBy('id_parent', 'id_gudang', 'id_barang', 'id_warna', 'id_motif', 'id_beam', 'id_songket', 'id_mesin', 'id_satuan_1', 'tanggal_potong');
        $data = DB::table("$tableName as kirim")
            ->whereNull('kirim.deleted_at')
            ->leftJoinSub($temp, 'terima', function ($q) {
                return $q->on('kirim.id', 'terima.id_parent');
            })
            ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'kirim.id_gudang')
            ->leftJoin('tbl_barang as barang', 'barang.id', 'kirim.id_barang')
            ->leftJoin('tbl_warna as warna', 'warna.id', 'kirim.id_warna')
            ->leftJoin('tbl_motif as motif', 'motif.id', 'kirim.id_motif')
            ->leftJoin('tbl_beam as beam', 'beam.id', 'kirim.id_beam')
            ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
            ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
            ->leftJoin('tbl_beam as songket', 'songket.id', 'kirim.id_songket')
            ->leftJoin('tbl_nomor_kikw as nomor_kiks', 'nomor_kiks.id', 'songket.id_nomor_kikw')
            ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'kirim.id_mesin')
            ->leftJoin('tbl_kualitas as grade', 'grade.id', 'kirim.id_grade')
            ->selectRaw('
                TO_CHAR(kirim.tanggal::date, \'dd/mm/yyyy\') as tanggal,
                kirim.id_inspect_retur,
                kirim.id,
                kirim.id_gudang,
                gudang.name nama_gudang,
                kirim.id_barang,
                barang.name nama_barang,
                kirim.id_warna,
                warna.alias nama_warna,
                kirim.id_motif,
                motif.alias nama_motif,
                kirim.id_grade,
                grade.grade nama_grade,
                grade.alias alias_grade,
                kirim.id_beam,
                nomor_kikw.name nomor_kikw,
                nomor_beam.name nomor_beam,
                kirim.id_songket,
                nomor_kiks.name nomor_kiks,
                kirim.id_mesin,
                mesin.name nama_mesin,
                kirim.id_satuan_1,
                kirim.tanggal_potong,
                TO_CHAR(kirim.tanggal_potong, \'DD-MM-YYYY\') tanggal_potong_text,
                SUM(COALESCE(kirim.volume_1,0)) - SUM(COALESCE(terima.volume_1,0)) volume_1
            ')
            ->when(isset($condition['proses']), function ($q) use ($condition) {
                $q->where("kirim.id_{$condition['proses']}", $condition['id_spk']);
            })
            ->when(isset($condition['code_kirim']), function ($q) use ($condition) {
                $q->where('kirim.code', $condition['code_kirim']);
            })->when(isset($condition['id_gudang']), function ($q) use ($condition) {
                $q->where('kirim.id_gudang', $condition['id_gudang']);
            })
            ->when(!empty($parts), function ($q) use ($parts) {
                $q->where(function ($q) use ($parts) {
                    $q->whereIn('gudang.name', $parts)
                        ->orWhereIn('barang.name', $parts)
                        ->orWhereIn('warna.alias', $parts)
                        ->orWhereIn('motif.alias', $parts)
                        ->orWhereIn('mesin.name', $parts)
                        ->orWhereIn('nomor_kikw.name', $parts)
                        ->orWhereIn('nomor_kiks.name', $parts)
                        ->orWhereIn(DB::raw("TO_CHAR(kirim.tanggal_potong, 'DD-MM-YYYY')"), $parts);
                });
            })
            ->groupByRaw('TO_CHAR(kirim.tanggal::date, \'dd/mm/yyyy\'), kirim.id_inspect_retur, kirim.id, gudang.name, barang.name, warna.alias, motif.alias, nomor_kikw.name, nomor_kiks.name, nomor_beam.name, mesin.name, grade.grade, grade.alias, kirim.tanggal_potong')
            ->havingRaw('
                SUM(COALESCE(kirim.volume_1,0)) - SUM(COALESCE(terima.volume_1,0)) != 0
            ')
            ->paginate(5);
    }

    return $data;
}

function getBarangInspecting($request, $proses)
{
    $term = $request->input('term');
    $parts = ($term == '') ? [] : explode(" | ", $term);
    $parent = str_replace('inspect_', '', $proses);
    $tableInspect = "tbl_{$proses}_detail";
    $tableDetail = "tbl_{$parent}_detail";
    $tableParent = "tbl_{$parent}";
    $code = getCode($proses)['input'];

    $gudang = $request->id_gudang ?? 0;
    $inspect = DB::table("$tableInspect as inspect")
        ->selectRaw("
            inspect.id_{$parent},
            coalesce(inspect.id_beam,0) id_beam,
            coalesce(inspect.id_songket,0) id_songket,
            coalesce(inspect.id_mesin,0) id_mesin,
            coalesce(inspect.id_barang,0) id_barang,
            coalesce(inspect.id_warna,0) id_warna,
            coalesce(inspect.id_gudang,0) id_gudang,
            coalesce(inspect.id_motif,0) id_motif,
            coalesce(inspect.tanggal_potong,'1997-10-23') tanggal_potong,
            coalesce(log.id_grade,0) id_grade,
            sum(coalesce(volume_1,0)) volume_1
        ")
        ->where('inspect.id_gudang', $gudang)
        ->whereNull('inspect.deleted_at')
        ->leftJoin('log_stok_penerimaan as log', 'log.id', 'inspect.id_log_stok_penerimaan_keluar')
        ->groupByRaw("inspect.id_{$parent}, inspect.id_beam, inspect.id_songket, inspect.id_mesin, inspect.id_barang, inspect.id_warna, inspect.id_gudang, inspect.id_motif, inspect.tanggal_potong, log.id_grade");

    $detail = DB::table("$tableDetail as detail")
        ->selectRaw("
            detail.id_{$parent},
            coalesce(detail.id_beam,0) id_beam,
            coalesce(detail.id_songket,0) id_songket,
            coalesce(detail.id_mesin,0) id_mesin,
            coalesce(detail.id_barang,0) id_barang,
            coalesce(detail.id_warna,0) id_warna,
            coalesce(detail.id_gudang,0) id_gudang,
            coalesce(detail.id_motif,0) id_motif,
            coalesce(detail.tanggal_potong,'1997-10-23') tanggal_potong,
            coalesce(detail.id_grade,0) id_grade,
            sum(coalesce(volume_1,0)) volume_1
        ")
        ->where('detail.id_gudang', $gudang)
        ->where('detail.code', $code)
        ->whereNull('detail.deleted_at')
        ->groupByRaw("detail.id_{$parent}, detail.id_beam, detail.id_songket, detail.id_mesin, detail.id_barang, detail.id_warna, detail.id_gudang, detail.id_motif, detail.tanggal_potong, detail.id_grade");

    $data = DB::table(DB::raw("({$detail->toSql()}) as detail"))
        ->mergeBindings($detail)
        ->mergeBindings($inspect)
        ->leftJoin(DB::raw("({$inspect->toSql()}) as inspect"), function ($join)  use ($parent) {
            $join->on("detail.id_{$parent}", "inspect.id_{$parent}")
                ->on(DB::raw('COALESCE(detail.id_beam,0)'), DB::raw('COALESCE(inspect.id_beam,0)'))
                ->on(DB::raw('COALESCE(detail.id_songket,0)'), DB::raw('COALESCE(inspect.id_songket,0)'))
                ->on(DB::raw('COALESCE(detail.id_mesin,0)'), DB::raw('COALESCE(inspect.id_mesin,0)'))
                ->on(DB::raw('COALESCE(detail.id_barang,0)'), DB::raw('COALESCE(inspect.id_barang,0)'))
                ->on(DB::raw('COALESCE(detail.id_warna,0)'), DB::raw('COALESCE(inspect.id_warna,0)'))
                ->on(DB::raw('COALESCE(detail.id_gudang,0)'), DB::raw('COALESCE(inspect.id_gudang,0)'))
                ->on(DB::raw('COALESCE(detail.id_motif,0)'), DB::raw('COALESCE(inspect.id_motif,0)'))
                ->on(DB::raw("COALESCE(detail.tanggal_potong,'1997-10-23')"), DB::raw("COALESCE(inspect.tanggal_potong,'1997-10-23')"))
                ->on(DB::raw('COALESCE(detail.id_grade,0)'), DB::raw('COALESCE(inspect.id_grade,0)'));
        })
        ->leftJoin("{$tableParent} as parent", 'parent.id', "detail.id_{$parent}")
        ->leftJoin('tbl_beam as beam', 'beam.id', 'detail.id_beam')
        ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
        ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
        ->leftJoin('tbl_beam as songket', 'songket.id', 'detail.id_songket')
        ->leftJoin('tbl_nomor_kikw as nomor_kiks', 'nomor_kiks.id', 'songket.id_nomor_kikw')
        ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'detail.id_mesin')
        ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'detail.id_gudang')
        ->leftJoin('tbl_barang as barang', 'barang.id', 'detail.id_barang')
        ->leftJoin('tbl_warna as warna', 'warna.id', 'detail.id_warna')
        ->leftJoin('tbl_motif as motif', 'motif.id', 'detail.id_motif')
        ->leftJoin('tbl_kualitas as grade', 'grade.id', 'detail.id_grade')
        ->selectRaw("
            CASE WHEN detail.id_{$parent} != 0 THEN detail.id_{$parent} ELSE NULL END id_{$parent},
            parent.nomor nomor,
            CASE WHEN detail.id_mesin != 0 THEN detail.id_mesin ELSE NULL END id_mesin,
            mesin.name nama_mesin,
            CASE WHEN detail.id_gudang != 0 THEN detail.id_gudang ELSE NULL END id_gudang,
            gudang.name nama_gudang,
            CASE WHEN detail.id_barang != 0 THEN detail.id_barang ELSE NULL END id_barang,
            barang.name nama_barang,
            CASE WHEN detail.id_warna != 0 THEN detail.id_warna ELSE NULL END id_warna,
            warna.alias nama_warna,
            CASE WHEN detail.id_motif != 0 THEN detail.id_motif ELSE NULL END id_motif,
            motif.alias nama_motif,
            CASE WHEN detail.tanggal_potong != '1997-10-23' THEN detail.tanggal_potong ELSE NULL END tanggal_potong,
            CASE WHEN detail.tanggal_potong != '1997-10-23' THEN TO_CHAR(detail.tanggal_potong,'DD-MM-YYYY') ELSE NULL END tanggal_potong_text,
            CASE WHEN detail.id_grade != 0 THEN detail.id_grade ELSE NULL END id_grade,
            grade.grade nama_grade,
            grade.alias alias_grade,
            CASE WHEN detail.id_beam != 0 THEN detail.id_beam ELSE NULL END id_beam,
            CASE WHEN detail.id_songket != 0 THEN detail.id_songket ELSE NULL END id_songket,
            nomor_kikw.name nomor_kikw,
            nomor_kiks.name nomor_kiks,
            nomor_beam.name nomor_beam
        ")
        ->groupByRaw("
            detail.id_{$parent},
            parent.nomor,
            detail.id_mesin,
            mesin.name,
            detail.id_gudang,
            gudang.name,
            detail.id_barang,
            barang.name,
            detail.id_warna,
            warna.alias,
            detail.id_motif,
            motif.alias,
            detail.tanggal_potong,
            detail.id_grade,
            grade.grade,
            grade.alias,
            detail.id_beam,
            detail.id_songket,
            nomor_kikw.name,
            nomor_kiks.name,
            nomor_beam.name
        ")
        ->when(!empty($parts), function ($q) use ($parts) {
            $q->where(function ($q) use ($parts) {
                $q->whereIn('gudang.name', $parts)
                    ->orWhereIn('barang.name', $parts)
                    ->orWhereIn('warna.alias', $parts)
                    ->orWhereIn('motif.alias', $parts)
                    ->orWhereIn('mesin.name', $parts)
                    ->orWhereIn('nomor_kikw.name', $parts)
                    ->orWhereIn('nomor_kiks.name', $parts)
                    ->orWhereIn('nomor_beam.name', $parts)
                    ->orWhereIn('grade.grade', $parts)
                    ->orWhereIn(DB::raw("TO_CHAR(detail.tanggal_potong, 'DD-MM-YYYY')"), $parts);
            });
        })
        ->havingRaw('
            SUM(COALESCE(detail.volume_1,0)) - SUM(COALESCE(inspect.volume_1,0)) != 0
        ')->paginate(5);
    return $data;
}

function getDataJasaDalam($request, $prosesName)
{
    $data = DB::table("tbl_{$prosesName}_detail as detail")
        ->leftJoin('tbl_barang as barang', 'barang.id', 'detail.id_barang')
        ->leftJoin('tbl_warna as warna', 'warna.id', 'detail.id_warna')
        ->leftJoin('tbl_motif as motif', 'motif.id', 'detail.id_motif')
        ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'detail.id_gudang')
        ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'detail.id_mesin')
        ->leftJoin('tbl_beam as beam', 'beam.id', 'detail.id_beam')
        ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
        ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
        ->leftJoin('tbl_beam as songket', 'songket.id', 'detail.id_songket')
        ->leftJoin('tbl_nomor_kikw as nomor_kiks', 'nomor_kiks.id', 'songket.id_nomor_kikw')
        ->leftJoin('tbl_kualitas as grade', 'grade.id', 'detail.id_grade')
        ->leftJoin('log_stok_penerimaan as log', 'log.id', 'detail.id_log_stok_penerimaan_keluar')
        ->leftJoin('tbl_kualitas as grade_awal', 'grade_awal.id', 'log.id_grade')
        ->selectRaw("
            detail.id,
            detail.tanggal,
            detail.id_barang,
            barang.name nama_barang,
            detail.id_gudang,
            gudang.name nama_gudang,
            detail.id_warna,
            warna.alias nama_warna,
            detail.id_motif,
            motif.alias nama_motif,
            detail.id_beam,
            nomor_kikw.name nomor_kikw,
            nomor_beam.name nomor_beam,
            detail.id_songket,
            nomor_kiks.name nomor_kiks,
            detail.id_mesin,
            mesin.name nama_mesin,
            detail.id_grade,
            grade.grade nama_grade,
            grade.alias alias_grade,
            log.id_grade id_grade_awal,
            grade_awal.grade nama_grade_awal,
            detail.volume_1,
            log.code
        ")
        ->whereNull('detail.deleted_at')
        ->where('detail.id', $request->id)->first();
    return $data;
}

function getDataJasaLuar($request, $prosesName)
{
    if ($request->mode == 'parent') {
        $data = DB::table("tbl_{$prosesName} as parent")
            ->selectRaw('
                parent.*,
                supplier.name nama_supplier
            ')
            ->leftJoin('tbl_supplier as supplier', 'supplier.id', 'parent.id_supplier')
            ->where('parent.id', $request->id)
            ->whereNull('parent.deleted_at')
            ->first();
    } else if ($request->mode == 'detail') {
        $data = DB::table("tbl_{$prosesName}_detail as detail")
            ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'detail.id_mesin')
            ->leftJoin('tbl_beam as beam', 'beam.id', 'detail.id_beam')
            ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
            ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
            ->leftJoin('tbl_barang as barang', 'barang.id', 'detail.id_barang')
            ->leftJoin('tbl_warna as warna', 'warna.id', 'detail.id_warna')
            ->leftJoin('tbl_kualitas as grade', 'grade.id', 'detail.id_grade')
            ->leftJoin('tbl_motif as motif', 'motif.id', 'detail.id_motif')
            ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'detail.id_gudang')
            ->leftJoin("tbl_{$prosesName}_detail as parent", 'parent.id', "detail.id_parent")
            ->leftJoin('tbl_kualitas as grade_awal', 'grade_awal.id', 'parent.id_grade')
            ->selectRaw("
                detail.id,
                detail.id_{$prosesName},
                detail.tanggal,
                detail.tanggal_potong,
                TO_CHAR(detail.tanggal_potong, 'DD-MM-YYYY') tanggal_potong_text,
                detail.id_mesin,
                mesin.name nama_mesin,
                detail.id_barang,
                barang.name nama_barang,
                detail.id_warna,
                warna.alias nama_warna,
                detail.id_motif,
                motif.alias nama_motif,
                detail.id_beam,
                nomor_kikw.name nomor_kikw,
                nomor_beam.name nomor_beam,
                detail.id_gudang,
                gudang.name nama_gudang,
                detail.id_grade,
                grade.grade nama_grade,
                grade.alias alias_grade,
                parent.id_grade id_grade_awal,
                grade_awal.grade nama_grade_awal,
                grade_awal.alias alias_grade_awal,
                detail.id_parent,
                detail.volume_1,
                detail.id_satuan_1,
                detail.volume_2,
                detail.id_satuan_2,
                detail.volume_1,
                detail.id_log_stok_penerimaan
            ")
            ->where('detail.id', $request->id)
            ->whereNull('detail.deleted_at')->first();
    }

    return $data;
}

function getDataInspecting($request, $prosesName)
{
    $parent = str_replace('inspect_', '', $prosesName);
    $tableInspect = "tbl_{$prosesName}_detail";
    $tableParent = "tbl_{$parent}";
    $data = DB::table("{$tableInspect} as inspect")
        ->leftJoin("{$tableParent} as parent", 'parent.id', "inspect.id_{$parent}")
        ->leftJoin('tbl_beam as beam', 'beam.id', 'inspect.id_beam')
        ->leftJoin('tbl_nomor_kikw as nomor_kikw', 'nomor_kikw.id', 'beam.id_nomor_kikw')
        ->leftJoin('tbl_nomor_beam as nomor_beam', 'nomor_beam.id', 'beam.id_nomor_beam')
        ->leftJoin('tbl_mesin as mesin', 'mesin.id', 'inspect.id_mesin')
        ->leftJoin('tbl_barang as barang', 'barang.id', 'inspect.id_barang')
        ->leftJoin('tbl_warna as warna', 'warna.id', 'inspect.id_warna')
        ->leftJoin('tbl_motif as motif', 'motif.id', 'inspect.id_motif')
        ->leftJoin('tbl_kualitas as grade', 'grade.id', 'inspect.id_grade')
        ->leftJoin('tbl_gudang as gudang', 'gudang.id', 'inspect.id_gudang')
        ->leftJoin('log_stok_penerimaan as log', 'log.id', 'inspect.id_log_stok_penerimaan_keluar')
        ->leftJoin('tbl_kualitas as grade_awal', 'grade_awal.id', 'log.id_grade')
        ->selectRaw("
            inspect.id,
            inspect.tanggal,
            parent.nomor,
            inspect.id_gudang,
            gudang.name nama_gudang,
            inspect.id_{$parent},
            inspect.id_beam,
            nomor_kikw.name nomor_kikw,
            nomor_beam.name nomor_beam,
            inspect.id_mesin,
            mesin.name nama_mesin,
            inspect.id_barang,
            barang.name nama_barang,
            inspect.id_warna,
            warna.alias nama_warna,
            inspect.id_motif,
            motif.alias nama_motif,
            inspect.id_grade,
            grade.grade nama_grade,
            grade.alias alias_grade,
            log.id_grade id_grade_awal,
            grade_awal.grade nama_grade_awal,
            grade_awal.alias nama_alias_awal,
            inspect.volume_1
        ")
        ->where('inspect.id', $request->id)->first();
    $data->kualitas = DB::table("tbl_{$prosesName}_kualitas as data")
        ->leftJoin('tbl_mapping_kualitas as kualitas', 'kualitas.id', 'data.id_kualitas')
        ->selectRaw('data.id_kualitas, kualitas.name nama_kualitas,kualitas.kode kode_kualitas')
        ->where("id_{$prosesName}_detail", $request->id)->whereNull('data.deleted_at')->get();
    return $data;
}

function getStokJasaLuar($request, $proses)
{
    $tipe = $request->tipe;
    $table = "tbl_{$proses}_detail";
    $code = getCode($proses)['input'];
    if ($tipe == 'input' || $tipe == null) {
        $data = DB::table('log_stok_penerimaan')
            ->selectRaw('
                SUM(COALESCE(volume_masuk_1,0)) - SUM(COALESCE(volume_keluar_1,0)) as stok_1,
                SUM(COALESCE(volume_masuk_2,0)) - SUM(COALESCE(volume_keluar_2,0)) as stok_2
            ')->where([
                ['id_mesin', $request->id_mesin],
                ['id_barang', $request->id_barang],
                ['id_warna', $request->id_warna],
                ['id_gudang', $request->id_gudang],
                ['id_motif', $request->id_motif],
                ['id_beam', $request->id_beam],
                ['id_songket', $request->id_songket],
                ['tanggal_potong', $request->tanggal_potong],
                ['deleted_at', null]
            ])
            ->when(isset($request['code']), function ($q) use ($request) {
                return $q->where('code', $request['code']);
            }, function ($q) use ($code) {
                return $q->where('code', $code);
            })
            ->when(isset($request['id_grade']), function ($q) use ($request) {
                $q->when(isset($request['id_grade_awal']), function ($q) use ($request) {
                    return $q->where('id_grade', $request['id_grade_awal']);
                }, function ($q) use ($request) {
                    return $q->where('id_grade', $request['id_grade']);
                });
            })
            ->first();
    } else {
        $detail = DB::table("$table")->where('id', $request->id)->first();
        $id_parent = $detail->id_parent;
        $findId = (!$id_parent) ? $detail->id : $id_parent;
        $temp = DB::table("$table")
            ->selectRaw('
            id_parent,
            sum(volume_1) volume_1,
            sum(volume_2) volume_2
        ')->where('id_parent', $findId)->whereNotNull('id_parent')->whereNull('deleted_at')->groupBy('id_parent');
        $data = DB::table("$table as kirim")
            ->where('kirim.id', $findId)
            ->whereNull('kirim.deleted_at')
            ->leftJoinSub($temp, 'terima', function ($q) {
                return $q->on('kirim.id', 'terima.id_parent');
            })
            ->selectRaw('
                SUM(COALESCE(kirim.volume_1,0)) - SUM(COALESCE(terima.volume_1,0)) stok_1,
                SUM(COALESCE(kirim.volume_2,0)) - SUM(COALESCE(terima.volume_2,0)) stok_2
            ')
            ->first();
    }

    if ($data) {
        $result = $data;
    } else {
        $result = [
            'stok_1' => 0,
            'stok_2' => 0,
        ];
    }
    return $result;
}

function getStokInspecting($request, $proses)
{
    $table = "tbl_{$proses}_detail";
    $parent = str_replace('inspect_', '', $proses);
    $code = getCode($proses)['input'];

    $sub = DB::table("$table")->selectRaw("
                {$table}.id_{$parent},
                coalesce({$table}.id_beam,0) id_beam,
                coalesce({$table}.id_songket,0) id_songket,
                coalesce({$table}.id_mesin,0) id_mesin,
                {$table}.id_barang,
                {$table}.id_warna,
                {$table}.id_gudang,
                {$table}.id_motif,
                {$table}.tanggal_potong,
                log.id_grade id_grade_awal,
                SUM(coalesce(volume_1,0)) volume_1
            ")
        ->leftJoin("log_stok_penerimaan as log", "log.id", "{$table}.id_log_stok_penerimaan_keluar")
        ->when(isset($request['id_parent']), function ($query) use ($request, $table, $parent) {
            return $query->where("{$table}.id_{$parent}", $request['id_parent']);
        }, function ($query) use ($table, $parent) {
            return $query->whereNull("{$table}.id_{$parent}");
        })->when(isset($request['id_beam']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_beam", $request['id_beam']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_beam");
        })->when(isset($request['id_songket']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_songket", $request['id_songket']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_songket");
        })->when(isset($request['id_mesin']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_mesin", $request['id_mesin']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_mesin");
        })->when(isset($request['id_barang']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_barang", $request['id_barang']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_barang");
        })->when(isset($request['id_gudang']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_gudang", $request['id_gudang']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_gudang");
        })->when(isset($request['id_motif']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_motif", $request['id_motif']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_motif");
        })->when(isset($request['id_warna']), function ($query) use ($request, $table) {
            return $query->where("{$table}.id_warna", $request['id_warna']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.id_warna");
        })->when(isset($request['tanggal_potong']), function ($query) use ($request, $table) {
            return $query->where("{$table}.tanggal_potong", $request['tanggal_potong']);
        }, function ($query) use ($table) {
            return $query->whereNull("{$table}.tanggal_potong");
        })->when(isset($request['id_grade']), function ($query) use ($request) {
            $query->when(isset($request['id_grade_awal']), function ($q) use ($request) {
                return $q->where("log.id_grade", $request['id_grade_awal']);
            }, function ($q) use ($request) {
                return $q->where("log.id_grade", $request['id_grade']);
            });
            // return $query->where("log.id_grade", $request['id_grade']);
        }, function ($query) {
            return $query->whereNull("log.id_grade");
        })
        ->whereNull("{$table}.deleted_at")
        ->groupByRaw("{$table}.id_{$parent}, {$table}.id_beam, {$table}.id_songket, {$table}.id_mesin, {$table}.id_barang, {$table}.id_gudang, {$table}.id_warna, {$table}.id_motif, {$table}.tanggal_potong, log.id_grade");
    $table2 = "tbl_{$parent}_detail";
    $sub2 = DB::table("$table2")->selectRaw("
                    {$table2}.id_{$parent},
                    coalesce({$table2}.id_beam,0) id_beam,
                    coalesce({$table2}.id_songket,0) id_songket,
                    coalesce({$table2}.id_mesin,0) id_mesin,
                    {$table2}.id_barang,
                    {$table2}.id_warna,
                    {$table2}.id_gudang,
                    {$table2}.id_motif,
                    {$table2}.tanggal_potong,
                    {$table2}.id_grade,
                    code,
                    SUM(coalesce({$table2}.volume_1,0)) volume_1
                ")
        ->when(isset($request['id_parent']), function ($query) use ($request, $table2, $parent) {
            return $query->where("{$table2}.id_{$parent}", $request['id_parent']);
        }, function ($query) use ($table2, $parent) {
            return $query->whereNull("{$table2}.id_{$parent}");
        })->when(isset($request['id_beam']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_beam", $request['id_beam']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_beam");
        })->when(isset($request['id_songket']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_songket", $request['id_songket']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_songket");
        })->when(isset($request['id_mesin']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_mesin", $request['id_mesin']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_mesin");
        })->when(isset($request['id_barang']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_barang", $request['id_barang']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_barang");
        })->when(isset($request['id_gudang']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_gudang", $request['id_gudang']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_gudang");
        })->when(isset($request['id_motif']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_motif", $request['id_motif']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_motif");
        })->when(isset($request['id_warna']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.id_warna", $request['id_warna']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_warna");
        })->when(isset($request['tanggal_potong']), function ($query) use ($request, $table2) {
            return $query->where("{$table2}.tanggal_potong", $request['tanggal_potong']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.tanggal_potong");
        })->when(isset($request['id_grade']), function ($query) use ($request, $table2) {
            $query->when(isset($request['id_grade_awal']), function ($q) use ($request, $table2) {
                return $q->where("{$table2}.id_grade", $request['id_grade_awal']);
            }, function ($q) use ($request, $table2) {
                return $q->where("{$table2}.id_grade", $request['id_grade']);
            });
            // return $query->where("{$table2}.id_grade", $request['id_grade']);
        }, function ($query) use ($table2) {
            return $query->whereNull("{$table2}.id_grade");
        })->where("code", $code)
        ->whereNull("{$table2}.deleted_at")
        ->groupByRaw("{$table2}.id_{$parent}, {$table2}.id_beam, {$table2}.id_songket, {$table2}.id_mesin, {$table2}.id_barang, {$table2}.id_warna, {$table2}.id_gudang, {$table2}.id_motif, {$table2}.tanggal_potong, {$table2}.id_grade, code");
    $data = DB::table(DB::raw("({$sub2->toSql()}) as sub2"))
        ->mergeBindings($sub2)
        ->mergeBindings($sub)
        ->leftJoin(DB::raw("({$sub->toSql()}) as sub"), function ($join)  use ($parent) {
            $join->on("sub.id_{$parent}", "sub2.id_{$parent}")
                ->on(DB::raw('COALESCE(sub.id_beam,0)'), DB::raw('COALESCE(sub2.id_beam,0)'))
                ->on(DB::raw('COALESCE(sub.id_songket,0)'), DB::raw('COALESCE(sub2.id_songket,0)'))
                ->on(DB::raw('COALESCE(sub.id_mesin,0)'), DB::raw('COALESCE(sub2.id_mesin,0)'))
                ->on(DB::raw('COALESCE(sub.id_barang,0)'), DB::raw('COALESCE(sub2.id_barang,0)'))
                ->on(DB::raw('COALESCE(sub.id_warna,0)'), DB::raw('COALESCE(sub2.id_warna,0)'))
                ->on(DB::raw('COALESCE(sub.id_gudang,0)'), DB::raw('COALESCE(sub2.id_gudang,0)'))
                ->on(DB::raw('COALESCE(sub.id_motif,0)'), DB::raw('COALESCE(sub2.id_motif,0)'))
                ->on(DB::raw("COALESCE(sub.tanggal_potong,'1997-10-23')"), DB::raw("COALESCE(sub2.tanggal_potong,'1997-10-23')"))
                ->on(DB::raw('COALESCE(sub.id_grade_awal,0)'), DB::raw('COALESCE(sub2.id_grade,0)'));
        })
        ->selectRaw('SUM(coalesce(sub2.volume_1,0)) - SUM(coalesce(sub.volume_1,0)) as stok_1')
        ->first();


    if ($data) {
        $result = $data;
    } else {
        $result = [
            'stok_1' => 0,
        ];
    }

    return $result;
}


function getGudang($atribut = [])
{
    if (!isset($atribut['table'])) {
        $data = DB::table('log_stok_penerimaan as data')->selectRaw("
                data.id_gudang,
                gudang.name nama_gudang
            ")->leftJoin('tbl_gudang as gudang', 'gudang.id', 'data.id_gudang')
            ->when(isset($atribut['code_custom']), function ($q) use ($atribut) {
                return $q->whereRaw("{$atribut['code_custom']}");
            }, function ($q) use ($atribut) {
                $q->when(isset($atribut['code']), function ($q) use ($atribut) {
                    if (is_array($atribut['code'])) {
                        $q->whereIn('data.code', $atribut['code']);
                    } else {
                        $q->where('data.code', $atribut['code']);
                    }
                });
            })
            // ->when(isset($atribut['code']), function ($q) use ($atribut) {
            //     $q->where('data.code', $atribut['code']);
            // })
            ->whereNull('data.deleted_at')
            ->groupBy('data.id_gudang', 'gudang.name')
            ->when(isset($atribut['search']), function ($q) use ($atribut) {
                $q->where(function ($q) use ($atribut) {
                    $q->where('gudang.name', 'like', '%' . $atribut['search'] . '%');
                });
            })->get();
        return $data;
    } else {
        $data = DB::table("{$atribut['table']} as data")->selectRaw("
                data.id_gudang,
                gudang.name nama_gudang
            ")->leftJoin('tbl_gudang as gudang', 'gudang.id', 'data.id_gudang')
            ->when(isset($atribut['id_parent']), function ($q) use ($atribut) {
                $q->where("data.id_{$atribut['parent']}", $atribut['id_parent']);
            })
            ->whereNull('data.deleted_at')
            ->groupBy('data.id_gudang', 'gudang.name')
            ->when(isset($atribut['search']), function ($q) use ($atribut) {
                $q->where(function ($q) use ($atribut) {
                    $q->where('gudang.name', 'like', '%' . $atribut['search'] . '%');
                });
            })->get();
        return $data;
    }
}

function getCode($proses = null)
{
    if ($proses == 'dudulan') {
        $data = [
            'input' => 'BGIG',
            'output' => 'BGD'
        ];
    } else if ($proses == 'inspect_dudulan') {
        $data = [
            'input' => 'BGD',
            'output' => 'BGID'
        ];
    } else if ($proses == 'jahit_sambung') {
        $data = [
            'input' => 'BGF',
            'output' => 'JS'
        ];
    } else if ($proses == 'p1') {
        $data = [
            'input' => 'JS',
            'output' => 'P1'
        ];
    } else if ($proses == 'inspect_p1') {
        $data = [
            'input' => 'P1',
            'output' => 'IP1'
        ];
    } else if ($proses == 'finishing_cabut') {
        $data = [
            'input' => 'IP1',
            'output' => 'FC'
        ];
    } else if ($proses == 'inspect_finishing_cabut') {
        $data = [
            'input' => 'FC',
            'output' => 'IFC'
        ];
    } else if ($proses == 'jigger') {
        $data = [
            'input' => 'IFC',
            'output' => 'JCS'
        ];
    } else if ($proses == 'drying') {
        $data = [
            'input' => 'JCS',
            'output' => 'DR'
        ];
    } else if ($proses == 'p2') {
        $data = [
            'input' => 'DR',
            'output' => 'P2'
        ];
    } else if ($proses == 'inspect_p2') {
        $data = [
            'input' => 'P2',
            'output' => 'IP2'
        ];
    } else if ($proses == 'jahit_p2') {
        $data = [
            'input' => 'IP2',
            'output' => 'JP2'
        ];
    }

    return $data;
}
