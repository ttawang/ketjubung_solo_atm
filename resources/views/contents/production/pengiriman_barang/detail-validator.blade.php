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
    <div class="nav-tabs-horizontal" data-plugin="tabs">
        <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-table="tablePengirimanBarangDetailInput" data-state="input"
                    data-status="ASAL" data-column="{{ $data->column_name }}" data-retrieve="false" data-toggle="tab"
                    href="#tabInput" aria-controls="tabInput" role="tab">
                    <i class="icon md-refresh-sync spin mr-2"></i> Kirim
                </a>
            </li>
            @if ($data->id_tipe_pengiriman != 7 || $data->id > 965)
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-toggle="tab" data-table="tablePengirimanBarangDetailOutput"
                        data-state="output" data-status="TUJUAN" data-column="{{ $data->column_name }}"
                        data-retrieve="false" href="#tabOutput" aria-controls="tabOutput" role="tab">
                        <i class="icon md-refresh-sync spin mr-2"></i> Terima
                    </a>
                </li>
            @endif
        </ul>
        <div class="tab-content py-20">
            <div class="tab-pane active" id="tabInput" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Kirim', ['state' => 'input']) !!}
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
            </div>
            <div class="tab-pane" id="tabOutput" role="tabpanel">
                <div class="form-group row">
                    <div class="col-md-12">
                        {!! App\Helpers\Template::tools($tools, $id, 'Terima', ['state' => 'output']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover table-striped" cellspacing="0"
                            id="tablePengirimanBarangDetailOutput">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    @if ($data->column_name == 'Beam')
                                        <th>No. Beam / KIKW</th>
                                    @endif
                                    <th>Nama Barang</th>
                                    <th>Gudang Tujuan</th>
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
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::footerTools($id, $model, $data->validated_at == null) !!}
        </div>
    </div>
</div>
