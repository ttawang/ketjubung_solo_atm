<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Template
{

    public static function tools($heyStackButtons, $id = '', $txtButton = 'Data', $extraData = [], $isValidated = false, $isDisabled = '')
    {

        $extraDataAppend = "";
        if (count($extraData) > 0) {
            foreach ($extraData as $key => $value) {
                $extraDataAppend .= "data-{$key}='{$value}'";
            }
        }

        $buttonExceptIfVaildated = ['tambah', 'tambahDetail', 'tambahDetailForm', 'deletePermanent'];

        $button['tambah']        = '<button type="button" ' . $extraDataAppend . ' class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" id="btnTambah" onclick="addForm($(this), false);" ' . $isDisabled . '><i class="icon md-plus mr-2"></i> Tambah ' . $txtButton . '</button>';
        $button['tambahForm']    = '<button type="button" ' . $extraDataAppend . ' class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="showFormView(' . $id . ');"><i class="icon md-plus mr-2"></i> Tambah ' . $txtButton . '</button>';
        $button['refresh']       = '<button type="button" onclick="tableAjax.ajax.reload();" class="btn btn-default btn-sm waves-effect waves-classic float-right"><i class="icon md-refresh-sync spin mr-2"></i> Refresh</button>';
        $button['filter']        = '<button type="button" class="btn btn-default btn-sm waves-effect waves-classic mr-2"><i class="icon md-filter mr-2"></i> Filter</button>';
        $button['excel']         = '<button type="button" class="btn btn-warning btn-sm waves-effect waves-classic mr-2" onclick="excelProcessForm();"><i class="icon md-file-excel mr-2"></i> Excel </button>';
        $button['tambahDetail']  = '<button type="button" data-id="' . $id . '" ' . $extraDataAppend . ' class="btn btn-primary btn-template btn-sm waves-effect waves-classic float-left mr-2 btn-create" onclick="addForm($(this), true);"><i class="icon md-plus mr-2"></i> Tambah ' . $txtButton . '</button>';
        $button['refreshDetail'] = '<button type="button" onclick="tableAjaxDetail.ajax.reload();" class="btn btn-default btn-sm waves-effect waves-classic float-right"><i class="icon md-refresh-sync spin btn-refresh mr-2"></i> Refresh</button>';
        $button['tambahDetailForm']  = '<button type="button" data-id="' . $id . '" ' . $extraDataAppend . ' class="btn btn-primary btn-template btn-sm waves-effect waves-classic float-left mr-2 btn-create" onclick="addForm($(this), true, false);"><i class="icon md-plus mr-2"></i> Tambah ' . $txtButton . '</button>';
        $button['approve']       = '<button type="button" ' . $extraDataAppend . ' class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" id="btnApprove" onclick="approval($(this));" disabled><i class="icon md-check-all mr-2"></i> Terima Semua Barang</button>';
        $button['deletePermanent'] = '<div class="col-lg-6">
            <button type="button" data-id="{{ $data->id }}" data-model="PenerimaanBarang"
                class="btn btn-danger btn-sm waves-effect waves-classic float-right" onclick="deleteAll($(this));">
                <i class="icon md-delete mr-2"></i> Hapus Semua Data
            </button>
        </div>';
        $mapButtons = array_map(function ($value) use ($button, $buttonExceptIfVaildated, $isValidated) {
            return (in_array($value, $buttonExceptIfVaildated) && $isValidated) ? '' : $button[$value];
        }, $heyStackButtons);
        if (Auth::user()->is('validator') || Auth::user()->is('user informasi')) $mapButtons = [];
        return implode('', $mapButtons);
    }

    public static function footerTools($id = null, $name = null, $isNotValidate = null, $isTenun = false, $data = [])
    {
        $validatorUser = Auth::user()->is('validator');
        $buttonBack = "<button type='button' class='btn btn-default btn-sm waves-effect waves-classic btn-back'
            onclick='closeForm($(this));'>
            <i class='icon md-arrow-left mr-2'></i> Kembali
        </button>";
        $button = "<button type='button' data-model='{$name}' data-id='{$id}' class='btn btn-warning btn-sm waves-effect waves-classic float-right'
            onclick='cetakForm($(this));'>
            <i class='icon md-print mr-2'></i> Cetak
        </button>";
        if ($validatorUser) {
            if ($isNotValidate) {
                $button = "<button type='button' class='btn btn-primary btn-sm waves-effect waves-classic float-right mr-2'
                        data-model='{$name}' data-id='{$id}' data-state='validate' data-is-show='detail'
                        onclick='validateForm($(this));'><i class='icon md-check mr-2'></i> Validasi
                    Form</button>";
            } else {
                $button = "<button type='button' class='btn btn-danger btn-sm waves-effect waves-classic float-right mr-2'
                    data-model='{$name}' data-id='{$id}' data-state='rollback' data-is-show='detail'
                    onclick='validateForm($(this), true);'><i class='icon md-refresh-sync-alert mr-2'></i> Batalkan Validasi
                Form</button>";
            }
        } else {
            if ($isNotValidate) {
                if ($isTenun) {
                    if ($data['is_finish']) {
                        $button = "<button type='button' data-id-beam='" . $data['id_beam'] . "' data-id-tenun='" . $id . "'
                            data-tipe-beam='lusi' data-rollback='true'
                            class='btn btn-danger btn-sm waves-effect waves-classic float-right' onclick='rollbackBeam($(this));'>
                            <i class='icon md-refresh-sync-alert mr-2'></i> Rollback
                        </button>";
                    } else {
                        $button = "<button type='button' data-id-beam='" . $data['id_beam'] . "' data-id-tenun='" . $id . "'
                            data-tipe-beam='lusi' data-rollback='false'
                            class='btn btn-primary btn-sm waves-effect waves-classic float-right'
                            onclick='finishBeam($(this));'>
                            <i class='icon md-check-all mr-2'></i> Beam Lusi
                        </button>";
                    }

                    // <button type='button' data-tipe-beam='songket' data-id-tenun='" . $id . "'
                    //         data-rollback='false' class='btn btn-warning btn-sm waves-effect waves-classic float-right mr-2'
                    //         onclick='finishSongket($(this));'>
                    //         <i class='icon md-check-all mr-2'></i> Beam Per Songket
                    //     </button>
                }
            }
        }
        return $buttonBack . '' . $button;
    }

    public static function excel()
    {
        return Option::orderBy('id')->get()->mapWithKeys(function ($item, $key) {
            return [$item['option_name'] => $item['option_value']];
        });
    }
}
