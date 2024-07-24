<div class="tab-pane active" id="tabInput" role="tabpanel">
    <div class="form-group row">
        <div class="col-md-12">
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this))">
                <i class="icon md-plus mr-2"></i> Tambah Barang</button>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-detail">
                <thead>
                    <tr>
                        <th width="30px">No.</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Warna</th>
                        <th>Gudang</th>
                        <th>Volume (Cones)</th>
                        <th>Volume (kg)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_palet" id="id_palet">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="input">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control select2" name="id_gudang" id="gudang" required>
                            <option value="0">-- pilih gudang --</option>
                            @foreach ($gudang as $i)
                                <option value="{{ $i->id_gudang }}">{{ $i->relGudang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control select2" name="id_barang" id="barang" required>
                            <option value="0">-- pilih barang --</option>
                            @foreach ($barang as $i)
                                <option value="{{ $i->id_barang }}">{{ $i->relBarang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Warna</label>
                        <select class="form-control select2" name="id_warna" id="warna" required>
                            <option value="0">-- pilih warna --</option>
                            @foreach ($warna as $i)
                                <option value="{{ $i->id_warna }}">{{ $i->relWarna->alias }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume 1</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_1" class="text-warning">0</span>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_1" id="volume_1" required>
                            <input type="hidden" value="1" name="id_satuan_1" id="id_satuan_1">
                            <div class="input-group-append">
                                <div class="input-group-text">Cones</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume 2</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_2" class="text-warning">0</span>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_2" id="volume_2" required>
                            <input type="hidden" value="2" name="id_satuan_2" id="id_satuan_2">
                            <div class="input-group-append">
                                <div class="input-group-text">Kg</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
    });

    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: `palet/table/${mode}/${id}/${tipe}`,
            lengthMenu: [15, 25, 50, 100],
            processing: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'barang',
                    name: 'barang'
                },
                {
                    data: 'warna',
                    name: 'warna'
                },
                {
                    data: 'gudang',
                    name: 'gudang'
                },
                {
                    data: 'volume_1',
                    name: 'volume_1'
                },
                {
                    data: 'volume_2',
                    name: 'volume_2'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#id_palet').val(id);
        $('.tambah').show();
        $('.edit').hide();
    }

    $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_palet').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#stok_1').text(0);
        $('#stok_2').text(0);
        $('#volume_1').val('');
        $('#volume_2').val('');
    });

    $('#barang').on('change', function() {
        var barang = $(this).val();
        var warna = $('#warna').val();
        var gudang = $('#gudang').val();
        $('#warna').val(warna);
        $.get(`palet/get-stok-barang/${barang}/${warna}/${gudang}/${tipe}`, function(data) {
            $('#stok_1').text(data.stok_1);
            $('#stok_2').text(data.stok_2);
            $('#volume_1').val(data.stok_1);
            $('#volume_2').val(data.stok_2);
        });
    });

    $('#gudang').on('change', function() {
        var gudang = $(this).val();
        var barang = $('#barang').val();
        var warna = $('#warna').val();
        $.get(`palet/get-stok-barang/${barang}/${warna}/${gudang}/${tipe}`, function(data) {
            $('#stok_1').text(data.stok_1);
            $('#stok_2').text(data.stok_2);
            $('#volume_1').val(data.stok_1);
            $('#volume_2').val(data.stok_2);
        });
    });

    $('#warna').on('change', function() {
        var warna = $(this).val();
        var barang = $('#barang').val();
        var gudang = $('#gudang').val();
        $.get(`palet/get-stok-barang/${barang}/${warna}/${gudang}/${tipe}`, function(data) {
            $('#stok_1').text(data.stok_1);
            $('#stok_2').text(data.stok_2);
            $('#volume_1').val(data.stok_1);
            $('#volume_2').val(data.stok_2);
        });
    });
</script>
