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
                    <th>No. Kikw</th>
                    <th>No. Beam</th>
                    <th>Loom</th>
                    <th>Barang</th>
                    <th>Warna</th>
                    <th>Motif</th>
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
                    <input type="hidden" name="id_sizing" id="id_sizing">
                    <input type="hidden" name="id_log_stok_penerimaan" id="id_log_stok_penerimaan">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="output">
                    <input type="hidden" name="id_parent" id="id_parent_detail">
                    <input type="hidden" name="id_beam_secondary" id="id_beam_secondary">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                            name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Beam</label>
                        <select class="form-control select2" onchange="changeBeam($(this))" name="id_beam"
                            id="id_beam" required>
                            <option value="0">-- pilih beam --</option>
                        </select>
                    </div>
                    <div class="radio-custom radio-primary" id="checkboxWrapper">
                        <div class="form-group">
                            <input type="radio" onchange="changeTipe($(this))" id="inputRadiosTipe" value="single"
                                name="radioTipe" checked>
                            <label for="inputRadiosTipe" class="mr-30">1 Beam</label>
                            <input type="radio" onchange="changeTipe($(this))" id="inputRadiosTipeMulti"
                                value="multi" name="radioTipe">
                            <label for="inputRadiosTipeMulti">2 Beam</label>
                        </div>
                    </div>
                    <div id="wrapperBeam">
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
    function changeTipe(element) {
        let value = element.val();
        let template = '';
        if (value == 'single') {
            template = `
            <div class="form-group">
                <label>Nomor Beam Baru</label>
                <select class="form-control" name="id_nomor_beam" id="id_nomor_beam_single" required>
                </select>
            </div>`;

            $('#wrapperBeam').html(`${template}`);

            $('#id_nomor_beam_single').select2({
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
        } else {
            template = `
            <p>========================== BEAM 1 =================================</p>
            <div class="form-group">
                <label>Nomor Beam Baru</label>
                <select class="form-control select_nomor_beam" name="id_nomor_beam" id="id_nomor_beam" required>
                </select>
            </div>
            <div class="form-group">
                <label>Volume</label>
                <input type="input" id="inputVolume" class="form-control number-only" name="volume_beam">
            </div>
            <p>========================== BEAM 2 =================================</p>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" class="form-control" name="tanggal2" id="tanggal2" required />
            </div>
            <div class="form-group">
                <label>Nomor Beam Baru</label>
                <select class="form-control select_nomor_beam" id="id_nomor_beam2" name="beam2[id_nomor_beam]" required>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor Kikw Baru</label>
                <input type="input" id="inputNokikw" onkeyup="onCheckNoKikw($(this))" class="form-control" name="beam2[no_kikw]" required>
                <span id="txtAlertNoKikw"></span>
            </div>
            <div class="form-group">
                <label>Mesin</label>
                <select class="form-control" id="mesin" name="beam2[id_mesin]" required>
                </select>
            </div>
            <div class="form-group">
                <label>Motif</label>
                <select class="form-control" id="motif" name="beam2[id_motif]" required>
                </select>
            </div>
            <div class="form-group">
                <label>Volume</label>
                <input type="input" id="inputVolume" class="form-control number-only" name="beam2[volume_beam]" required>
            </div>
            `

            $('#wrapperBeam').html(`${template}`);

            $('.select_nomor_beam').select2({
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
        }

        $('input.number-only').keypress(function(e) {
            var txt = String.fromCharCode(e.which);
            if (!txt.match(/[0-9.,]/)) {
                return false;
            }
        });
    }

    function onCheckNoKikw(element) {
        let param = element.val();
        clearTimeout(timer);
        timer = setTimeout(function validate() {
            $.ajax({
                url: "{{ route('helper.getNomorKikw') }}",
                type: "GET",
                data: {
                    param: param
                },
                success: (data) => {
                    let oldKikw = element.data('no-kikw');
                    if (param == oldKikw) {
                        $('#txtAlertNoKikw').html(
                            `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                        );
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        if (data.length > 0) {
                            $('#txtAlertNoKikw').html(
                                `<small class="text-danger">*) Nomor Kikw sudah digunakan!</small>`
                            );
                            $('#btnSubmit').prop('disabled', true);
                        } else {
                            $('#txtAlertNoKikw').html(
                                `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                            );
                            $('#btnSubmit').prop('disabled', false);
                        }
                    }

                    if (param == '') $('#txtAlertNoKikw').html('');
                }
            })
        }, 800);
    }

    $(function() {
        $('#id_beam').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `sizing/get-barang/${tipe}/${id}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: data.id,
                                text: `${data.mesin} | ${data.rel_nomor_kikw.name} | ${data.rel_nomor_beam.name} | ${data.rel_log_stok_penerimaan_b_l.rel_warna.alias} | ${data.rel_log_stok_penerimaan_b_l.rel_motif.alias}`,
                                id_parent: data.rel_sizing.id
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


    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `sizing/table/${mode}/${id}/${tipe}`,
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
                    data: 'no_kikw',
                    name: 'no_kikw'
                },
                {
                    data: 'no_beam',
                    name: 'no_beam'
                },
                {
                    data: 'mesin',
                    name: 'mesin'
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
                    data: 'motif',
                    name: 'motif'
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
        $('#checkboxWrapper').show();
        $('#id_sizing').val(id);
        $('#inputRadiosTipe').click();
        $('#id_beam').val(0).trigger('change');
        $(`#id_beam`).prop('disabled', false);
        $('#id_beam_secondary').val('');
        $('#id_parent_detail').val('');
        $('#wrapperBeam').html(`<div class="form-group">
            <label>Nomor Beam Baru</label>
            <select class="form-control select2" name="id_nomor_beam" id="id_nomor_beam" required>
            </select>
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
    }

    $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_sizing').val(id);
        // $('#id_beam').val(0).trigger('change');
    });

    function changeBeam(element) {
        let value = element.select2('data')[0];
        $('#id_parent_detail').val(value.id_parent);
    }
</script>
