<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            @if (!$data->validated_at)
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>
            @endif
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Gudang</th>
                    <th>Volume (Kg)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-default btn-sm waves-effect waves-classic" onclick="parent($(this));">
            <i class="icon md-arrow-left mr-2"></i> Kembali
        </button>
        {{-- <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-right" onclick="cetak($(this));">
            <i class="icon md-print mr-2"></i> Cetak
        </button> --}}
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
                    <input type="hidden" name="menu" value="{{ $menu }}">
                    <input type="hidden" name="id_detail" value="{{ $id }}">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control" name="id_gudang" id="gudang" required>
                            <option value="0">-- pilih gudang --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" name="id_barang" id="barang" required>
                            <option value="0">-- pilih barang --</option>
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
                            <input type="hidden" value="2" name="id_satuan_1" id="id_satuan_1">
                            <div class="input-group-append">
                                <div class="input-group-text">Kg</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure" onclick="closeModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanChemical($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var menu = `{{ $menu }}`;
    var id = `{{ $id }}`;
    $(function() {
        var editbarang = '';
        var editgudang = '';
        $('#gudang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `{{ url('production/chemical_finishing/get-gudang/${menu}') }}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id_gudang,
                                text: item.rel_gudang.name
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#barang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `{{ url('production/chemical_finishing/get-barang/${menu}') }}`,
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
                cache: true
            }
        });
    });

    function parent(this_) {
        view()
    }

    function table() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: `{{ url('production/chemical_finishing/table/${menu}/${id}') }}`,
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
                    data: 'gudang',
                    name: 'gudang'
                },
                {
                    data: 'volume_1',
                    name: 'volume_1'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#gudang').val(0).trigger('change');
        $('#barang').val(0).trigger('change');
        $('#stok_1').text(0);
        $('#stok_2').text(0);
        $('#volume_1').val('');
        $('#volume_2').val('');
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function simpanChemical(this_) {
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: `{{ url('production/chemical_finishing/simpan') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(respon) {
                if (respon.success == true) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: respon.message,
                    }).then((result) => {
                        table.ajax.reload();
                    });
                } else {
                    $('#modal-kelola').modal('hide');
                    let errorMessage = '';
                    $.each(respon.messages, function(fieldName, fieldErrors) {
                        errorMessage += fieldErrors[0];
                        if (fieldName !== Object.keys(respon.messages).slice(-1)[0]) {
                            errorMessage += ', ';
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                    }).then((result) => {
                        $('#modal-kelola').modal('show');
                        table.ajax.reload();
                    });
                }
            }
        });
    }

    function hapus(this_) {
        this_.tooltip('dispose');
        Swal.fire({
            title: "Apakah anda yakin ?",
            text: "Data yang dihapus tidak dapat dikembalikan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Close",
            reverseButtons: true,
        }).then(function(result) {
            if (result.value === true) {
                var id = this_.data('id');
                $.ajax({
                    url: `{{ url('production/chemical_finishing/hapus/${id}') }}`,
                    type: 'get',
                    success: function(respon) {
                        if (respon.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: respon.message,
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal dihapus',
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        }
                    }
                });
            }
        });
    }

    $('#barang').on('change', function() {
        var barang = $(this).val();
        var gudang = $('#gudang').val();
        stok(barang, gudang);
    });

    $('#gudang').on('change', function() {
        var gudang = $(this).val();
        var barang = $('#barang').val();
        stok(barang, gudang);
    });

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id_detail = this_.data('id');
        var URL = `{{ url('production/chemical_finishing/get-data/${id_detail}') }}`;
        $.get(URL, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            $('#barang').select2("trigger", "select", {
                data: {
                    id: data.id_barang,
                    text: data.rel_barang.name
                }
            });
            editbarang = data.id_barang;
            $('#gudang').select2("trigger", "select", {
                data: {
                    id: data.id_gudang,
                    text: data.rel_gudang.name
                }
            });
            editgudang = data.id_gudang;
            $('#volume_1').val(data.volume_1);
            $('#volume_2').val(data.volume_2);
        });
    }

    function getStok(barang, gudang, id = null) {
        var URL = `{{ url('production/chemical_finishing/get-stok-barang/${menu}/${barang}/${gudang}') }}`;
        if (barang != 0 && gudang != 0) {
            $.get(URL, function(data) {
                if (!id) {
                    $('#stok_1').text(data.stok_1);
                    $('#stok_2').text(data.stok_2);
                    $('#volume_1').val(data.stok_1);
                    $('#volume_2').val(data.stok_2);
                } else {
                    if (editbarang != barang || editgudang != gudang) {
                        $('#stok_1').text(data.stok_1);
                        $('#stok_2').text(data.stok_2);
                    } else {
                        $('#stok_1').text(parseFloat(parseFloat(data.stok_1) + parseFloat($('#volume_1').val())));
                        $('#stok_2').text(parseFloat(parseFloat(data.stok_2) + parseFloat($('#volume_2').val())));
                    }
                }
            });
        }
    }

    function stok(barang, gudang) {
        var id = $('#id').val();
        if (id) {
            getStok(barang, gudang, id);
        } else {
            getStok(barang, gudang);
        }
    }
</script>
