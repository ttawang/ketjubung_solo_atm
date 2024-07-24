<div class="form-group row">
    <div class="col-md-12">
        <h3 class="text-center">
            <span class="badge badge-outline badge-default"> {{ $data->nama_barang }}</span>
        </h3>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
            data-id-dyeing-detail="{{ $id }}" data-id-warna="{{ $data->id_warna }}"
            onclick="addFormWarna($(this));"><i class="icon md-plus mr-2"></i> Tambah Warna</button>
        <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
            data-id-dyeing-detail="{{ $id }}" data-id-warna="{{ $data->id_warna }}" data-id-barang="{{ $data->id_barang }}"
            onclick="addFormResep($(this));"><i class="icon md-assignment mr-2"></i> Tambah Resep Warna</button>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableWarna">
            <thead>
                <tr>
                    <th width="30px">No</th>
                    <th>Tanggal</th>
                    <th>Warna</th>
                    <th>Satuan</th>
                    <th>Volume</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($warna as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ App\Helpers\Date::format($item->tanggal, 98) }}</td>
                        <td>{{ $item->relBarang()->value('name') }}</td>
                        <td>{{ $item->relSatuan()->value('name') }}</td>
                        <td>{{ $item->volume }}</td>
                        <td>
                            <a href="javascript:void(0);"
                                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                                data-toggle="tooltip" data-id-dyeing-detail="{{ $id }}"
                                data-id-satuan="{{ $item->id_satuan }}" data-volume="{{ $item->volume }}"
                                data-id="{{ $item->id }}" data-id-log-stok="{{ $item->id_log_stok }}"
                                data-id-pewarna="{{ $item->id_barang }}"
                                data-nama-pewarna="{{ $item->relBarang()->value('name') }}"
                                data-id-warna="{{ $data->id_warna }}" onclick="addFormWarna($(this));"
                                data-original-title="Edit">
                                <i class="icon md-edit" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0);"
                                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                                data-id-dyeing-detail="{{ $id }}" data-id="{{ $item->id }}"
                                data-id-log-stok="{{ $item->id_log_stok }}" data-toggle="tooltip"
                                onclick="deleteFormWarna($(this));" data-original-title="Delete">
                                <i class="icon md-delete" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
