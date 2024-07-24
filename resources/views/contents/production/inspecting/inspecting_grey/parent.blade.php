<div class="form-row">
    <div class="form-group col-md-4">
        <label>tanggal</label>
        <input type="date" value="" class="form-control form-control-sm" onchange="" name="tgl" id="tgl"
            required />
    </div>
    <div class="form-group col-md-4">
        <label>KIKW</label>
        <select id="kikw" style="width: 100%;" class="form-select" name="kikw">
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Barang</label>
        <select id="barang" style="width: 100%;" class="form-select" name="barang">
        </select>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            {{-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah Lama</button> --}}
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                onclick="tambahBaru($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>
        @endif
        <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
            onclick="filter($(this))"><i class="icon md-search mr-2"></i> Filter</button>
        <button type="button" class="btn btn-danger btn-sm waves-effect waves-classic float-left mr-2"
            onclick="cetak($(this))"><i class="icon md-print mr-2"></i> Cetak</button>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-parent">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Tgl. Potong</th>
                    <th>No. Beam</th>
                    <th>No. KIKW</th>
                    <th>No. KIKS</th>
                    <th>No. Loom</th>
                    <th>Barang</th>
                    <th>Potongan</th>
                    <th>Group (A, B, C)</th>
                    <th>Panjang Sarung</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
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
                <h4 class="modal-title">Form Lama</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_group_1" id="id_group_1">
                    <input type="hidden" name="id_group_2" id="id_group_2">
                    <input type="hidden" name="id_group_3" id="id_group_3">
                    <input type="hidden" name="mode" id="mode" value="parent">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm"
                            onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>No. KIKW</label>
                            <select class="form-control form-control-sm select2" name="beam" id="beam"
                                onchange="getBeam($(this))">
                                <option value="0">-- pilih kikw --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{-- <label>Barang</label> --}}
                            <div class="row">
                                <div class="col">
                                    <label>Barang</label>
                                </div>
                                <div class="col text-right">
                                    <span id="nama_beam" class="text-warning"></span>
                                </div>
                            </div>
                            <select class="form-control form-control-sm select2" name="id_barang" id="id_barang">
                                <option value="0">-- pilih barang --</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Group 1</label>
                            <input type="number" class="form-control form-control-sm" name="group_1" id="group_1">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Group 2</label>
                            <input type="number" class="form-control form-control-sm" name="group_2" id="group_2">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Group 3</label>
                            <input type="number" class="form-control form-control-sm" name="group_3" id="group_3">
                        </div>
                    </div> --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 1</label>
                            <input type="number" class="form-control form-control-sm" name="group_1"
                                id="group_1">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_a"
                                id="group_1_grade_a">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_b"
                                id="group_1_grade_b">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_c"
                                id="group_1_grade_c">
                        </div>
                        <input type="hidden" name="group_1_grade_total" id="group_1_grade_total">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 2</label>
                            <input type="number" class="form-control form-control-sm" name="group_2"
                                id="group_2">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_a"
                                id="group_2_grade_a">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_b"
                                id="group_2_grade_b">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_c"
                                id="group_2_grade_c">
                        </div>
                        <input type="hidden" name="group_2_grade_total" id="group_2_grade_total">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 3</label>
                            <input type="number" class="form-control form-control-sm" name="group_3"
                                id="group_3">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_a"
                                id="group_3_grade_a">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_b"
                                id="group_3_grade_b">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_c"
                                id="group_3_grade_c">
                        </div>
                        <input type="hidden" name="group_3_grade_total" id="group_3_grade_total">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Total</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok" class="text-warning">0</span>
                            </div>
                        </div>
                        <input type="number" class="form-control form-control-sm" name="total" id="total"
                            readonly>
                    </div>
                    <input type="hidden" id="id_lusi" name="id_lusi">
                    <input type="hidden" id="id_tenun" name="id_tenun">
                    <input type="hidden" id="id_beam" name="id_beam">
                    <input type="hidden" id="id_mesin" name="id_mesin">
                    <input type="hidden" id="id_motif" name="id_motif">
                    <input type="hidden" id="id_warna" name="id_warna">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure"
                    onclick="closeModal()">Batal</button>
                <button type="button" id="btn-simpan" class="btn btn-primary"
                    onclick="simpan($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

