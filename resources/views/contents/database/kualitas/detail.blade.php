<div class="panel-heading">
    <div class="panel-title form-group row">
        <div class="col-md-12">
            <div class="row text-center">
                <div class="col-md-12">
                    <h3><span class="badge badge-outline badge-primary">Grade : {{ $data->grade }}</span></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel-body">
    <div class="nav-tabs-horizontal" data-plugin="tabs">
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabKualitas" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambahDetail', 'refreshDetail'], $id, 'Jenis Cacat') !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tableMappingKualitas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Name</th>
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
            <button type="button" class="btn btn-default btn-sm waves-effect waves-classic"
                onclick="closeForm($(this));">
                <i class="icon md-arrow-left mr-2"></i> Kembali
            </button>
        </div>
    </div>
</div>
