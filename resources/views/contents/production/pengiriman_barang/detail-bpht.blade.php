<div class="panel-heading">
    <div class="panel-title form-group row">
        <div class="col-md-12">
            <div class="col-md-12">
                @if ($data->validated_at != null)
                    <h5 class="text-right">Tanggal Validasi :
                        &nbsp;<em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em><i
                            class="icon md-check-circle ml-2 text-success"></i></h4>
                @endif
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3>
                            <span class="badge badge-outline badge-primary">
                                {{ $data->total ?? 0 }} Pcs
                            </span>
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
</div>

<div class="panel-body">
    <div class="form-group row">
        <div class="col-md-12">
            <div class="form-group row">
                <div class="col-md-12">
                    @if (Auth::user()->roles_id !== 8)
                        <button type="button" data-id="{{ $data->id }}"
                            class="btn btn-primary btn-template btn-sm waves-effect waves-classic float-left mr-2 btn-create"
                            onclick="tambah($(this), true);">
                            <i class="icon md-plus mr-2"></i> Tambah Kirim
                        </button>
                    @endif
                    <button type="button" onclick="tableDetail.ajax.reload();"
                        class="btn btn-default btn-sm waves-effect waves-classic float-right">
                        <i class="icon md-refresh-sync spin btn-refresh mr-2"></i> Refresh
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-detail">
                <thead>
                    <tr>
                        <th width="30px">No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Gudang Asal</th>
                        <th>KIKW</th>
                        <th>KIKS</th>
                        <th>No.Beam</th>
                        <th>No.Loom</th>
                        <th>Motif</th>
                        <th>Warna</th>
                        <th>Pcs</th>
                        <th>Catatan</th>
                        <th>Sisa Beam</th>
                        <th width="70px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <div class="form-group row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-default btn-sm waves-effect waves-classic btn-back"
                        onclick="closeForm($(this));">
                        <i class="icon md-arrow-left mr-2"></i> Kembali
                    </button>
                    @if (Auth::user()->roles_id !== 8)
                        <button type="button" data-model="PengirimanBarang" data-id="{{ $data->id }}"
                            class="btn btn-warning btn-sm waves-effect waves-classic float-right"
                            onclick="cetakForm($(this));">
                            <i class="icon md-print mr-2"></i> Cetak
                        </button>
                    @else
                        @if ($data->validated_at != null)
                            <button type="button"
                                class="btn btn-danger btn-sm waves-effect waves-classic float-right mr-2"
                                data-model="PengirimanBarang" data-id="{{ $data->id }}"
                                data-id_tipe_pengiriman="{{ $data->id_tipe_pengiriman }}" data-state="rollback"
                                data-is-show="detail" onclick="validateForm($(this), true);"><i
                                    class="icon md-refresh-sync-alert mr-2"></i>
                                Batalkan Validasi
                                Form</button>
                        @else
                            <button type="button"
                                class="btn btn-primary btn-sm waves-effect waves-classic float-right mr-2"
                                data-model="PengirimanBarang" data-id="{{ $data->id }}"
                                data-id_tipe_pengiriman="{{ $data->id_tipe_pengiriman }}" data-state="validate"
                                data-is-show="detail" onclick="validateForm($(this));"><i
                                    class="icon md-check mr-2"></i> Validasi
                                Form</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModal($(this))">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_pengiriman_barang" id="id_pengiriman_barang"
                        value="{{ $data->id }}">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control" name="id_gudang" id="gudang">
                            <option value="4">Gudang Weaving</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Beam</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_beam" class="text-warning">0</span>
                            </div>
                        </div>
                        <select class="form-control" id="beam" onchange="getBeam($(this))">
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Songket</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_songket" class="text-warning">0</span>
                            </div>
                        </div>
                        <select class="form-control" id="songket" onchange="getSongket($(this))">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" id="barang" name="id_barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume</label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_1"
                                id="volume_1" required>
                            <input type="hidden" value="4" name="id_satuan_1" id="id_satuan_1">
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
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
<script>
    $(function() {
        tableDetail();
        $('#beam').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `{{ url('production/bpht/get-beam') }}`,
                data: function(d) {
                    d.id_gudang = $('#gudang').val();

                    return d;
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: `${data.id_tenun_detail}`,
                                text: `${data.id_mesin ? data.nama_mesin+' | ' : ''}${data.id_beam ? `${data.no_beam} | ${data.no_kikw }` + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif}`,
                                data: {
                                    id_tenun_detail: data.id_tenun_detail,
                                    id_tenun: data.id_tenun,
                                    id_beam: data.id_beam,
                                    id_barang: data.id_barang,
                                    id_warna: data.id_warna,
                                    id_motif: data.id_motif,
                                    id_mesin: data.id_mesin,
                                    total: data.total,
                                    nama_barang: data.nama_barang,
                                }
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                error: () => {},
                cache: true
            }
        });
        $('#songket').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `{{ url('production/bpht/get-songket') }}`,
                data: function(d) {
                    let val = $('#beam').select2('data')[0];
                    if (val) {
                        d.id_tenun = val.data.id_tenun;
                    } else {
                        d.id_tenun = null;
                    }

                    return d;
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: `${data.id_tenun_detail}`,
                                text: `${data.id_beam ? `${data.no_kikw }` + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif}`,
                                data: {
                                    id_tenun_detail: data.id_tenun_detail,
                                    id_tenun: data.id_tenun,
                                    id_beam: data.id_beam,
                                    id_barang: data.id_barang,
                                    id_warna: data.id_warna,
                                    id_motif: data.id_motif,
                                    id_mesin: data.id_mesin,
                                    total: data.total,
                                }
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                error: () => {},
                cache: true
            }
        });
        $('#barang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `{{ url('production/bpht/get-barang') }}`,
                data: function(d) {
                    let val = $('#beam').select2('data')[0];
                    if (val) {
                        d.nama_barang = val.data.nama_barang;
                    } else {
                        d.nama_barang = null;
                    }

                    return d;
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: `${data.id}`,
                                text: `${data.name}`,
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                error: () => {},
                cache: true
            }
        });
    });

    function validateForm(element, isRollback = false) {
        showConfirmDialog({
            icon: 'icon md-alert-triangle',
            title: 'Konfirmasi Validasi Form?',
            content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
            autoClose: true,
            formButton: (isRollback) ? 'delete' : 'saveorupdate',
            textButton: (isRollback) ? 'Batalkan Validasi' : 'Validasi',
            callback: () => {
                validate({
                    objects: element.data()
                });
            }
        });
    }

    function validate({
        objects: objects,
        callback: callback
    }) {
        $.ajax({
            url: `{{ route('helper.validateForm') }}`,
            type: 'POST',
            data: objects,
            success: (response) => {
                if (callback != null) callback();
                (objects.isShow == 'parent') ? tableAjax.ajax.reload(null, false): goToDetail(response[
                    'id'], objects);
                toastr.success(response['message']);
            }

        })
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        $.get(`{{ url('production/bpht/get-data') }}/${id}`, function(data) {
            $('#id').val(data.id_pengiriman_barang_detail);
            $('#id_pengiriman_barang').val(data.id_pengiriman_barang);
            $('#tanggal').val(data.tanggal);
            $(`#beam`).select2("trigger", "select", {
                data: {
                    id: `${data.id_tenun_detail_beam}`,
                    text: `${data.id_mesin_beam ? data.nama_mesin_beam+' | ' : ''}${data.id_beam_beam ? `${data.no_beam_beam} | ${data.no_kikw_beam }` + ' | ' : ''}${data.nama_barang_beam} | ${data.nama_warna_beam} | ${data.nama_motif_beam}`,
                    data: {
                        id_tenun_detail: data.id_tenun_detail_beam,
                        id_tenun: data.id_tenun,
                        id_beam: data.id_beam_beam,
                        id_barang: data.id_barang_beam,
                        id_warna: data.id_warna_beam,
                        id_motif: data.id_motif_beam,
                        id_mesin: data.id_mesin_beam,
                        total: data.total_beam,
                        nama_barang: data.nama_barang_beam,
                    }
                }
            });
            if (data.id_tenun_detail_songket) {
                $(`#songket`).select2("trigger", "select", {
                    data: {
                        id: `${data.id_tenun_detail_songket}`,
                        text: `${data.id_beam_songket ? `${data.no_kikw_songket }` + ' | ' : ''}${data.nama_barang_songket} | ${data.nama_warna_songket} | ${data.nama_motif_songket}`,
                        data: {
                            id_tenun_detail: data.id_tenun_detail_songket,
                            id_tenun: data.id_tenun,
                            id_beam: data.id_beam_songket,
                            id_barang: data.id_barang_songket,
                            id_warna: data.id_warna_songket,
                            id_motif: data.id_motif_songket,
                            id_mesin: data.id_mesin_songket,
                            total: data.total_songket,
                        }
                    }
                });
            }

            $(`#barang`).select2("trigger", "select", {
                data: {
                    id: data.id_barang_sarung,
                    text: `${data.nama_barang_sarung}`
                }
            });
            $('#volume_1').val(data.jml_sarung);
        });
    }

    function tableDetail() {
        tableDetail = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: true,
            order: [],
            // ajax: `sizing/table`,
            ajax: {
                url: `{{ url('production/bpht/table') }}`,
                type: 'GET', // atau 'POST' tergantung kebutuhan
                data: function(d) {
                    d.id_pengiriman_barang = '{{ $data->id }}';

                    return d;
                }
            },
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
                    data: 'barang',
                    name: 'barang',
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
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'no_kiks',
                    name: 'no_kiks',
                    searchable: false,
                    orderable: false
                }, {
                    data: 'no_beam',
                    name: 'no_beam',
                    searchable: false,
                    orderable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false,
                    orderable: false
                }, {
                    data: 'motif',
                    name: 'motif',
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
                    data: 'jml',
                    name: 'jml',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'sisa_beam',
                    name: 'sisa_beam',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                },
            ]
        });
    }

    function getBeam(elem) {
        let val = elem.select2('data')[0];
        var total = 0;
        if (val) {
            $('#songket').empty();
            $('#barang').empty();
            $('#stok_songket').text(0);
            total = val.data.total;
        } else {
            $('#songket').empty();
            $('#barang').empty();
            $('#stok_songket').text(0);
        }
        $('#stok_beam').text(total);
    }

    function getSongket(elem) {
        let val = elem.select2('data')[0];
        var total = 0;
        if (val) {
            total = val.data.total;
        }
        $('#stok_songket').text(total);
    }

    function tambah(elem) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#volume_1').val('');
        $('#stok_beam').text(0);
        $('#stok_songket').text(0);
        $('#beam').empty();
        $('#songket').empty();
        $('#barang').empty();
    }

    function simpan(elem) {
        var form = $('#form')[0];
        var formData = new FormData(form);
        var beam = $('#beam').select2('data')[0];
        var songket = $('#songket').select2('data')[0];

        var simpan = true;
        if (beam) {
            for (var key in beam.data) {
                if (beam.data.hasOwnProperty(key)) {
                    formData.append(`beam[${key}]`, beam.data[key]);
                }
            }
        }
        if (songket) {
            for (var key in songket.data) {
                if (songket.data.hasOwnProperty(key)) {
                    formData.append(`songket[${key}]`, songket.data[key]);
                }
            }
        }
        var id = formData.get('id');
        var uri = (id) ? 'update' : 'simpan';
        $.ajax({
            url: `{{ url('production/bpht') }}/${uri}`,
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
                        tableDetail.ajax.reload();
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
                        tableDetail.ajax.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#modal-kelola').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON.messages,
                }).then((result) => {
                    $('#modal-kelola').modal('show');
                    tableDetail.ajax.reload();
                });
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
                console.log(id);
                $.ajax({
                    url: `{{ url('production/bpht/hapus') }}`,
                    type: 'get',
                    data: {
                        id: id
                    },
                    success: function(respon) {
                        if (respon.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: respon.message,
                            }).then((result) => {
                                tableDetail.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal dihapus',
                            }).then((result) => {
                                tableDetail.ajax.reload();
                            });
                        }
                    }
                });
            }
        });
    }
</script>
