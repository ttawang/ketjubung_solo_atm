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
                    <h3><span class="badge badge-outline badge-primary">Nomor : {{ $data->nomor }}</span></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel-body">
    <div class="nav-tabs-horizontal" data-plugin="tabs">
        <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-retrieve="true" data-table="tableKirim" data-status="KIRIM"
                    data-toggle="tab" href="#tabKirim" aria-controls="tabKirim" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Input
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" data-retrieve="false" data-table="tableTerima" data-status="TERIMA"
                    href="#tabTerima" aria-controls="tabTerima" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Output
                </a>
            </li>
        </ul>
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabKirim" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Input', ['status' => 'KIRIM']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableKirim">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Benang</th>
                                    <th>Volume 1</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tabTerima" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Output', ['status' => 'TERIMA']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableTerima">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Benang</th>
                                    <th>Warna</th>
                                    <th>Volume 1</th>
                                    <th>Volume 2</th>
                                    <th>Aksi</th>
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
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
