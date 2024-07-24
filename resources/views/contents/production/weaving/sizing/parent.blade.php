<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-parent">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>No. SPK</th>
                    <th>Vendor</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
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
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="mode" id="mode" value="parent">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                            name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>No. SPK</label>
                        <input type="text" class="form-control" name="no_sizing" id="no_sizing">
                    </div>
                    <div class="form-group">
                        <label>Vendor</label>
                        <select class="form-control select2" name="id_supplier" id="supplier" required>
                            <option value="0">-- pilih vendor --</option>
                            {{-- @foreach ($supplier as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" cols="30" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure"
                    data-dismiss="modal">Batal</button>
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
        $('#supplier').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `sizing/get-supplier`,
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
    $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#no_sizing').val('');
        $('#supplier').val(0).trigger('change');
        $('#catatan').val('');
    });

    function tambah(this_) {
        $('#checkboxWrapper').show();
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#checkboxWrapper').hide();
        var id = this_.data('id');
        $.get(`sizing/get-data/${id}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            if (typeof(data.id_sizing) !== 'undefined') {
                if (data.id_sizing != '') {
                    $('#id_sizing').val(data.id_sizing);
                    $(`#id_beam`).select2("trigger", "select", {
                        data: {
                            id: data.id_beam,
                            text: `${data.rel_mesin_history_latest?.rel_mesin?.name} | ${data.through_nomor_kikw.name} | ${data.through_nomor_beam.name} | ${data.rel_log_stok_penerimaan_b_l?.rel_warna?.alias} | ${data.rel_log_stok_penerimaan_b_l?.rel_motif?.alias}`
                        }
                    });

                    $('#id_log_stok_penerimaan').val(data.id_log_stok_penerimaan);
                }
            } else {
                $('#no_sizing').val(data.no_sizing);
                $('#supplier').val(data.supplier).trigger('change');
                $('#catatan').val(data.catatan);
            }
        });
    }

    function editTerima(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#checkboxWrapper').hide();
        var id = this_.data('id');
        let checkBeamPrimary = this_.data('check-beam') == 'YA';
        let checkCountParent = this_.data('count-parent') > 1;
        $.get(`sizing/get-data/${id}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            if (typeof(data.id_sizing) !== 'undefined') {
                if (data.id_sizing != '') {
                    $('#id_sizing').val(data.id_sizing);
                    if (checkCountParent) {
                        if (checkBeamPrimary) {
                            $('input[name="radioTipe"]').val('multi-single');
                            $('#wrapperBeam').html(`<div class="form-group">
                                <label>Nomor Beam Baru</label>
                                <select class="form-control select_nomor_beam" name="id_nomor_beam" id="id_nomor_beam" required></select>
                            </div>
                            <div class="form-group">
                                <label>Volume</label>
                                <input type="input" id="inputVolume" class="form-control number-only" value="${data.rel_log_stok_penerimaan.volume_masuk_2}" name="volume_beam">
                            </div>`);

                            $('#id_nomor_beam').select2({
                                placeholder: "-- pilih nomor beam baru --",
                                width: '100%',
                                closeOnSelect: true,
                                dropdownParent: $('#modal-kelola'),
                                ajax: {
                                    url: "{{ route('helper.getNomorBeam') }}",
                                    dataType: 'JSON',
                                    beforeSend: () => {},
                                    data: function(params) {
                                        let objects = {
                                            param: $.trim(params.term),
                                            page: params.page || 1,
                                            finish: 1
                                        };
                                        return objects;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data['data'],
                                            pagination: data['pagination']
                                        };
                                    },
                                    delay: 300,
                                    complete: () => {},
                                    error: () => {}
                                }
                            })

                            $(`#id_nomor_beam`).select2("trigger", "select", {
                                data: {
                                    id: data.through_nomor_beam.id,
                                    text: `${data.through_nomor_beam.name}`
                                }
                            });
                        } else {
                            $('input[name="radioTipe"]').val('multi');
                            $('#wrapperBeam').html(`<div class="form-group">
                                <label>Nomor Beam Baru</label>
                                <select class="form-control select_nomor_beam" id="id_nomor_beam2" name="id_nomor_beam" required></select>
                            </div>
                            <div class="form-group">
                                <label>Nomor Kikw Baru</label>
                                <input type="hidden" name="id_nomor_kikw" value="${data.through_nomor_kikw.id}">
                                <input type="input" id="inputNokikw" onkeyup="onCheckNoKikw($(this))" class="form-control" value="${data.through_nomor_kikw.name}" name="no_kikw" required>
                                <span id="txtAlertNoKikw"></span>
                            </div>
                            <div class="form-group">
                                <label>Mesin</label>
                                <select class="form-control" id="mesin" name="id_mesin" required></select>
                            </div>
                            <div class="form-group">
                                <label>Motif</label>
                                <select class="form-control" id="motif" name="id_motif" required></select>
                            </div>
                            <div class="form-group">
                                <label>Volume</label>
                                <input type="input" id="inputVolume" class="form-control number-only" value="${data.rel_log_stok_penerimaan.volume_masuk_2}" name="volume_beam" required>
                            </div>`);

                            $('#id_nomor_beam2').select2({
                                placeholder: "-- pilih nomor beam baru --",
                                width: '100%',
                                closeOnSelect: true,
                                dropdownParent: $('#modal-kelola'),
                                ajax: {
                                    url: "{{ route('helper.getNomorBeam') }}",
                                    dataType: 'JSON',
                                    beforeSend: () => {},
                                    data: function(params) {
                                        let objects = {
                                            param: $.trim(params.term),
                                            page: params.page || 1,
                                            finish: 1
                                        };
                                        return objects;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data['data'],
                                            pagination: data['pagination']
                                        };
                                    },
                                    delay: 300,
                                    complete: () => {},
                                    error: () => {}
                                }
                            })

                            $(`#id_nomor_beam2`).select2("trigger", "select", {
                                data: {
                                    id: data.through_nomor_beam.id,
                                    text: `${data.through_nomor_beam?.name}`
                                }
                            });

                            $('#mesin').select2({
                                placeholder: "-- pilih mesin loom --",
                                dropdownParent: $('#modal-kelola'),
                                width: '100%',
                                ajax: {
                                    url: `warping/get-mesin/output`,
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

                            $(`#mesin`).select2("trigger", "select", {
                                data: {
                                    id: data.rel_log_stok_penerimaan_b_l.id_mesin,
                                    text: `${data.rel_log_stok_penerimaan_b_l?.nama_mesin}`
                                }
                            });

                            $('#motif').select2({
                                placeholder: "-- pilih nomor motif --",
                                dropdownParent: $('#modal-kelola'),
                                width: '100%',
                                ajax: {
                                    url: `warping/get-motif/output`,
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

                            $(`#motif`).select2("trigger", "select", {
                                data: {
                                    id: data.rel_log_stok_penerimaan_b_l.id_motif,
                                    text: `${data.rel_log_stok_penerimaan_b_l?.rel_motif?.alias}`
                                }
                            });
                        }
                    } else {
                        $('input[name="radioTipe"]').val('single');
                        $('#wrapperBeam').html(`<div class="form-group">
                            <label>Nomor Beam Baru</label>
                            <select class="form-control select2" name="id_nomor_beam" id="id_nomor_beam" required></select>
                        </div>`);

                        $('#id_nomor_beam').select2({
                            placeholder: "-- pilih nomor beam baru --",
                            width: '100%',
                            closeOnSelect: true,
                            dropdownParent: $('#modal-kelola'),
                            ajax: {
                                url: "{{ route('helper.getNomorBeam') }}",
                                dataType: 'JSON',
                                beforeSend: () => {},
                                data: function(params) {
                                    let objects = {
                                        param: $.trim(params.term),
                                        page: params.page || 1,
                                        finish: 1
                                    };
                                    return objects;
                                },
                                processResults: function(data) {
                                    return {
                                        results: data['data'],
                                        pagination: data['pagination']
                                    };
                                },
                                delay: 300,
                                complete: () => {},
                                error: () => {}
                            }
                        })

                        $(`#id_nomor_beam`).select2("trigger", "select", {
                            data: {
                                id: data.through_nomor_beam.id,
                                text: `${data.through_nomor_beam.name}`
                            }
                        });
                    }

                    $('#id_beam_secondary').val(data.id_beam);
                    $('#id_parent_detail').val(data.id_parent_detail);
                    $(`#id_beam`).prop('disabled', true).select2("trigger", "select", {
                        data: data.parent_beam
                    });

                    $('#id_log_stok_penerimaan').val(data.id_log_stok_penerimaan);

                }
            } else {
                $('#no_sizing').val(data.no_sizing);
                $('#supplier').val(data.supplier).trigger('change');
                $('#catatan').val(data.catatan);
            }
        });
    }

    function tableParent() {
        table = $('#table-parent').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `sizing/table/${mode}`,
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
                    data: 'no_sizing',
                    name: 'no_sizing'
                },
                {
                    data: 'supplier',
                    name: 'supplier'
                },
                {
                    data: 'catatan',
                    name: 'catatan'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }
</script>
