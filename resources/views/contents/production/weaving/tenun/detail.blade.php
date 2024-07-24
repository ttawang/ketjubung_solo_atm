<div class="panel-heading">
    <div class="panel-title form-group row">
        <div class="col-md-12">
            @if ($data->validated_at != null)
                <h5 class="text-right">Tanggal Validasi :
                    &nbsp;<em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em><i
                        class="icon md-check-circle ml-2 text-success"></i></h4>
            @endif
            <div class="row text-center">
                <div class="col-md-4">
                    <h3><span class="badge badge-outline badge-primary">No. Beam :
                            {{ $data->throughNomorBeam()->value('name') ?? '' }}</span></h3>
                </div>
                <div class="col-md-4">
                    <h3><span class="badge badge-outline badge-primary">No. KIKW :
                            {{ $data->throughNomorKikw()->value('name') ?? '' }}</span></h3>
                </div>
                <div class="col-md-4">
                    <h3>
                        <span class="badge badge-outline badge-primary" id="spanSisaBeam">Sisa Beam :
                            {{ $data->relSisaBeam() . ' Pcs' }}
                        </span>
                    </h3>
                </div>
                <input type="hidden" id="hidden_id" value="{{ $data->id }}">
                <input type="hidden" id="hidden_id_beam" value="{{ $data->id_beam }}">
                <input type="hidden" id="hidden_id_mesin"
                    value="{{ $data->relMesinHistoryLatest()->value('id_mesin') }}">
                <input type="hidden" id="hidden_mesin"
                    value="{{ $data->relMesinHistoryLatest()->first()->relMesin()->value('name') ?? '' }}">
            </div>
        </div>
    </div>
</div>
<div class="panel-body">
    <div class="nav-tabs-horizontal" data-plugin="tabs">
        <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-is-finish="{{ $data->is_finish ? 'true' : 'false' }}"
                    data-table="tableTenunInput" data-tab="TenunDetail" data-column="TenunInput" data-retrieve="true"
                    data-form="input" data-toggle="tab" href="#tabInput" aria-controls="tabInput" role="tab">
                    <i class="icon md-format-valign-bottom mr-2"></i> Input
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-is-finish="{{ $data->is_finish ? 'true' : 'false' }}" data-toggle="tab"
                    data-table="tableTenunOutput" data-tab="TenunDetail" data-column="TenunOutput" data-retrieve="false"
                    data-form="output" href="#tabOutput" aria-controls="tabOutput" role="tab">
                    <i class="icon md-format-valign-top mr-2"></i> Output
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-is-finish="{{ $data->is_finish ? 'true' : 'false' }}" data-toggle="tab"
                    data-table="tableTenunDiturunkan" data-tab="TenunDetail" data-column="TenunDiturunkan"
                    data-retrieve="false" data-form="diturunkan" href="#tabDiturunkan" aria-controls="tabDiturunkan"
                    role="tab">
                    <i class="icon md-swap-vertical mr-2"></i> Diturunkan
                </a>
            </li>
        </ul>
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabInput" role="tabpanel">

                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Input', [
                            'form' => 'input',
                            'tab' => 'TenunDetail',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableTenunInput">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Mesin</th>
                                    <th>Volume 1</th>
                                    <th width="60px">Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th width="60px">Satuan 2 (Pilihan)</th>
                                    <th>Status</th>
                                    <th width="70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tabOutput" role="tabpanel">

                {{-- <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Output', [
                            'form' => 'output',
                            'tab' => 'TenunDetail',
                        ]) !!}
                    </div>
                </div> --}}

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableTenunOutput">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th data-visible="false">Pekerja</th>
                                    <th data-visible="false">Songket</th>
                                    <th>Nama Sarung</th>
                                    <th>Mesin</th>
                                    <th>Potongan</th>
                                    <th>Sisa Beam</th>
                                    {{-- <th width="70px">Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tabDiturunkan" role="tabpanel">

                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Input', [
                            'form' => 'diturunkan',
                            'tab' => 'TenunDetail',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableTenunDiturunkan">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Mesin</th>
                                    <th>Volume 1</th>
                                    <th width="60px">Satuan 1 (Utama)</th>
                                    <th>Volume 2</th>
                                    <th width="60px">Satuan 2 (Pilihan)</th>
                                    <th width="70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {{-- <div class="form-group row">
                <label class="col-md-2" for=""><i class="md-airline-seat-recline-normal mr-2"></i> Mesin
                    Loom:
                </label>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-9">
                            <select name="id_mesin" data-route="{{ route('helper.getMesin') }}" allow-clear="false"
                                onchange="changeMesin($(this));" data-jenis="LOOM" dropdown-parent=""
                                data-id-beam="{{ $data->id_beam }}" data-placeholder="-- Pilih Mesin --"
                                class="form-control col-md-6 select2" id="select_mesin"></select>
                        </div>
                        <div class="col-md-3">
                            <div id="wrapperButtonApplyMesin"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null, true, [
                'is_finish' => $data->is_finish,
                'id_beam' => $data->id_beam,
            ]) !!}
        </div>
    </div>
</div>
