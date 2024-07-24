<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            @if (!$data->validated_at)
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                    onclick="tambah($(this));"><i class="icon md-plus mr-2"></i> Tambah Barang</button>
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
                    <th>Volume (Cones)</th>
                    <th>Volume (Kg)</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_warping" id="id_warping">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="sisa">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select class="form-control select2" name="jenis" id="jenis" required>
                            <option value="">-- pilih jenis --</option>
                            <option value="1">Benang Bisa Digunakan Lagi</option>
                            <option value="2">Benang Tidak Bisa Digunakan Lagi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control select2" name="id_gudang" id="gudang" required>
                            <option value="0">-- pilih gudang --</option>
                            {{-- @foreach ($gudang as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control select2" name="id_barang" id="barang" required>
                            <option value="0">-- pilih barang --</option>
                            {{-- @foreach ($barang as $i)
                                <option value="{{ $i->id_barang }}">{{ $i->relBarang->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Warna</label>
                        <select class="form-control select2" name="id_warna" id="warna" required>
                            <option value="0">-- pilih warna --</option>
                            {{-- @foreach ($warna as $i)
                                <option value="{{ $i->id_warna }}">{{ $i->relWarna->alias }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume </label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_1"
                                id="volume_1">
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
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_2"
                                id="volume_2" required>
                            <input type="hidden" value="2" name="id_satuan_2" id="id_satuan_2">
                            <div class="input-group-append">
                                <div class="input-group-text">Kg</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure"
                    onclick="closeModal()">Batal</button>
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
                url: `warping/get-gudang/${tipe}/${id}`,
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
                url: `warping/get-barang/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id_barang,
                                text: item.rel_barang.name
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
                url: `warping/get-warna/${tipe}/${id}`,
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
            stateSave:true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `warping/table/${mode}/${id}/${tipe}`,
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
                    data: 'jenis',
                    name: 'jenis',
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    render: (data, display, row) => {
                        return (row['returned_at'] != null) ? '<span class="badge badge-outline badge-success">Sudah Diretur</span>' : data;
                    }
                }
            ]
        });
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#id_warping').val(id);
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#id_warping').val(id);
        var id_detail = this_.data('id');
        $.get(`warping/get-data/${id_detail}/${mode}`, function(data) {
            let jenis = data.code == 'BBWS' ? 1 : 2;
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            $('#jenis').val(jenis).trigger('change');
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

    /* $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_warping').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }); */

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        // $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#id_warping').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }
</script>
