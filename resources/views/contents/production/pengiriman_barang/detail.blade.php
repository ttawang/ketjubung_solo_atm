<div class="panel-heading">
    <div class="panel-title form-group row">
        <div class="col-md-12">
            @if ($data->validated_at != null)
                <h5 class="text-right">Tanggal Validasi :
                    &nbsp;<em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em><i
                        class="icon md-check-circle ml-2 text-success"></i></h4>
            @endif
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>
                        @if ($model == 'PengirimanBarang')
                            @if ($data->id_tipe_pengiriman === 7 || $data->id_tipe_pengiriman === 8)
                                <span class="badge badge-outline badge-primary">
                                    {{ $data->total ?? 0 }} Pcs
                                </span>
                            @endif
                        @endif
                        <span class="badge badge-outline badge-primary">
                            {{ $data->nomor }}
                        </span>
                        <input type="hidden" name="tanggal" value="{{ $data->tanggal }}">
                        <input type="hidden" name="id_tipe_pengiriman" value="{{ $data->id_tipe_pengiriman }}">
                        <span class="badge badge-outline badge-primary">
                            {{ $data->relTipePengiriman()->value('title') ?? $data->txt_tipe_pengiriman }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel-body">
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::tools($tools, $id, 'Kirim', ['state' => 'input']) !!}
            {{-- <button type="button" data-id="{{ $id }}" data-tanggal="{{ $data->tanggal ?? date('Y-m-d') }}"
                class="btn btn-warning btn-template btn-sm waves-effect waves-classic float-left mr-2"
                onclick="addFormSendAll($(this));">
                <i class="icon md-mail-send mr-2"></i> Kirim Semua
            </button> --}}
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0"
                id="tablePengirimanBarangDetailInput">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        @if ($data->column_name == 'Beam')
                            <th>No. Beam / KIKW</th>
                        @endif
                        <th>Nama Barang</th>
                        <th>Gudang Asal</th>
                        <th>Volume 1</th>
                        <th>Satuan 1 (Utama)</th>
                        <th>Volume 2</th>
                        <th>Satuan 2 (Pilihan)</th>
                        <th>Catatan</th>
                        <th width="70px">Aksi</th>
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
