<div class="form-row">
    <div class="form-group col-md-6">
        <label>SPK</label>
        <select id="spk" style="width: 100%;" class="form-select" name="spk">
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Tanggal</label>
        <input type="date" value="" class="form-control" name="tanggal_filter" id="tanggal_filter" />
    </div>
</div>
<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
            onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>
        <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
            data-model-inspect="InspectP2Detail" data-model-jasa-luar="P2Detail" data-table-parent="p2"
            data-route="inspect_p2/get-barang" onclick="retur($(this))"><i class="icon md-plus mr-2"></i> Tambah (Retur)
        </button>
        <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-left mr-2"
            onclick="filter($(this))"><i class="icon md-search mr-2"></i> Filter</button>
        <button type="button" class="btn btn-secondary btn-sm waves-effect waves-classic float-left mr-2"
            onclick="cetak($(this))"><i class="icon md-print mr-2"></i> Cetak</button>
        {{-- <button type="button" class="btn btn-success btn-sm waves-effect waves-classic float-left mr-2" onclick="cetak($(this))"><i class="icon md-print mr-2"></i> Cetak</button> --}}
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Tgl. Potong</th>
                    <th>Gudang</th>
                    <th data-visible="false">SPK</th>
                    <th>No. KIKW</th>
                    <th>No. KIKS</th>
                    <th>Barang / Warna / Motif / Mesin</th>
                    <th>Kualitas</th>
                    <th>Jenis Cacat</th>
                    <th>Volume (Pcs)</th>
                    {{-- <th>Status</th> --}}
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
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                            name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control" name="id_gudang" id="gudang">
                            <option value="0">-- pilih gudang --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" id="barang" onchange="getBarang($(this))">
                            <option value="0">-- pilih barang --</option>
                        </select>
                    </div>
                    <input type="hidden" name="id_p2" id="id_p2">
                    <input type="hidden" name="id_mesin" id="id_mesin">
                    <input type="hidden" name="id_barang" id="id_barang">
                    <input type="hidden" name="id_warna" id="id_warna">
                    <input type="hidden" name="id_motif" id="id_motif">
                    <input type="hidden" name="id_beam" id="id_beam">
                    <input type="hidden" name="id_songket" id="id_songket">
                    <input type="hidden" name="id_grade_awal" id="id_grade_awal">
                    <input type="hidden" name="tanggal_potong" id="tanggal_potong">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_1" class="text-warning">0</span>
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
                    <div class="form-group">
                        <label>Kualitas</label>
                        <select class="form-control select2" name="id_grade" id="grade" required>
                            @foreach ($grade as $i)
                                <option value="{{ $i->id }}">{{ $i->grade }} | {{ $i->alias }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Cacat</label>
                        <select class="form-control" name="id_kualitas[]" id="kualitas" required multiple>
                            {{-- <option value="0">-- pilih jenis cacat --</option> --}}
                        </select>
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
        var editgudang = '';
        var editbarang = '';
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: '-- pilih spk --',
        });
        $('#spk').select2({
            width: '100%',
            allowClear: true,
            placeholder: '-- semua spk --',
            ajax: {
                url: `inspect_p2/get-spk`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    var temp = data.data.map(function(data) {
                        return {
                            id: data.id,
                            text: `${data.nomor}`,
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
        selectGudang(`inspect_p2/get-gudang`);
        $('#barang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `inspect_p2/get-barang`,
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
                                id: `${data.id_p2},${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_grade},${data.tanggal_potong}`,
                                text: `${data.nomor} | ${data.id_mesin ? data.nama_mesin+' | ' : ''}${data.id_beam ? data.nomor_kikw + ' | ' : ''}${data.id_songket ? data.nomor_kiks + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif} | ${data.nama_grade}${data.tanggal_potong ? ' | '+data.tanggal_potong_text:''}`,
                                data: {
                                    id_p2: data.id_p2,
                                    id_mesin: data.id_mesin,
                                    id_barang: data.id_barang,
                                    id_warna: data.id_warna,
                                    id_motif: data.id_motif,
                                    id_beam: data.id_beam,
                                    id_songket: data.id_songket,
                                    id_grade: data.id_grade,
                                    id_grade_awal: data.id_grade,
                                    tanggal_potong: data.tanggal_potong,
                                }
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                cache: true
            }
        });

        $('#kualitas').select2({
            dropdownParent: $('#modal-kelola'),
            closeOnSelect: false,
            width: '100%',
            ajax: {
                url: `inspect_p2/get-kualitas`,
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function(d) {
                    return {
                        grade: $('#grade').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(data) {
                            return {
                                id: data.id,
                                text: `${data.kode} | ${data.name}`
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });

    function cetak(this_) {
        var data = {
            spk: $('#spk').val() ?? null,
            tanggal: $('#tanggal_filter').val() ?? null,
            proses: 'inspect_p2',
        };
        var uri = `proses/cetak`;
        window.open(uri + '?' + new URLSearchParams(data), '_blank');
    }

    $('#grade').on('change', function() {
        $('#kualitas').val(0).trigger('change');
    });

    function table() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: {
                url: `inspect_p2/table`,
                type: 'GET',
                data: function(d) {
                    d.spk = $('#spk').val();
                    d.tanggal = $('#tanggal_filter').val();
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
                    data: 'gudang',
                    name: 'gudang'
                },
                {
                    data: 'spk',
                    name: 'spk'
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
                    data: null,
                    name: 'barang',
                    render: (data) => {
                        return `${data.barang} / ${data.warna} / ${data.motif} / ${data.mesin}`
                    }
                },
                {
                    data: 'grade',
                    name: 'grade'
                },
                {
                    data: 'kualitas',
                    name: 'kualitas'
                },
                {
                    data: 'volume_1',
                    name: 'volume_1'
                },
                /* {
                    data: null,
                    name: 'status',
                    render: (data) => {
                        let idBeam = data.id_beam || '';

                        let countRetur = data.count_retur || 0;

                        if (countRetur > 0)
                        return `<span class="badge badge-outline badge-danger">Retur Jasa Luar</span>`;

                        if (idBeam != '') {
                            let jumlahInspekting = data.jumlah_finishing_cabut || 0;
                            if (jumlahInspekting > 0) {
                                return `<span class="badge badge-outline badge-primary">Proses Finishing Cabut</span>`;
                            } else {
                                return `<span class="badge badge-outline badge-warning">Belum Finishing Cabut</span>`;
                            }
                        } else {
                            return `-`;
                        }
                    }
                }, */
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function filter(this_) {
        table.ajax.reload();
    }

    function getBarang(this_) {
        let val = this_.select2('data')[0];
        var gudang = $('#gudang').val();
        var id = $('#id').val();
        if (val) {
            var p2 = val.data.id_p2;
            var mesin = val.data.id_mesin;
            var barang = val.data.id_barang;
            var warna = val.data.id_warna;
            var motif = val.data.id_motif;
            var beam = val.data.id_beam;
            var songket = val.data.id_songket;
            var tanggal_potong = val.data.tanggal_potong;

            if (id == "") {
                var grade = val.data.id_grade;
                var grade_awal = val.data.id_grade;
            } else {
                var grade = val.data.id_grade;
                var grade_awal = val.data.id_grade_awal;
            }
            $('#id_grade_awal').val(grade_awal);
            $('#grade').val(grade).trigger('change');
            $('#id_p2').val(p2);
            $('#id_mesin').val(mesin);
            $('#id_barang').val(barang);
            $('#id_warna').val(warna);
            $('#id_motif').val(motif);
            $('#id_beam').val(beam);
            $('#id_songket').val(songket);
            $('#tanggal_potong').val(tanggal_potong);

            var data = {
                id_parent: p2,
                id_mesin: mesin,
                id_barang: barang,
                id_warna: warna,
                id_gudang: gudang,
                id_motif: motif,
                id_beam: beam,
                id_songket: songket,
                id_grade: grade,
                id_grade_awal: grade_awal,
                tanggal_potong: tanggal_potong,
                id: id
            };
            getStok(data);
        }

    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#grade').val(null).trigger('change');
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        var data = {
            id: id
        };
        $.ajax({
            url: `inspect_p2/get-data`,
            data: data,
            type: 'GET',
            success: function(respon) {
                $('#id').val(respon.id);
                $('#tanggal').val(respon.tanggal);
                $('#id_grade_awal').val(respon.id_grade_awal);
                $(`#gudang`).select2("trigger", "select", {
                    data: {
                        id: respon.id_gudang,
                        text: respon.nama_gudang
                    }
                });
                editgudang = respon.id_gudang;
                $(`#barang`).select2("trigger", "select", {
                    data: {
                        id: `${respon.id_p2},${respon.id_mesin},${respon.id_barang},${respon.id_warna},${respon.id_motif},${respon.id_beam},${respon.id_songket},${respon.id_grade_awal},${respon.tanggal_potong}`,
                        text: `${respon.nomor} | ${respon.id_mesin ? respon.nama_mesin+' | ' : ''}${respon.id_beam ? respon.nomor_kikw + ' | ' : ''}${respon.id_songket ? respon.nomor_kiks + ' | ' : ''}${respon.nama_barang} | ${respon.nama_warna} | ${respon.nama_motif} | ${respon.nama_grade_awal}${respon.tanggal_potong ? ' | '+respon.tanggal_potong_text:''}`,
                        data: {
                            id_p2: respon.id_p2,
                            id_mesin: respon.id_mesin,
                            id_barang: respon.id_barang,
                            id_warna: respon.id_warna,
                            id_motif: respon.id_motif,
                            id_beam: respon.id_beam,
                            id_songket: respon.id_songket,
                            id_grade: respon.id_grade,
                            id_grade_awal: respon.id_grade_awal,
                            tanggal_potong: respon.tanggal_potong,
                        }
                    }
                });
                $('#id_grade_awal').val(respon.id_grade_awal);
                editbarang =
                    `${respon.id_p2},${respon.id_mesin},${respon.id_barang},${respon.id_warna},${respon.id_motif},${respon.id_beam},${respon.id_songket},${respon.id_grade},${respon.id_grade_awal},${respon.tanggal_potong}`;
                $(`#grade`).select2("trigger", "select", {
                    data: {
                        id: respon.id_grade,
                        text: `${respon.nama_grade} | ${respon.alias_grade}`
                    }
                });
                $('#kualitas').empty();
                respon.kualitas.forEach(function(data) {
                    var option = new Option(`${data.kode_kualitas} | ${data.nama_kualitas}`, data
                        .id_kualitas, true, true);
                    $('#kualitas').append(option);
                });
                $('#volume_1').val(respon.volume_1);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#id_p2').val('');
        $('#id_barang').val(0);
        $('#kualitas').val(0).trigger('change');
        $('#id_grade_awal').val('');
        $('#volume_1').val('');
        $('#stok_1').text(0);
        $('#barang').empty();
        $('#gudang').empty();
        $('#grade').val(null).trigger('change');
        $('#tanggal_potong').val('');
    }

    function getStok(data = {}, isRetur = false) {
        $.ajax({
            url: `inspect_p2/get-stok-barang`,
            type: 'GET',
            data: data,
            success: function(respon) {
                var id = data.id;
                if (id == "") {
                    if (isRetur) $('#stok_retur').text(respon.stok_1);
                    $('#stok_1').text(respon.stok_1);
                    $('#stok_2').text(respon.stok_2);
                    $('#volume_1').val(respon.stok_1);
                    $('#volume_2').val(respon.stok_2);
                } else {
                    var tempbarang =
                        `${data.id_parent},${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_grade},${data.id_grade_awal},${data.tanggal_potong}`;
                    if (editgudang != data.id_gudang || editbarang != tempbarang) {
                        $('#stok_1').text(respon.stok_1);
                        $('#stok_2').text(respon.stok_2);
                        if (isRetur) $('#stok_retur').text(respon.stok_1);
                    } else {
                        if (isRetur) $('#stok_retur').text(parseFloat(parseFloat(respon.stok_1) +
                            parseFloat($('#volume').val())));
                        $('#stok_1').text(parseFloat(parseFloat(respon.stok_1) + parseFloat($('#volume_1')
                            .val())));
                        $('#stok_2').text(parseFloat(parseFloat(respon.stok_2) + parseFloat($('#volume_2')
                            .val())));
                    }
                }

                if (isRetur) jqueryConfirmRetur.hideLoading(true);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
</script>
