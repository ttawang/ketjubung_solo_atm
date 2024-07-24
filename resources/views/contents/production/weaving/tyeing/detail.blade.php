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
                            {{ $data->throughNomorBeam()->value('name') }}</span></h3>
                </div>
                <div class="col-md-4">
                    <h3><span class="badge badge-outline badge-primary">No. KIKW :
                            {{ $data->throughNomorKikw()->value('name') }}</span></h3>
                </div>
                <div class="col-md-4">
                    <h3><span class="badge badge-outline badge-primary">No. Loom :
                            {{ $data->relMesin()->value('name') }}</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel-body">

    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::tools($tools, $id) !!}
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableTyeingDetail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>Tanggal</th>
                        <th>Pekerja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
