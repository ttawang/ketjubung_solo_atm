<div class="panel-body container-fluid">
    <div class="form-group row row-lg">
        <div class="col-md-12 col-lg-6">
            <div class="form-group form-material row">
                <label class="col-md-3 col-form-label">Tanggal: </label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{ App\Helpers\Date::format($data->tanggal, 105) }}"
                        disabled>
                </div>
            </div>
            <div class="form-group form-material row">
                <label class="col-md-3 col-form-label">Proses: </label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{ $data->proses }}" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            {!! App\Helpers\Template::tools(['refreshDetail'], $id, 'Stokopname') !!}
        </div>
    </div>

    <div class="form-group row row-lg">
        <div class="col-md-12 col-lg-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableStokopnameDetail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>Gudang</th>
                        <th>Nama Barang</th>
                        @if (checkCodeStokopname($data->code, 'class1'))
                            <th>Stok</th>
                            <th>Stokopname</th>
                            <th>Selisih</th>
                        @elseif (checkCodeStokopname($data->code, 'class2'))
                            <th>Warna</th>
                            <th>Stok 1</th>
                            <th>Stokopname 1</th>
                            <th>Selisih 1</th>
                            <th>Stok 2</th>
                            <th>Stokopname 2</th>
                            <th>Selisih 2</th>
                        @elseif (checkCodeStokopname($data->code, 'class3'))
                            <th>Warna</th>
                            <th>Motif</th>
                            <th>No. Beam</th>
                            <th>No. KIKW</th>
                            <th>Mesin</th>
                            <th>Tipe Pra Tenun</th>
                            <th>Sizing</th>
                            <th>Stok 1</th>
                            <th>Stokopname 1</th>
                            <th>Selisih 1</th>
                            <th>Stok 2</th>
                            <th>Stokopname 2</th>
                            <th>Selisih 2</th>
                        @elseif (checkCodeStokopname($data->code, 'class4'))
                            <th>Warna</th>
                            <th>Motif</th>
                            <th>No. Beam</th>
                            <th>No. KIKW</th>
                            <th>Mesin</th>
                            <th>Kualitas</th>
                            <th>Jenis Cacat</th>
                            <th>Stok</th>
                            <th>Stokopname</th>
                            <th>Selisih</th>
                        @endif
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <button type='button' class='btn btn-default btn-sm waves-effect waves-classic'
                onclick='closeForm($(this));'>
                <i class='icon md-arrow-left mr-2'></i> Kembali
            </button>
        </div>
    </div>
</div>
