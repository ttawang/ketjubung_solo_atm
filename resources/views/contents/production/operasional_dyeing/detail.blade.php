<div class="panel-body">
    <div class="form-group row">
        <div class="col-md-12">
            <button type="button" data-id="{{ $id }}"
                class="btn btn-primary btn-template btn-sm waves-effect waves-classic float-left mr-2 btn-create"
                onclick="addForm($(this), true);"><i class="icon md-plus mr-2"></i> Tambah Chemical
            </button>
            <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
                data-id-operasional-dyeing="{{ $id }}" data-tanggal="{{ $data->tanggal }}"
                onclick="addFormResep($(this));"><i class="icon md-assignment mr-2"></i> Tambah Resep Warna
            </button>
            <button type="button" onclick="tableAjaxDetail.ajax.reload();"
                class="btn btn-default btn-sm waves-effect waves-classic float-right">
                <i class="icon md-refresh-sync spin mr-2"></i>
                Refresh
            </button>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0"
                id="tableOperasionalDyeingDetail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>Tanggal</th>
                        <th>Chemical</th>
                        <th>Volume</th>
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
