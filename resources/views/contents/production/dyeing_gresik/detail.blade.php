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
                <a class="nav-link active" data-retrieve="true" data-table="tableInput" data-code="BBDG"
                    data-toggle="tab" href="#tabInput" aria-controls="tabInput" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Input
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" data-retrieve="false" data-table="tableOutput" data-code="BDG"
                    href="#tabOutput" aria-controls="tabOutput" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Output
                </a>
            </li>
        </ul>
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabInput" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Input', ['code' => 'BBDG']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableInput">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Gudang</th>
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
            <div class="tab-pane" id="tabOutput" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Output', ['code' => 'BDG']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableOutput">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th>Tanggal</th>
                                    <th>Gudang</th>
                                    <th>Jenis Benang</th>
                                    <th>Warna</th>
                                    <th>Volume 1</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div id="wrapperWarna"></div>
                
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
