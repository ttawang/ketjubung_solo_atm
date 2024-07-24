<div class="panel-body">
    <div class="form-group row">
        <div class="col-md-12">
            <h3 class="text-center">
                <span class="badge badge-outline badge-default"><i class="icon md-assignment mr-2"></i>
                    {{ $data->name ?? '' }}
                </span>
            </h3>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <button type="button" data-id="{{ $data->id }}"
                class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                data-route="{{ route('database.resep.create') }}" onclick="addForm($(this), true);"><i
                    class="icon md-plus mr-2"></i> Tambah Chemical</button>
            <button type="button" onclick="tableAjaxDetail.ajax.reload();"
                class="btn btn-default btn-sm waves-effect waves-classic float-right"><i
                    class="icon md-refresh-sync spin mr-2"></i> Refresh</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="tableResepDetail" class="table table-bordered table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Chemical</th>
                        <th>Satuan</th>
                        <th>Volume</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
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
