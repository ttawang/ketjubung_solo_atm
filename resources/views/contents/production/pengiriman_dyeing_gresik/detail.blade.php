<div class="panel-body">
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::tools($tools, $id) !!}
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tablePengirimanDyeingGresikDetail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>Tanggal</th>
                        <th>Jenis Benang</th>
                        <th>Volume 1</th>
                        <th>Volume 2</th>
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
