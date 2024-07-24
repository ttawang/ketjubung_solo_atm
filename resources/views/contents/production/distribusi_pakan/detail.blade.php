<div class="panel-body">
    <div class="form-group row row-lg">
        <div class="col-md-12">
            @if ($data->validated_at != null)
                <h5 class="text-right">Tanggal Validasi :
                    &nbsp;<em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em><i
                        class="icon md-check-circle ml-2 text-success"></i></h4>
            @endif
            <div class="row text-center">
                <div class="col-md-6">
                    <h3><span class="badge badge-outline badge-primary">Nomor : {{ $data->nomor }}</span></h3>
                </div>
                <div class="col-md-6">
                    <h3><span class="badge badge-outline badge-primary">Tipe : {{ ucwords($data->tipe) }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <button type="button" data-id="{{ $id }}"
                class="btn btn-primary btn-template btn-sm waves-effect waves-classic float-left mr-2 btn-create"
                onclick="addFormView($(this));"><i class="icon md-plus mr-2"></i> Tambah Data [END]</button>
            <button type="button" onclick="tableAjaxDetail.ajax.reload();"
                class="btn btn-default btn-sm waves-effect waves-classic float-right btn-refresh"><i
                    class="icon md-refresh-sync spin mr-2"></i> Refresh [F2]</button>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0"
                id="tableDistribusiPakanDetail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>No. KIKW</th>
                        <th>No. Mesin</th>
                        <th>Nama Barang</th>
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
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