@include('contents.production.inspecting.inspecting_grey.parent-baru')

<script type="text/javascript">
    var editbarang = '';

    $('#beam').select2({
        dropdownParent: $('#modal-kelola'),
        width: '100%',
        allowClear: true,
        placeholder: '-- pilih kikw --',
        ajax: {
            url: 'inspect_grey/get-beam',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                var temp = data.data.map(function(data) {
                    return {
                        id: data.id_beam,
                        text: `${data.nomor_kikw} | ${data.nomor_beam} | ${data.nama_mesin} | ${data.nama_motif} | ${data.nama_warna}`,
                        data: {
                            id_lusi: data.id,
                            id_tenun: data.id_tenun,
                            id_beam: data.id_beam,
                            id_mesin: data.id_mesin,
                            id_motif: data.id_motif,
                            id_warna: data.id_warna,
                            nama_beam: data.nama_barang,
                        }
                    };
                });

                return {
                    results: temp,
                    pagination: {
                        more: data.next_page_url ? true : false
                    }
                };
            },
            error: () => {},
            cache: true
        }
    }).on('select2:select', function(e) {
        var val = e.params.data;
        var nama_beam = val.data.nama_beam;
        $('#nama_beam').text(`Beam ${nama_beam}`);
    }).on('select2:unselect', function(e) {
        $('#nama_beam').text('');
    });

    $('#id_barang').select2({
        dropdownParent: $('#modal-kelola'),
        width: '100%',
        ajax: {
            url: `inspect_grey/get-barang`,
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                var add = [{
                    id: 0,
                    text: '-- pilih barang --'
                }];
                var temp = []
                if (data && data.length > 0) {
                    var temp = data.map(function(data) {
                        return {
                            id: data.id,
                            text: `${data.name}`,
                        };
                    });
                    temp = add.concat(temp);
                } else {
                    temp = add;
                }
                return {
                    results: temp
                };
            },
            error: () => {},
            cache: true
        }
    });

    $('#kikw').select2({
        width: '100%',
        allowClear: true,
        placeholder: '-- pilih kikw --',
        ajax: {
            url: `inspect_grey/get-beam`,
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                var temp = data.data.map(function(data) {
                    return {
                        id: data.id_beam,
                        text: `${data.nomor_kikw} | ${data.nomor_beam} | ${data.nama_mesin} | ${data.nama_motif} | ${data.nama_warna}`
                    };
                });
                return {
                    results: temp,
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
        width: '100%',
        allowClear: true,
        placeholder: '-- pilih kikw --',
        ajax: {
            url: `inspect_grey/get-barang`,
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                var add = [{
                    id: 'semua',
                    text: '-- semua barang --'
                }];
                var temp = []
                if (data && data.length > 0) {
                    var temp = data.map(function(data) {
                        return {
                            id: data.id,
                            text: `${data.name}`,
                        };
                    });
                    temp = add.concat(temp);
                } else {
                    temp = add;
                }
                return {
                    results: temp
                };
            },
            error: () => {},
            cache: true
        }
    });

    function filter(this_) {
        table.ajax.reload();
    }

    function cetak(this_) {
        tgl = $('#tgl').val();
        if (tgl == '') {
            alert('Tanggal tidak boleh kosong');
        } else {
            Swal.fire({
                title: 'Cetak',
                html: `
                <select name="tipe_cetak" id="tipe_cetak" class="swal2-input">
                    <option value="laporan">Laporan</option>
                    <option value="distribusi">Distribusi</option>
                </select>
            `,
                focusConfirm: false,
                preConfirm: () => {
                    var tipe_cetak = document.getElementById('tipe_cetak').value;
                    if (!tipe_cetak) {
                        Swal.showValidationMessage('tipe cetak harus diisi');
                    }
                    return tipe_cetak;
                }
            }).then((result) => {
                var data = {
                    kikw: $('#kikw').val(),
                    barang: $('#barang').val(),
                    tgl: $('#tgl').val(),
                };
                data.tipe_cetak = result.value;
                var uri = `inspect_grey/cetak`;
                window.open(uri + '?' + new URLSearchParams(data), '_blank');
            });
        }
    }

    function table() {
        table = $('#table-parent').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            responsive: true,
            autoWidth: false,
            stateSave: true,
            order: [],
            ajax: {
                url: `inspect_grey/table/${mode}`,
                type: 'GET',
                data: function(d) {
                    d.barang = $('#barang').val();
                    d.kikw = $('#kikw').val();
                    d.tgl = $('#tgl').val();
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
                    data: 'tanggal_potong',
                    name: 'tanggal_potong'
                },
                {
                    data: 'no_beam',
                    name: 'no_beam'
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw'
                },
                {
                    data: 'no_kiks',
                    name: 'no_kiks'
                },
                {
                    data: 'no_loom',
                    name: 'no_loom'
                },
                {
                    data: 'barang',
                    name: 'barang'
                },
                {
                    data: 'potongan',
                    name: 'potongan'
                },
                {
                    data: 'pergroup',
                    name: 'pergroup'
                },
                {
                    data: 'panjang_sarung',
                    name: 'panjang_sarung'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
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
        $('#id_group_1').val('');
        $('#id_group_2').val('');
        $('#id_group_3').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#beam').empty();
        $('#id_barang').val(0).trigger('change');
        $('#group_1').val(0);
        $('#group_1_grade_a').val(0);
        $('#group_1_grade_b').val(0);
        $('#group_1_grade_c').val(0);
        $('#group_2').val(0);
        $('#group_2_grade_a').val(0);
        $('#group_2_grade_b').val(0);
        $('#group_2_grade_c').val(0);
        $('#group_3').val(0);
        $('#group_3_grade_a').val(0);
        $('#group_3_grade_b').val(0);
        $('#group_3_grade_c').val(0);
        $('#total').val(0);
        $('#stok').text(0);
        $('#group_1_grade_total').val(0);
        $('#group_2_grade_total').val(0);
        $('#group_3_grade_total').val(0);
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah Lama');
        $('#modal-kelola').modal('show');
        $('#group_1').val(0);
        $('#group_1_grade_a').val(0);
        $('#group_1_grade_b').val(0);
        $('#group_1_grade_c').val(0);
        $('#group_2').val(0);
        $('#group_2_grade_a').val(0);
        $('#group_2_grade_b').val(0);
        $('#group_2_grade_c').val(0);
        $('#group_3').val(0);
        $('#group_3_grade_a').val(0);
        $('#group_3_grade_b').val(0);
        $('#group_3_grade_c').val(0);
        $('#total').val(0);
        $('#group_1_grade_total').val(0);
        $('#group_2_grade_total').val(0);
        $('#group_3_grade_total').val(0);
    }

    function edit(this_) {
        var id_inspecting_grey = this_.data('id_inspecting_grey');
        if (id_inspecting_grey) {
            editBaru(id_inspecting_grey);
        } else {
            $('.modal-title').text('Edit Lama');
            $('#modal-kelola').modal('show');
            var id = this_.data('id');
            $.get(`inspect_grey/get-data/${id}`, function(data) {
                $('#id').val(data.id);
                $('#id_group_1').val(data.id_group_1);
                $('#id_group_2').val(data.id_group_2);
                $('#id_group_3').val(data.id_group_3);
                $('#tanggal').val(data.tanggal);
                $(`#beam`).select2("trigger", "select", {
                    data: {
                        id: data.id_beam,
                        text: `${data.rel_beam.rel_nomor_kikw.name} | ${data.rel_beam.rel_nomor_beam.name} | ${data.rel_mesin.name} | ${data.rel_motif.alias} | ${data.rel_warna.alias}`,
                        data: {
                            id_lusi: data.id_lusi_detail,
                            id_tenun: data.id_tenun,
                            id_beam: data.id_beam,
                            id_mesin: data.id_mesin,
                            id_motif: data.id_motif,
                            id_warna: data.id_warna,
                            nama_beam: data.rel_barang.name,
                        }
                    }
                });
                editbarang = `${data.id_beam}`;
                $(`#id_barang`).select2("trigger", "select", {
                    data: {
                        id: data.id_barang,
                        text: `${data.rel_barang.name}`
                    }
                });
                $('#group_1').val(data.group_1);
                $('#group_1_grade_a').val(data.group_1_grade_a);
                $('#group_1_grade_b').val(data.group_1_grade_b);
                $('#group_1_grade_c').val(data.group_1_grade_c);
                $('#group_2').val(data.group_2);
                $('#group_2_grade_a').val(data.group_2_grade_a);
                $('#group_2_grade_b').val(data.group_2_grade_b);
                $('#group_2_grade_c').val(data.group_2_grade_c);
                $('#group_3').val(data.group_3);
                $('#group_3_grade_a').val(data.group_3_grade_a);
                $('#group_3_grade_b').val(data.group_3_grade_b);
                $('#group_3_grade_c').val(data.group_3_grade_c);
                $('#total').val(data.volume_1);

                $('#group_1_grade_total').val(parseFloat(data.group_1_grade_a) + parseFloat(data
                        .group_1_grade_b) +
                    parseFloat(data.group_1_grade_c));
                $('#group_2_grade_total').val(parseFloat(data.group_2_grade_a) + parseFloat(data
                        .group_2_grade_b) +
                    parseFloat(data.group_2_grade_c));
                $('#group_3_grade_total').val(parseFloat(data.group_3_grade_a) + parseFloat(data
                        .group_3_grade_b) +
                    parseFloat(data.group_3_grade_c));
            });
        }
    }

    function getTotal() {
        var group_1 = parseFloat($('#group_1').val()) || 0;
        var group_2 = parseFloat($('#group_2').val()) || 0;
        var group_3 = parseFloat($('#group_3').val()) || 0;

        var total = group_1 + group_2 + group_3;
        $('#total').val(total);
    }

    function getTotalGroup(id) {
        var group = parseFloat($(`#group_${id}`).val()) || 0;
        var grade_a = parseFloat($(`#group_${id}_grade_a`).val()) || 0;
        var grade_b = parseFloat($(`#group_${id}_grade_b`).val()) || 0;
        var grade_c = parseFloat($(`#group_${id}_grade_c`).val()) || 0;

        var total = grade_a + grade_b + grade_c;

        if (total > group) {
            $(`#group_${id}_grade_a`).val(0);
            $(`#group_${id}_grade_b`).val(0);
            $(`#group_${id}_grade_c`).val(0);
            alert(`Jumlah potongan tiap Grade pada Group ${id} tidak boleh melebihi jumlah potongan Group ${id}`);
        }
        $(`#group_${id}_grade_total`).val(total)

    }

    $('#group_1, #group_2, #group_3').on('keyup', function() {
        getTotal();
    });

    $('#group_1_grade_a, #group_1_grade_b, #group_1_grade_c').on('keyup', function() {
        getTotalGroup(1);
    });
    $('#group_2_grade_a, #group_2_grade_b, #group_2_grade_c').on('keyup', function() {
        getTotalGroup(2);
    });
    $('#group_3_grade_a, #group_3_grade_b, #group_3_grade_c').on('keyup', function() {
        getTotalGroup(3);
    });

    function getBeam(this_) {
        var val = this_.select2('data')[0];
        var id = $('#id').val();
        if (val) {
            var id_lusi = val.data.id_lusi;
            var id_tenun = val.data.id_tenun;
            var id_beam = val.data.id_beam;
            var id_mesin = val.data.id_mesin;
            var id_motif = val.data.id_motif;
            var id_warna = val.data.id_warna;

            $('#id_lusi').val(id_lusi);
            $('#id_tenun').val(id_tenun);
            $('#id_beam').val(id_beam);
            $('#id_mesin').val(id_mesin);
            $('#id_motif').val(id_motif);
            $('#id_warna').val(id_warna);
            stok(id_beam);
        }

    }

    function getStok(beam, id = null) {
        $.get(`inspect_grey/get-stok/${beam}`, function(data) {
            if (!id) {
                $('#stok').text(data.stok);
            } else {
                var tempbarang = `${beam}`;
                if (editbarang != tempbarang) {
                    $('#stok').text(data.stok);
                } else {
                    $('#stok').text(parseFloat(parseFloat(data.stok) + parseFloat($('#total').val())));
                }
            }
        });
    }

    function stok(beam) {
        var id = $('#id').val();
        if (id) {
            getStok(beam, id);
        } else {
            getStok(beam);
        }
    }
</script>
