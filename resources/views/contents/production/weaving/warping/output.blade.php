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
                    <th>No. Beam</th>
                    <th>No. KIKW</th>
                    <th>Loom</th>
                    <th>Barang</th>
                    <th>Warna</th>
                    <th>Motif</th>
                    <th>Gudang</th>
                    <th>Volume (Beam)</th>
                    <th>Volume (Pcs)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_warping" id="id_warping">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="output">
                    <input type="hidden" name="id_beam" id="id_beam">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Barang</label>
                            <select class="form-control" id="barang" name="id_barang">
                                <option value="0">-- pilih barang --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>No. Beam</label>
                            <select class="form-control" name="id_nomor_beam" id="no_beam" required>
                                <option value="0">-- pilih no beam --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>No. KIKW</label>
                        <input type="text" class="form-control" name="no_kikw" id="no_kikw">
                    </div>
                    <div class="form-group">
                        <label>Loom</label>
                        <select class="form-control select2" name="id_mesin" id="mesin" required>
                            <option value="0">-- pilih loom --</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Gudang</label>
                            <select class="form-control" name="id_gudang" id="gudang" required>
                                <option value="0">-- pilih gudang --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Warna</label>
                            <select class="form-control" name="id_warna" id="warna" required>
                                <option value="0">-- pilih warna --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Motif</label>
                            <select class="form-control" name="id_motif" id="motif" required>
                                <option value="0" data-motif="0">-- pilih motif --</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" value="1" class="form-control number-only" name="volume_1" id="volume_1" readonly>
                    <input type="hidden" value="3" name="id_satuan_1" id="id_satuan_1">

                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume</label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_2" id="volume_2" required>
                            <input type="hidden" value="4" name="id_satuan_2" id="id_satuan_2">
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure" onclick="closeModal()">Batal</button>
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
        $('#no_beam').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `warping/get-nomor-beam`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name,
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
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
                                id: item.id,
                                text: `${item.name} | ${item.rel_tipe.name}`
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
                                id: item.id,
                                text: item.alias
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });

        $('#motif').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `warping/get-motif/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.alias
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });

        $('#mesin').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `warping/get-mesin/${mode}`,
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
                    name: 'tanggal',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'no_beam',
                    name: 'no_beam',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'barang',
                    name: 'barang',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'warna',
                    name: 'warna',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'motif',
                    name: 'motif',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'volume_1',
                    name: 'volume_1',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'volume_2',
                    name: 'volume_2',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }
            ]
        });
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#id_warping').val(id);
        var id_detail = this_.data('id');
        $.get(`warping/get-data/${id_detail}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#id_beam').val(data.id_beam);
            $('#tanggal').val(data.tanggal);
            if (data.rel_beam.rel_nomor_beam) {
                $('#no_beam').select2("trigger", "select", {
                    data: {
                        id: data.rel_beam.id_nomor_beam,
                        text: data.rel_beam.rel_nomor_beam.name
                    }
                });
            }
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
            $('#mesin').select2("trigger", "select", {
                data: {
                    id: data.id_mesin,
                    text: data.rel_mesin.name
                }
            });
            $('#warna').select2("trigger", "select", {
                data: {
                    id: data.id_warna,
                    text: data.rel_warna.alias
                }
            });
            $('#motif').select2("trigger", "select", {
                data: {
                    id: data.id_motif,
                    text: data.rel_motif.alias
                }
            });
            $('#volume_2').val(data.volume_2);
            $('#no_kikw').val(data.rel_beam.rel_nomor_kikw.name);
        });
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#id_warping').val(id);
    }

    /* $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_warping').val(id);
        $('#no_beam').val(0).trigger('change');
        $('#no_kikw').val('');
        $('#no_kikw_songket').val(0).trigger('change');
        $('#mesin').val(0).trigger('change');
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_2').val('');
    }); */
    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#id_beam').val('');
        // $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#id_warping').val(id);
        $('#no_beam').val(0).trigger('change');
        $('#no_kikw').val('');
        $('#mesin').val(0).trigger('change');
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_2').val('');
    }
</script>
