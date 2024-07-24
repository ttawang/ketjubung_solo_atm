<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            @if (!$data->validated_at)
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this));"><i class="icon md-plus mr-2"></i> Tambah Barang</button>
            @endif
        @endif
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
                    <th>Volume (Pcs)</th>
                    {{-- <th>Volume (Cones)</th> --}}
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
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
                    <input type="hidden" name="id_leno" id="id_leno">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="output">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control" name="id_gudang" id="gudang" required>
                            <option value="0">-- pilih gudang --</option>
                            {{-- @foreach ($gudang as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" name="id_barang" id="barang" required>
                            <option value="0">-- pilih barang --</option>
                            {{-- @foreach ($barang as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Warna</label>
                        <select class="form-control" name="id_warna" id="warna" required>
                            <option value="0">-- pilih warna --</option>
                            {{-- @foreach ($warna as $i)
                                <option value="{{ $i->id_warna }}">{{ $i->relWarna->alias }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume 1</label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_1" id="volume_1" required>
                            <input type="hidden" value="4" name="id_satuan_1" id="id_satuan_1">
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume 2</label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_2" id="volume_2" required>
                            <input type="hidden" value="2" name="id_satuan_2" id="id_satuan_2">
                            <div class="input-group-append">
                                <div class="input-group-text">Kg</div>
                            </div>
                        </div>
                    </div> --}}
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
        $('#gudang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `leno/get-gudang/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });

        $('#barang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `leno/get-barang/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });
        $('#warna').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `leno/get-warna/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id_warna,
                                text: item.rel_warna.alias
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });
    });

    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `leno/table/${mode}/${id}/${tipe}`,
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
                /* {
                    data: 'volume_2',
                    name: 'volume_2'
                }, */
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
        $('#id_leno').val(id);
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#id_leno').val(id);
        var id_detail = this_.data('id');
        $.get(`leno/get-data/${id_detail}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            $('#barang').select2("trigger", "select", {
                data: {
                    id: data.id_barang,
                    text: data.rel_barang.name
                }
            });
            $('#gudang').select2("trigger", "select", {
                data: {
                    id: data.id_gudang,
                    text: data.rel_gudang.name
                }
            });
            $('#warna').select2("trigger", "select", {
                data: {
                    id: data.id_warna,
                    text: data.rel_warna.alias
                }
            });
            $('#volume_1').val(data.volume_1);
            $('#volume_2').val(data.volume_2);
        });
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#id_leno').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }
    /* $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_leno').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }); */
</script>
