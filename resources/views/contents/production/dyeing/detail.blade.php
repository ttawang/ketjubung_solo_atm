<div class="panel-heading">
    <div class="panel-title form-group row">
        <div class="col-md-12">
            @if ($data->validated_at != null)
                <h5 class="text-right">Tanggal Validasi :
                    &nbsp;<em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em><i
                        class="icon md-check-circle ml-2 text-success"></i></h4>
            @endif
            <div class="row text-center">
                <div class="col-md-12">
                    <h3><span class="badge badge-outline badge-primary">Nomor : {{ $data->no_kikd }}</span></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel-body">
    <div class="nav-tabs-horizontal" data-plugin="tabs">
        <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-retrieve="true" data-table="tableSoftcone" data-status="SOFTCONE"
                    data-toggle="tab" href="#tabSoftcone" aria-controls="tabSoftcone" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Softcone
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" data-retrieve="false" data-table="tableDyeOven"
                    data-status="DYEOVEN" href="#tabDyeOven" aria-controls="tabDyeOven" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Dye & Oven
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" data-retrieve="false" data-table="tableOvercone"
                    data-status="OVERCONE" href="#tabOvercone" aria-controls="tabOvercone" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Overcone
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" data-retrieve="false" data-table="tableReturn"
                    data-status="RETURN" href="#tabReturn" aria-controls="tabReturn" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Return
                </a>
            </li>
        </ul>
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabSoftcone" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Softcone', ['status' => 'SOFTCONE']) !!}
                        <button type="button" data-id="{{ $id }}" data-status="SOFTCONE" data-retur='YA'
                            class="btn btn-warning btn-template btn-sm waves-effect waves-classic float-left mr-2"
                            onclick="addForm($(this), true);"><i class="icon md-plus mr-2"></i>
                            Tambah Softcone (Retur)
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableSoftcone">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Mesin</th>
                                    <th>Jenis Benang</th>
                                    <th>Volume 1</th>
                                    <th>Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th>Satuan 2 (Pilihan)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tabDyeOven" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Dye & Oven', ['status' => 'DYEOVEN']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDyeOven">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Mesin</th>
                                    <th>Jenis Benang | Warna</th>
                                    <th>Volume 1</th>
                                    <th>Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th>Satuan 2 (Pilihan)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div id="wrapperWarna"></div>

            </div>
            <div class="tab-pane" id="tabOvercone" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Overcone', ['status' => 'OVERCONE']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableOvercone">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Mesin</th>
                                    <th>Jenis Benang | Warna</th>
                                    <th>Volume 1</th>
                                    <th>Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th>Satuan 2 (Pilihan)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tabReturn" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Return', ['status' => 'RETURN']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableReturn">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Mesin</th>
                                    <th>Jenis Benang | Warna</th>
                                    <th>Volume 1</th>
                                    <th>Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th>Satuan 2 (Pilihan)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <small>*) Klik Aksi '<i class="icon md-palette"></i>' Untuk Menambah Obat Pewarna. </small>
                    </div>
                </div>

                <div id="wrapperWarnaReturn"></div>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
