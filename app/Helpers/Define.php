<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class Define
{
    public static function fetch($request, $constructor, $attributes = [], $filters = [], $appends = [], $rawColumn = ['aksi'])
    {
        $name = $request['name'] ?? '';
        $roles['isUserInformasi'] = Auth::user()->is('user informasi');
        $roles['isValidator'] = Auth::user()->is('validator');
        $roles['isAdministrator'] = Auth::user()->is('administrator');

        // $division = $request['division'] ?? '';
        $rawColumns = $rawColumn;
        // $draw = $request["draw"];
        // $order = $request['order'][0]['dir'] ?? 'desc';
        $isDetail = $request['isDetail'] ?? 'false';
        $isPengiriman = $request['isPengiriman'] ?? false;
        $limit = is_null($request["length"]) ? 15 : $request["length"];
        $offset = is_null($request["start"]) ? 0 : $request["start"];
        // dd($limit, $offset);
        $btnExtra = $request['btnExtras'] ?? [];
        $usedAction = $request['usedAction'] ?? [];
        $extraData = $request['extraData'] ?? [];
        $datas = $constructor;
        // $datasCount = $datas->get()->setAppends([])->count();
        $datasCount = $datas->get()->setAppends([])->count();
        $datasFiltered = $datas->limit($limit)->offset($offset);

        $exportDatatable = DataTables::of($datasFiltered);

        //ATTRIBUTES
        if (in_array('status', $attributes)) {
            $rawColumns[] = 'status';
            $exportDatatable->addColumn('status', function ($data) {
                $badge = ($data->status == 'AKTIF') ? 'badge-success' : 'badge-danger';
                $spanHtml = "<span class='badge {$badge}'>{$data->status}</span>";
                return $spanHtml;
            });
        }

        if (in_array('relGudang', $attributes)) {
            $exportDatatable->addColumn('gudang', function ($data) {
                return $data->relGudang()->value('name');
            });
        }

        if (in_array('relGroup', $attributes)) {
            $exportDatatable->addColumn('group', function ($data) {
                return $data->relGroup()->value('name');
            });
        }

        if (in_array('relBarang', $attributes)) {
            $exportDatatable->addColumn('nama_barang', function ($data) {
                return $data->relBarang()->value('name');
            });
        }

        if (in_array('relPenomoranBeamReturDetail', $attributes)) {
            $exportDatatable->addColumn('nama_barang_retur', function ($data) {
                return $data->relPenomoranBeamReturDetail->nama_barang;
            });
        }

        if (in_array('relWarna', $attributes)) {
            $exportDatatable->addColumn('warna', function ($data) {
                return $data->relWarna()->value('name');
            });
        }

        if (in_array('relMotif', $attributes)) {
            $exportDatatable->addColumn('motif', function ($data) {
                return $data->relMotif()->value('alias');
            });
        }

        if (in_array('relSatuan1', $attributes)) {
            $exportDatatable->addColumn('satuan_utama', function ($data) {
                return $data->relSatuan()->value('name');
            });
        }

        if (in_array('relSatuan', $attributes)) {
            $exportDatatable->addColumn('satuan_utama', function ($data) {
                return $data->relSatuan1()->value('name');
            });
            $exportDatatable->addColumn('satuan_pilihan', function ($data) {
                return $data->relSatuan2()->value('name');
            });
        }

        if (in_array('relSupplier', $attributes)) {
            $exportDatatable->addColumn('supplier', function ($data) {
                return $data->relSupplier()->value('name');
            });
        }

        if (in_array('relTipe', $attributes)) {
            $exportDatatable->addColumn('tipe', function ($data) {
                return $data->relTipe()->value('name');
            });
        }

        if (in_array('relGudangAsal', $attributes)) {
            $exportDatatable->addColumn('gudang_asal', function ($data) {
                return $data->relGudangAsal()->value('name');
            });
        }

        if (in_array('relGudangTujuan', $attributes)) {
            $exportDatatable->addColumn('gudang_tujuan', function ($data) {
                return $data->relGudangTujuan()->value('name');
            });
        }

        if (in_array('relGrade', $attributes)) {
            $exportDatatable->addColumn('grade', function ($data) {
                return $data->relGrade()->value('grade');
            });
            $exportDatatable->addColumn('kualitas', function ($data) {
                return $data->relKualitas()->value('name');
            });
        }

        if (in_array('tipeGudangAsal', $attributes)) {
            $rawColumns[] = 'gudang_asal';
            $exportDatatable->addColumn('gudang_asal', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relTipePengiriman()->first()->gudang_asal;
            });
        }

        if (in_array('tipeGudangTujuan', $attributes)) {
            $rawColumns[] = 'gudang_tujuan';
            $exportDatatable->addColumn('gudang_tujuan', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relTipePengiriman()->first()->gudang_tujuan;
            });
        }
        if (in_array('relKualitas', $attributes)) {
            $rawColumns[] = 'kualitas';
            $exportDatatable->addColumn('kualitas', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relKualitas()->first()->kualitas;
            });
        }

        if (in_array('relPekerja', $attributes)) {
            $exportDatatable->addColumn('pekerja', function ($data) {
                return $data->relPekerja()->value('name');
            });
        }

        if (in_array('relNoRegisterPekerja', $attributes)) {
            $exportDatatable->addColumn('no_register', function ($data) {
                return $data->relPekerja()->value('no_register');
            });
        }

        if (in_array('jumlahBeam', $attributes)) {
            $exportDatatable->addColumn('jumlah_pcs', function ($data) {
                return $data->relLogStokPenerimaanBL()->value('volume_masuk_2');
            });
        }

        if (in_array('noBeam', $attributes)) {
            $exportDatatable->addColumn('no_beam', function ($data) {
                return $data->throughNomorBeam()->value('name');
            });
        }

        if (in_array('noKikw', $attributes)) {
            $exportDatatable->addColumn('no_kikw', function ($data) {
                return $data->throughNomorKikw()->value('name');
            });
        }
        if (in_array('noKiks', $attributes)) {
            $exportDatatable->addColumn('no_kiks', function ($data) {
                return $data->throughNomorKiks()->value('name');
            });
        }
        if (in_array('tanggal_potong', $attributes)) {
            $exportDatatable->addColumn('tanggal_potong', function ($data) {
                return Date::format($data->tanggal_potong, 98);
            });
        }

        if (in_array('relMesin', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                return $data->relMesin()->value('name');
            });
        }

        if (in_array('relAbsensiMesin', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                return implode(', ', $data->relAbsensiMesin());
            });
        }

        if (in_array('relPekerjaMesin', $attributes)) {
            $exportDatatable->addColumn('pekerja_mesin', function ($data) {
                $namaPekerja = [];
                $data->relPekerjaMesin()->each(function ($item, $key) use (&$namaPekerja) {
                    $namaPekerja[] = $item->relPekerja()->value('name');
                });
                return implode(', ', $namaPekerja);
            });
        }

        if (in_array('relMesinTenun', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                $nomorMesin = [];
                $data->relMesinTenun()->each(function ($item, $key) use (&$nomorMesin) {
                    $nomorMesin[] = $item->relMesin()->value('name');
                });
                return implode(', ', $nomorMesin);
            });
        }

        if (in_array('tipe_beam', $attributes)) {
            $exportDatatable->addColumn('tipe_beam', function ($data) {
                return $data->relBeam()->value('tipe_beam');
            });
        }

        if (in_array('tipe_pra_tenun', $attributes)) {
            $exportDatatable->addColumn('tipe_pra_tenun', function ($data) {
                return $data->relBeam()->value('tipe_pra_tenun');
            });
        }

        if (in_array('is_sizing', $attributes)) {
            $exportDatatable->addColumn('is_sizing', function ($data) {
                return $data->relBeam()->value('is_sizing') ?? 'TIDAK';
            });
        }

        if (in_array('customTanggal', $attributes)) {
            $exportDatatable->addColumn('tanggal_custom', function ($data) {
                return Date::format($data->tanggal, 98);
            });
        }

        if (in_array('rel_parent.name', $attributes)) {
            $exportDatatable->addColumn('rel_parent.name', function ($data) {
                return $data->parent_id != null ? $data->relParent->name : '-';
            });
        }

        if (in_array('relProductionCode', $attributes)) {
            $exportDatatable->addColumn('production_code', function ($data) {
                return $data->relProductionCode()->value('alias');
            });
        }

        if (in_array('relNomorPengiriman', $attributes)) {
            $exportDatatable->addColumn('no_pengiriman', function ($data) {
                return $data->relPengirimanBarang()->value('nomor');
            });
        }

        if (in_array('relMotifArray', $attributes)) {
            $exportDatatable->addColumn('motif', function ($data) {
                $motif = DB::table('tbl_motif')->whereIn('id', json_decode($data->id_motif, true))->pluck('alias')->toArray();
                return implode(' | ', $motif);
            });
        }

        if (in_array('relStatusNomorBeam', $attributes)) {
            $exportDatatable->addColumn('status_beam', function ($data) {
                return (in_array(0, $data->relBeam()->pluck('finish')->toArray())) ? '<span class="badge badge-outline badge-warning">Masih Dipakai</span>' : '<span class="badge badge-outline badge-primary">Tersedia</span>';
            });
        }

        //END NON RAW COLUMNS
        //END ATTRIBUTES

        //FILTERS
        if (in_array('filterBarang', $filters)) {
            $exportDatatable->filterColumn('nama_barang', function ($query, $keyword) {
                $query->whereHas('relBarang', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterWarna', $filters)) {
            $exportDatatable->filterColumn('warna', function ($query, $keyword) {
                $query->whereHas('relWarna', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterNomorBeam', $filters)) {
            $exportDatatable->filterColumn('no_beam', function ($query, $keyword) {
                $query->whereHas('throughNomorBeam', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterKikw', $filters)) {
            $exportDatatable->filterColumn('no_kikw', function ($query, $keyword) {
                $query->whereHas('throughNomorKikw', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }
        if (in_array('filterKiks', $filters)) {
            $exportDatatable->filterColumn('no_kiks', function ($query, $keyword) {
                $query->whereHas('throughNomorKiks', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterPekerja', $filters)) {
            $exportDatatable->filterColumn('pekerja', function ($query, $keyword) {
                $query->whereHas('relPekerja', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterSupplier', $filters)) {
            $exportDatatable->filterColumn('supplier', function ($query, $keyword) {
                $query->whereHas('relSupplier', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterPenomoranBeamReturDetail', $filters)) {
            $exportDatatable->filterColumn('nama_barang_retur', function ($query, $keyword) {
                $query->whereHas('relPenomoranBeamReturDetail', function ($query) use ($keyword) {
                    return $query->whereHas('relBarang', function ($query) use ($keyword) {
                        return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                    });
                });
            });
        }
        //END FILTERS

        // if(!empty($appends)){
        //     foreach ($appends as $append) {
        //         $exportDatatable->addColumn($append, function ($data) use($append) {
        //             return $data->$append;
        //         });
        //     }
        // }

        $exportDatatable->addColumn('aksi', function ($data) use ($btnExtra, $isDetail, $usedAction, $extraData, $isPengiriman, $name, $roles) {

            if ($roles['isUserInformasi']) return '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Detail">
                <i class="icon md-lock-outline"></i>
            </a>';

            $btnExtra = str_replace('%id', $data->id, implode('', $btnExtra));

            $extraDataAppend = "";
            if (!isset($extraData['row'])) {
                if (count($extraData) > 0) {
                    foreach ($extraData as $key => $value) {
                        $extraDataAppend .= "data-{$key}='{$value}'";
                    }
                }
            } else {
                if (count($extraData['row']) > 0) {
                    foreach ($extraData['row'] as $row) {
                        $value = $data->$row;
                        $extraDataAppend .= "data-{$row}='{$value}'";
                    }
                }
            }

            $add = ($isPengiriman == 1) ? 'data-id_tipe_pengiriman="' . $data->id_tipe_pengiriman . '"' : '';

            $btnDetail = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" ' . $extraDataAppend . ' data-original-title="Detail" onclick="goToDetail(' . $data->id . ', $(this));" ' . $add . '>
                <i class="icon md-menu" aria-hidden="true"></i>
            </a>';
            $btnEdit = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" ' . $extraDataAppend . ' onclick="editForm(' . $data->id . ', \'' . $isDetail . '\', $(this));" data-original-title="Edit">
                <i class="icon md-edit" aria-hidden="true"></i>
            </a>';
            $btnDelete = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" onclick="deleteForm(' . $data->id . ', \'' . $isDetail . '\', $(this));" data-original-title="Delete">
                <i class="icon md-delete" aria-hidden="true"></i>
            </a>';

            $isValidate = isset($data->validated_at) ? $data->validated_at != null : false;
            if ($roles['isValidator']) {
                $btnValidation = '<button type="button" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                    data-model="' . $name . '" data-id="' . $data->id . '" data-state="validate" data-is-show="parent"
                    onclick="validateForm($(this));"><i class="icon md-check mr-2"></i></button>';
                $buttons = '';
                if ($usedAction != 'NOUSED') {
                    if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                }
                if ($isDetail != 'true' && !$isValidate) $buttons .= $btnValidation;
                return $buttons;
            }

            if ($isValidate && !$roles['isAdministrator']) {
                $buttons = '';
                if ($usedAction != 'NOUSED') {
                    if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                }
                return $buttons;
            }

            $checkDetails = $data->count_detail <= 0;
            $checkDetailsPengiriman = $checkDetails || !$isPengiriman;
            $checkCode = isset($data->code) ? $data->code : '';
            $checkBeamTenun = $checkCode != 'BBTL';
            $buttons = '';
            if ($usedAction == 'NOUSED') {
                $buttons = "";
            } else {
                if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                if ((in_array('edit', $usedAction) || empty($usedAction)) && $checkDetailsPengiriman && $checkBeamTenun) $buttons .= $btnEdit;
                if ((in_array('delete', $usedAction) || empty($usedAction)) && $checkDetails && $checkBeamTenun) $buttons .= $btnDelete;
            }
            return $btnExtra . '' . $buttons;
        });

        $exportDatatable->setTotalRecords($datasCount);
        $exportDatatable->setFilteredRecords($datasCount);
        $exportDatatable->rawColumns($rawColumns);
        return $exportDatatable->make(true);
    }
    public static function fetch2($request, $constructor, $attributes = [], $filters = [], $appends = [], $rawColumn = ['aksi'])
    {
        $name = $request['name'] ?? '';
        $roles['isUserInformasi'] = Auth::user()->is('user informasi');
        $roles['isValidator'] = Auth::user()->is('validator');
        $roles['isAdministrator'] = Auth::user()->is('administrator');

        $rawColumns = $rawColumn;
        $isDetail = $request['isDetail'] ?? 'false';
        $isPengiriman = $request['isPengiriman'] ?? false;
        $btnExtra = $request['btnExtras'] ?? [];
        $usedAction = $request['usedAction'] ?? [];
        $extraData = $request['extraData'] ?? [];
        $datas = $constructor;

        $exportDatatable = DataTables::of($datas);

        //ATTRIBUTES
        if (in_array('status', $attributes)) {
            $rawColumns[] = 'status';
            $exportDatatable->addColumn('status', function ($data) {
                $badge = ($data->status == 'AKTIF') ? 'badge-success' : 'badge-danger';
                $spanHtml = "<span class='badge {$badge}'>{$data->status}</span>";
                return $spanHtml;
            });
        }

        if (in_array('relGudang', $attributes)) {
            $exportDatatable->addColumn('gudang', function ($data) {
                return $data->relGudang()->value('name');
            });
        }

        if (in_array('relGroup', $attributes)) {
            $exportDatatable->addColumn('group', function ($data) {
                return $data->relGroup()->value('name');
            });
        }

        if (in_array('relBarang', $attributes)) {
            $exportDatatable->addColumn('nama_barang', function ($data) {
                return $data->relBarang()->value('name');
            });
        }

        if (in_array('relPenomoranBeamReturDetail', $attributes)) {
            $exportDatatable->addColumn('nama_barang_retur', function ($data) {
                return $data->relPenomoranBeamReturDetail->nama_barang;
            });
        }

        if (in_array('relWarna', $attributes)) {
            $exportDatatable->addColumn('warna', function ($data) {
                return $data->relWarna()->value('name');
            });
        }

        if (in_array('relMotif', $attributes)) {
            $exportDatatable->addColumn('motif', function ($data) {
                return $data->relMotif()->value('alias');
            });
        }

        if (in_array('relSatuan1', $attributes)) {
            $exportDatatable->addColumn('satuan_utama', function ($data) {
                return $data->relSatuan()->value('name');
            });
        }

        if (in_array('relSatuan', $attributes)) {
            $exportDatatable->addColumn('satuan_utama', function ($data) {
                return $data->relSatuan1()->value('name');
            });
            $exportDatatable->addColumn('satuan_pilihan', function ($data) {
                return $data->relSatuan2()->value('name');
            });
        }

        if (in_array('relSupplier', $attributes)) {
            $exportDatatable->addColumn('supplier', function ($data) {
                return $data->relSupplier()->value('name');
            });
        }

        if (in_array('relTipe', $attributes)) {
            $exportDatatable->addColumn('tipe', function ($data) {
                return $data->relTipe()->value('name');
            });
        }

        if (in_array('relGudangAsal', $attributes)) {
            $exportDatatable->addColumn('gudang_asal', function ($data) {
                return $data->relGudangAsal()->value('name');
            });
        }

        if (in_array('relGudangTujuan', $attributes)) {
            $exportDatatable->addColumn('gudang_tujuan', function ($data) {
                return $data->relGudangTujuan()->value('name');
            });
        }

        if (in_array('relGrade', $attributes)) {
            $exportDatatable->addColumn('grade', function ($data) {
                return $data->relGrade()->value('grade');
            });
            $exportDatatable->addColumn('kualitas', function ($data) {
                return $data->relKualitas()->value('name');
            });
        }

        if (in_array('tipeGudangAsal', $attributes)) {
            $rawColumns[] = 'gudang_asal';
            $exportDatatable->addColumn('gudang_asal', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relTipePengiriman()->first()->gudang_asal;
            });
        }

        if (in_array('tipeGudangTujuan', $attributes)) {
            $rawColumns[] = 'gudang_tujuan';
            $exportDatatable->addColumn('gudang_tujuan', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relTipePengiriman()->first()->gudang_tujuan;
            });
        }
        if (in_array('relKualitas', $attributes)) {
            $rawColumns[] = 'kualitas';
            $exportDatatable->addColumn('kualitas', function ($data) {
                return '<i class="icon md-truck mr-2"></i>' . $data->relKualitas()->first()->kualitas;
            });
        }

        if (in_array('relPekerja', $attributes)) {
            $exportDatatable->addColumn('pekerja', function ($data) {
                return $data->relPekerja()->value('name');
            });
        }

        if (in_array('relNoRegisterPekerja', $attributes)) {
            $exportDatatable->addColumn('no_register', function ($data) {
                return $data->relPekerja()->value('no_register');
            });
        }

        if (in_array('jumlahBeam', $attributes)) {
            $exportDatatable->addColumn('jumlah_pcs', function ($data) {
                return $data->relLogStokPenerimaanBL()->value('volume_masuk_2');
            });
        }

        if (in_array('noBeam', $attributes)) {
            $exportDatatable->addColumn('no_beam', function ($data) {
                return $data->throughNomorBeam()->value('name');
            });
        }

        if (in_array('noKikw', $attributes)) {
            $exportDatatable->addColumn('no_kikw', function ($data) {
                return $data->throughNomorKikw()->value('name');
            });
        }
        if (in_array('noKiks', $attributes)) {
            $exportDatatable->addColumn('no_kiks', function ($data) {
                return $data->throughNomorKiks()->value('name');
            });
        }
        if (in_array('tanggal_potong', $attributes)) {
            $exportDatatable->addColumn('tanggal_potong', function ($data) {
                return Date::format($data->tanggal_potong, 98);
            });
        }

        if (in_array('relMesin', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                return $data->relMesin()->value('name');
            });
        }

        if (in_array('relAbsensiMesin', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                return implode(', ', $data->relAbsensiMesin());
            });
        }

        if (in_array('relPekerjaMesin', $attributes)) {
            $exportDatatable->addColumn('pekerja_mesin', function ($data) {
                $namaPekerja = [];
                $data->relPekerjaMesin()->each(function ($item, $key) use (&$namaPekerja) {
                    $namaPekerja[] = $item->relPekerja()->value('name');
                });
                return implode(', ', $namaPekerja);
            });
        }

        if (in_array('relMesinTenun', $attributes)) {
            $exportDatatable->addColumn('mesin', function ($data) {
                $nomorMesin = [];
                $data->relMesinTenun()->each(function ($item, $key) use (&$nomorMesin) {
                    $nomorMesin[] = $item->relMesin()->value('name');
                });
                return implode(', ', $nomorMesin);
            });
        }

        if (in_array('tipe_beam', $attributes)) {
            $exportDatatable->addColumn('tipe_beam', function ($data) {
                return $data->relBeam()->value('tipe_beam');
            });
        }

        if (in_array('tipe_pra_tenun', $attributes)) {
            $exportDatatable->addColumn('tipe_pra_tenun', function ($data) {
                return $data->relBeam()->value('tipe_pra_tenun');
            });
        }

        if (in_array('is_sizing', $attributes)) {
            $exportDatatable->addColumn('is_sizing', function ($data) {
                return $data->relBeam()->value('is_sizing') ?? 'TIDAK';
            });
        }

        if (in_array('customTanggal', $attributes)) {
            $exportDatatable->addColumn('tanggal_custom', function ($data) {
                return Date::format($data->tanggal, 98);
            });
        }

        if (in_array('rel_parent.name', $attributes)) {
            $exportDatatable->addColumn('rel_parent.name', function ($data) {
                return $data->parent_id != null ? $data->relParent->name : '-';
            });
        }

        if (in_array('relProductionCode', $attributes)) {
            $exportDatatable->addColumn('production_code', function ($data) {
                return $data->relProductionCode()->value('alias');
            });
        }

        if (in_array('relNomorPengiriman', $attributes)) {
            $exportDatatable->addColumn('no_pengiriman', function ($data) {
                return $data->relPengirimanBarang()->value('nomor');
            });
        }

        if (in_array('relMotifArray', $attributes)) {
            $exportDatatable->addColumn('motif', function ($data) {
                $motif = DB::table('tbl_motif')->whereIn('id', json_decode($data->id_motif, true))->pluck('alias')->toArray();
                return implode(' | ', $motif);
            });
        }

        if (in_array('relStatusNomorBeam', $attributes)) {
            $exportDatatable->addColumn('status_beam', function ($data) {
                return (in_array(0, $data->relBeam()->pluck('finish')->toArray())) ? '<span class="badge badge-outline badge-warning">Masih Dipakai</span>' : '<span class="badge badge-outline badge-primary">Tersedia</span>';
            });
        }

        //END NON RAW COLUMNS
        //END ATTRIBUTES

        //FILTERS
        if (in_array('filterBarang', $filters)) {
            $exportDatatable->filterColumn('nama_barang', function ($query, $keyword) {
                $query->whereHas('relBarang', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterWarna', $filters)) {
            $exportDatatable->filterColumn('warna', function ($query, $keyword) {
                $query->whereHas('relWarna', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterNomorBeam', $filters)) {
            $exportDatatable->filterColumn('no_beam', function ($query, $keyword) {
                $query->whereHas('throughNomorBeam', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterKikw', $filters)) {
            $exportDatatable->filterColumn('no_kikw', function ($query, $keyword) {
                $query->whereHas('throughNomorKikw', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }
        if (in_array('filterKiks', $filters)) {
            $exportDatatable->filterColumn('no_kiks', function ($query, $keyword) {
                $query->whereHas('throughNomorKiks', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterPekerja', $filters)) {
            $exportDatatable->filterColumn('pekerja', function ($query, $keyword) {
                $query->whereHas('relPekerja', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterSupplier', $filters)) {
            $exportDatatable->filterColumn('supplier', function ($query, $keyword) {
                $query->whereHas('relSupplier', function ($query) use ($keyword) {
                    return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                });
            });
        }

        if (in_array('filterPenomoranBeamReturDetail', $filters)) {
            $exportDatatable->filterColumn('nama_barang_retur', function ($query, $keyword) {
                $query->whereHas('relPenomoranBeamReturDetail', function ($query) use ($keyword) {
                    return $query->whereHas('relBarang', function ($query) use ($keyword) {
                        return $query->whereRaw("LOWER(name) LIKE '%$keyword%'");
                    });
                });
            });
        }
        //END FILTERS

        $exportDatatable->addColumn('aksi', function ($data) use ($btnExtra, $isDetail, $usedAction, $extraData, $isPengiriman, $name, $roles) {

            if ($roles['isUserInformasi']) return '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Detail">
                <i class="icon md-lock-outline"></i>
            </a>';

            $btnExtra = str_replace('%id', $data->id, implode('', $btnExtra));

            $extraDataAppend = "";
            if (!isset($extraData['row'])) {
                if (count($extraData) > 0) {
                    foreach ($extraData as $key => $value) {
                        $extraDataAppend .= "data-{$key}='{$value}'";
                    }
                }
            } else {
                if (count($extraData['row']) > 0) {
                    foreach ($extraData['row'] as $row) {
                        $value = $data->$row;
                        $extraDataAppend .= "data-{$row}='{$value}'";
                    }
                }
            }

            $add = ($isPengiriman == 1) ? 'data-id_tipe_pengiriman="' . $data->id_tipe_pengiriman . '"' : '';

            $btnDetail = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" ' . $extraDataAppend . ' data-original-title="Detail" onclick="goToDetail(' . $data->id . ', $(this));" ' . $add . '>
                <i class="icon md-menu" aria-hidden="true"></i>
            </a>';
            $btnEdit = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" ' . $extraDataAppend . ' onclick="editForm(' . $data->id . ', \'' . $isDetail . '\', $(this));" data-original-title="Edit">
                <i class="icon md-edit" aria-hidden="true"></i>
            </a>';
            $btnDelete = '<a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" onclick="deleteForm(' . $data->id . ', \'' . $isDetail . '\', $(this));" data-original-title="Delete">
                <i class="icon md-delete" aria-hidden="true"></i>
            </a>';

            $isValidate = isset($data->validated_at) ? $data->validated_at != null : false;
            if ($roles['isValidator']) {
                $btnValidation = '<button type="button" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                    data-model="' . $name . '" data-id="' . $data->id . '" data-state="validate" data-is-show="parent"
                    onclick="validateForm($(this));"><i class="icon md-check mr-2"></i></button>';
                $buttons = '';
                if ($usedAction != 'NOUSED') {
                    if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                }
                if ($isDetail != 'true' && !$isValidate) $buttons .= $btnValidation;
                return $buttons;
            }

            if ($isValidate && !$roles['isAdministrator']) {
                $buttons = '';
                if ($usedAction != 'NOUSED') {
                    if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                }
                return $buttons;
            }

            $checkDetails = $data->count_detail <= 0;
            $checkDetailsPengiriman = $checkDetails || !$isPengiriman;
            $checkCode = isset($data->code) ? $data->code : '';
            $checkBeamTenun = $checkCode != 'BBTL';
            $buttons = '';
            if ($usedAction == 'NOUSED') {
                $buttons = "";
            } else {
                if ((in_array('detail', $usedAction) || empty($usedAction)) && $isDetail != 'true') $buttons .= $btnDetail;
                if ((in_array('edit', $usedAction) || empty($usedAction)) && $checkDetailsPengiriman && $checkBeamTenun) $buttons .= $btnEdit;
                if ((in_array('delete', $usedAction) || empty($usedAction)) && $checkDetails && $checkBeamTenun) $buttons .= $btnDelete;
            }
            return $btnExtra . '' . $buttons;
        });

        $exportDatatable->rawColumns($rawColumns);
        return $exportDatatable->make(true);
    }

    public static function fetchSelect2($request, $constructor, $optional = ['id', 'name'], $extra = [], $multiSearch = false)
    {
        $param = strtolower($request['param']) ?? '';
        $page = $request['page'];
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $fetch = $constructor;
        $countData = $fetch->get()->count();
        $data['data'] = $fetch->when(!$multiSearch, function ($query) use ($offset, $resultCount) {
            return $query->skip($offset)->take($resultCount);
        })->get()
            ->mapWithKeys(function ($item, $key) use ($optional, $extra) {
                $uniqueKey = rand(1, 1000);
                $data[$key] = [
                    'id' => ($optional[0] == 'uniqueKey') ? $uniqueKey : $item[$optional[0]],
                    'text' => $item[$optional[1]]
                ];

                if (!empty($extra)) {
                    $convertExtra = collect($extra)->mapWithKeys(function ($value, $keys) use ($item) {
                        ($value === 'stok_utama' || $value === 'stok_pilihan') ? $itemValue = floatValue($item[$value]) : $itemValue = $item[$value];
                        return [$value => $itemValue];
                    });
                    $data[$key] = array_merge($data[$key], $convertExtra->toArray());
                }

                return $data;
            })->when($multiSearch, function ($query) use ($optional, $param) {
                return $query->reject(function ($value) use ($optional, $param) {
                    $name = $value[$optional[1]];
                    return !str_contains(strtolower($name), $param);
                });
            })->values()->all();

        $endCount = $offset + $resultCount;
        $data['pagination'] = ['more' => ($multiSearch) ? count($data['data']) > $endCount : $countData > $endCount];
        return response()->json($data);
    }

    public static function store($input, $model, $arrayCondition = [], $isResponseWithId = false)
    {
        if (!array_key_exists($model, getModel())) return response('Model Not Found', 404);
        // DB::beginTransaction();
        try {
            $stringModel = strtolower($model);
            $model = getModel($model);
            $modelId = $model::create($input)->id;
            activity()->log("Menambah data {$stringModel} {$model}");
            DB::commit();
            return $isResponseWithId ? response(['message' => 'Data Successfully Saved!', 'id' => $modelId], 200) : response('Data Successfully Saved!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            print_r($th->getMessage() . ' ');
            return response('Data is Not Successfully Saved!', 401);
        }
    }

    public static function update($input, $model, $id, $arrayCondition = [])
    {
        if (!array_key_exists($model, getModel())) return response('Model Not Found', 404);

        // DB::beginTransaction();
        try {
            $stringModel = strtolower($model);
            $model = getModel($model);
            $input['updated_by'] = Auth::id();
            $model::where(['id' => $id])->update($input);
            activity()->log("Merubah data {$stringModel} {$model}");
            DB::commit();
            return response('Data Successfully Updated!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            print_r($th->getMessage() . ' ');
            return response('Data Not Successfully Updated!', 401);
        }
    }

    public static function delete($id, $model, $isPermanent = false)
    {
        if (!array_key_exists($model, getModel())) return response('Model Not Found', 404);

        DB::beginTransaction();
        try {
            $stringModel = strtolower($model);
            $model = getModel($model);
            ($isPermanent) ? $model::where('id', $id)->forceDelete() : $model::where('id', $id)->delete();
            activity()->log("Menghapus data {$stringModel} {$model}");
            DB::commit();
            return response('Data is Successfully Deleted', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            print_r($th->getMessage() . ' ');
            return response('Data is Not Successfully Deleted', 401);
        }
    }

    public static function storeFile($request, $fields = [], $allowedfileExtension, $callback)
    {
        foreach ($fields as $field) {
            $store = function ($file, $loc, $key = null) use ($callback, $allowedfileExtension, $field) {
                $extension = $file->getClientOriginalExtension();
                $key = ($key != null) ? '_' . $key : '';
                // $filename = "{$field}{$key}.jpg";
                $name = str_replace('profile_', '', $field);
                $filename = $name . $file->hashName();
                if (in_array($extension, $allowedfileExtension)) {
                    $file->move($loc . '/', $filename);
                    $callback($name, $filename);
                }
            };

            if ($request->has($field)) {
                $file = $request[$field];
                if (is_array($file)) {
                    foreach ($request[$field] as $i => $files) {
                        $store($files, public_path() . '/assets/img', $i);
                    }
                } else {
                    $store($file, public_path() . '/assets/img');
                }
            }
        }
    }

    public static function storeImage($request, $field, $callback)
    {
        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'svg', 'ico'];
        self::storeFile($request, $field, $allowedfileExtension, $callback);
    }

    public static function storeDoc($request, $field, $callback)
    {
        $allowedfileExtension = ['doc', 'pdf', 'docx', 'odt'];
        self::storeFile($request, $field, $allowedfileExtension, $callback);
    }

    public static function storeWarehouse($warehouse, $input = [])
    {
        if (!array_key_exists($warehouse, getModel())) return response('Model Not Found', 404);
        try {
            $warehouse = getModel($warehouse);
            $stringModel = strtolower($warehouse);
            $warehouseId = $warehouse::create($input)->id;
            activity()->log("Menambah data {$stringModel} {$warehouse}");
            return $warehouseId;
        } catch (\Throwable $th) {
            DB::rollBack();
            print_r($th->getMessage() . ' ');
            return response('Data is Not Successfully Saved!', 401);
        }
    }

    public static function updateWarehouse($id, $warehouse, $input = [])
    {
        if (!array_key_exists($warehouse, getModel())) return response('Model Not Found', 404);
        try {
            $warehouse = getModel($warehouse);
            $stringModel = strtolower($warehouse);
            $warehouse::where('id', $id)->update($input);
            activity()->log("Menambah data {$stringModel} {$warehouse}");
            return (int) $id;
        } catch (\Throwable $th) {
            DB::rollBack();
            print_r($th->getMessage() . ' ');
            return response('Data is Not Successfully Saved!', 401);
        }
    }
}
