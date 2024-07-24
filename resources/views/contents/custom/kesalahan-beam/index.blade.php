@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading">&nbsp</div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-1">
                    <label">Beam</label>
                </div>
                <div class="col-md-11">
                    <select class="form-control" name="beam" id="beam"></select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                        onclick="cari($(this))"><i class="icon md-search mr-2"></i> Cari</button>
                    <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
                        onclick="ganti($(this))"> Ganti</button>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
                        <thead>
                            <tr>
                                <th width="30px">No.</th>
                                <th>Gudang</th>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Mesin</th>
                                <th>Grade</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Status</th>
                                <th>Code</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
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
                        <input type="hidden" name="id_beam" id="id_beam">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">No. KIKW</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_no_kikw_lama" id="id_no_kikw_lama">
                                <input type="text" class="form-control" name="no_kikw_lama" id="no_kikw_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="no_kikw_baru" id="no_kikw_baru">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">No. BEAM</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_no_beam_lama" id="id_no_beam_lama">
                                <input type="text" class="form-control" name="no_beam_lama" id="no_beam_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="no_beam_baru" id="no_beam_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Mesin</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_mesin_lama" id="id_mesin_lama">
                                <input type="text" class="form-control" name="mesin_lama" id="mesin_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="mesin_baru" id="mesin_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Barang Lusi</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_barang_lusi_lama" id="id_barang_lusi_lama">
                                <input type="text" class="form-control" name="barang_lusi_lama" id="barang_lusi_lama"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="barang_lusi_baru" id="barang_lusi_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Barang Sarung</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_barang_sarung_lama" id="id_barang_sarung_lama">
                                <input type="text" class="form-control" name="barang_sarung_lama"
                                    id="barang_sarung_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="barang_sarung_baru" id="barang_sarung_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Warna</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_warna_lama" id="id_warna_lama">
                                <input type="text" class="form-control" name="warna_lama" id="warna_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="warna_baru" id="warna_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Motif</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="id_motif_lama" id="id_motif_lama">
                                <input type="text" class="form-control" name="motif_lama" id="motif_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="motif_baru" id="motif_baru">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">Volume</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="volume_lama" id="volume_lama" readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="number" value="" class="form-control number-only" name="volume_baru"
                                    id="volume_baru" required>
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
@endsection
@section('js')
    <script type="text/javascript">
        var tables = '';
        $(function() {
            $('#beam').select2({
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-beam') }}`,
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(data) {
                                return {
                                    id: `${data.id_beam}`,
                                    text: `${data.nama_mesin ? data.nama_mesin+' | ' : ''}${data.no_kikw ? data.no_kikw + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif} (${data.volume_2})`,
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
            $('#no_beam_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'no_beam';

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
            $('#mesin_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'mesin';

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
            $('#barang_lusi_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'barang_lusi';

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
            $('#barang_sarung_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'barang_sarung';

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
            $('#warna_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'warna';

                        return d;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(data) {
                                return {
                                    id: `${data.id}`,
                                    text: `${data.alias}`,
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
            $('#motif_baru').select2({
                dropdownParent: $('#modal-kelola'),
                width: '100%',
                allowClear: true,
                placeholder: "-- pilih --",
                ajax: {
                    url: `{{ url('kesalahan-beam/get-select') }}`,
                    data: function(d) {
                        d.tipe = 'motif';

                        return d;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(data) {
                                return {
                                    id: `${data.id}`,
                                    text: `${data.alias}`,
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
            table();
        });

        function ganti(elem) {
            var tableData = $('#table').DataTable().data();

            if (tableData.length === 0) {
                alert('tabel tidak memiliki data.');
                return;
            }
            var selectedBeam = $('#beam').val();
            var hasNoStatus = false;
            var statusTable = true;
            for (var i = 0; i < tableData.length; i++) {
                if (tableData[i].id_beam.toString() === selectedBeam.toString()) {
                    if (tableData[i].status === 'NO') {
                        hasNoStatus = true;
                        break;
                    }
                } else {
                    statusTable = false;
                    alert('tabel tidak sesuai, klik tombol cari terlebih dahulu');
                    break;
                }
            }
            if (statusTable) {
                if (hasNoStatus) {
                    alert('status belum sesuai.');
                } else {
                    $('#modal-kelola').modal('show');
                    $.ajax({
                        url: `{{ url('kesalahan-beam/get-data') }}`,
                        data: {
                            id_beam: selectedBeam
                        },
                        type: 'GET',
                        success: function(respon) {
                            $('#id_beam').val(respon.id_beam);
                            $('#id_no_kikw_lama').val(respon.id_no_kikw);
                            $('#no_kikw_lama').val(respon.no_kikw);
                            $('#no_kikw_baru').val(respon.no_kikw);
                            $('#id_no_beam_lama').val(respon.id_no_beam);
                            $('#no_beam_lama').val(respon.no_beam);
                            $('#no_beam_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_no_beam,
                                    text: respon.no_beam
                                }
                            });
                            $('#id_mesin_lama').val(respon.id_mesin);
                            $('#mesin_lama').val(respon.nama_mesin);
                            $('#mesin_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_mesin,
                                    text: respon.nama_mesin
                                }
                            });
                            $('#id_barang_lusi_lama').val(respon.id_barang_lusi);
                            $('#barang_lusi_lama').val(respon.nama_barang_lusi);
                            $('#barang_lusi_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_barang_lusi,
                                    text: respon.nama_barang_lusi
                                }
                            });
                            $('#id_barang_sarung_lama').val(respon.id_barang_sarung);
                            $('#barang_sarung_lama').val(respon.nama_barang_sarung);
                            $('#barang_sarung_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_barang_sarung,
                                    text: respon.nama_barang_sarung
                                }
                            });
                            $('#id_warna_lama').val(respon.id_warna);
                            $('#warna_lama').val(respon.nama_warna);
                            $('#warna_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_warna,
                                    text: respon.nama_warna
                                }
                            });
                            $('#id_motif_lama').val(respon.id_motif);
                            $('#motif_lama').val(respon.nama_motif);
                            $('#motif_baru').select2("trigger", "select", {
                                data: {
                                    id: respon.id_motif,
                                    text: respon.nama_motif
                                }
                            });
                            $('#volume_lama').val(respon.jml);
                            $('#volume_baru').val(respon.jml);
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                }
            }
        }

        function closeModal() {
            $('#modal-kelola').modal('hide');
            $('#id_beam').val('');
            $('#id_no_kikw_lama').val('');
            $('#no_kikw_lama').val('');
            $('#no_kikw_baru').val('');
            $('#id_no_beam_lama').val('');
            $('#no_beam_lama').val('');
            $('#no_beam_baru').empty();
            $('#id_mesin_lama').val('');
            $('#mesin_lama').val('');
            $('#mesin_baru').empty();
            $('#id_barang_lusi_lama').val('');
            $('#barang_lusi_lama').val('');
            $('#barang_lusi_baru').empty();
            $('#id_barang_sarung_lama').val('');
            $('#barang_sarung_lama').val('');
            $('#barang_sarung_baru').empty();
            $('#id_warna_lama').val('');
            $('#warna_lama').val('');
            $('#warna_baru').empty();
            $('#id_motif_lama').val('');
            $('#motif_lama').val('');
            $('#motif_baru').empty();
            $('#volume_lama').val('');
            $('#volume_baru').val('');
        }

        function simpan(elem) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            $.ajax({
                url: `{{ url('kesalahan-beam/simpan') }}`,
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
                            text: respon.messages,
                        }).then((result) => {
                            tables.ajax.reload();
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
                            tables.ajax.reload();
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
                        tables.ajax.reload();
                    });
                }
            });
        }


        function cari(elem) {
            tables.ajax.reload();
        }

        function table() {
            tables = $('#table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                searching: false,
                order: [],
                ajax: {
                    url: `{{ url('kesalahan-beam/table') }}`,
                    type: 'GET',
                    data: function(d) {
                        let val = $('#beam').val();
                        if (val) {
                            d.id_beam = val;
                        } else {
                            d.id_beam = null;
                        }

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
                        name: 'tanggal'
                    },
                    {
                        data: 'gudang',
                        name: 'gudang'
                    },
                    {
                        data: 'barang',
                        name: 'barang'
                    },
                    {
                        data: 'mesin',
                        name: 'mesin'
                    },
                    {
                        data: 'grade',
                        name: 'grade'
                    },
                    {
                        data: 'masuk',
                        name: 'masuk'
                    },
                    {
                        data: 'keluar',
                        name: 'keluar'
                    },
                    {
                        data: 'text_status',
                        name: 'text_status'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                ]
            });
        }
    </script>
@endsection
