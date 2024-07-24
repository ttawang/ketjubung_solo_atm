<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Mesin</th>
                    <th>No. KIKW</th>
                    <th>Barang</th>
                    <th>Warna</th>
                    <th>Motif</th>
                    <th>Kualitas</th>
                    <th>Gudang</th>
                    <th>Volume (Pcs)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
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
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
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
                    <input type="hidden" name="id_mesin" id="id_mesin">
                    <input type="hidden" name="id_barang" id="id_barang">
                    <input type="hidden" name="id_warna" id="id_warna">
                    <input type="hidden" name="id_motif" id="id_motif">
                    <input type="hidden" name="id_beam" id="id_beam">
                    <input type="hidden" name="id_grade_awal" id="id_grade_awal">
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
                            <input type="number" value="" class="form-control number-only" name="volume_1" id="volume_1" required>
                            <input type="hidden" value="4" name="id_satuan_1" id="id_satuan_1">
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kualitas</label>
                        <select class="form-control select2" name="id_grade" id="grade" required>
                            <option value="0">-- pilih kualitas --</option>
                            @foreach ($grade as $i)
                                <option value="{{ $i->id }}">{{ $i->grade }} | {{ $i->alias }}</option>
                            @endforeach
                        </select>
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
        var editgudang = '';
        var editbarang = '';
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
        $('#gudang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `folding/get-gudang`,
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
                url: function(params) {
                    var gudang = $('#gudang').val();
                    if (gudang == '' || gudang == 0 || gudang == null) {
                        return `folding/get-barang/0`;
                    } else {
                        return `folding/get-barang/${gudang}`;
                    }
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: `${item.id_mesin},${item.id_barang},${item.id_warna},${item.id_motif},${item.id_beam},${item.id_grade}`,
                                text: `${item.rel_mesin ? item.rel_mesin.name+' | ' : ''}${item.rel_beam ? item.rel_beam.no_kikw + ' | ' : ''}${item.rel_barang.name} | ${item.rel_warna.alias} | ${item.rel_motif.name} | ${item.rel_grade.grade}`,
                                data: {
                                    id_mesin: item.id_mesin,
                                    id_barang: item.id_barang,
                                    id_warna: item.id_warna,
                                    id_motif: item.id_motif,
                                    id_beam: item.id_beam,
                                    id_grade: item.id_grade,
                                    id_grade_awal: item.id_grade_awal,
                                }
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });

    function table() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: `folding/table`,
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
                    data: 'mesin',
                    name: 'mesin'
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw'
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
                    data: 'grade',
                    name: 'grade'
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

    function getBarang(this_) {
        let val = this_.select2('data')[0];
        var gudang = $('#gudang').val();
        var id = $('#id').val();
        if (val.id != 0) {
            var mesin = val.data.id_mesin;
            var barang = val.data.id_barang;
            var warna = val.data.id_warna;
            var motif = val.data.id_motif;
            var beam = val.data.id_beam;
            if (!id) {
                $('#id_grade_awal').val(val.data.id_grade);
                $('#grade').val(val.data.id_grade).trigger('change');
            } else {
                $('#id_grade_awal').val(val.data.id_grade_awal);
            }

            $('#id_mesin').val(mesin);
            $('#id_barang').val(barang);
            $('#id_warna').val(warna);
            $('#id_motif').val(motif);
            $('#id_beam').val(beam);



            stok(mesin, barang, warna, gudang, motif, beam, val.data.id_grade);
        }

    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        $.get(`folding/get-data/${id}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            $('#id_grade_awal').val(data.id_grade_awal);
            $(`#gudang`).select2("trigger", "select", {
                data: {
                    id: data.id_gudang,
                    text: data.rel_gudang.name
                }
            });
            editgudang = data.id_gudang;
            $(`#barang`).select2("trigger", "select", {
                data: {
                    id: `${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_grade_awal}`,
                    text: `${data.rel_mesin ? data.rel_mesin.name+' | ' : ''}${data.rel_beam ? data.rel_beam.no_kikw + ' | ' : ''}${data.rel_barang.name} | ${data.rel_warna.alias} | ${data.rel_motif.name} | ${data.nama_grade_awal}`,
                    data: {
                        id_mesin: data.id_mesin,
                        id_barang: data.id_barang,
                        id_warna: data.id_warna,
                        id_motif: data.id_motif,
                        id_beam: data.id_beam,
                        id_grade: data.id_grade,
                        id_grade_awal: data.id_grade_awal,
                    }
                }
            });
            $('#id_grade_awal').val(data.id_grade_awal);
            editbarang = `${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_grade_awal}`;
            $(`#grade`).select2("trigger", "select", {
                data: {
                    id: data.id_grade,
                    text: `${data.rel_grade.grade} | ${data.rel_grade.alias}`
                }
            });
            $('#volume_1').val(data.volume_1);
            $('#volume_2').val(data.volume_2);
        });
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#gudang').val(0).trigger('change');
        $('#barang').val(0).trigger('change');
        $('#id_barang').val(0);
        $('#grade').val(0).trigger('change');
        $('#id_grade_awal').val('');
        $('#volume_1').val('');
        $('#stok_1').text(0);
    }

    function getStok(mesin, barang, warna, gudang, motif, beam, grade, id = null) {
        $.get(`folding/get-stok-barang/${mesin}/${barang}/${warna}/${gudang}/${motif}/${beam}/${grade}`, function(data) {
            if (!id) {
                $('#stok_1').text(data.stok_1);
                $('#stok_2').text(data.stok_2);
                $('#volume_1').val(data.stok_1);
                $('#volume_2').val(data.stok_2);
            } else {
                var tempbarang = `${mesin},${barang},${warna},${motif},${beam},${grade}`;
                if (tempbarang != editbarang) {
                    $('#id_grade_awal').val(grade);
                }
                if (editgudang != gudang || editbarang != tempbarang) {
                    $('#stok_1').text(data.stok_1);
                    $('#stok_2').text(data.stok_2);
                } else {
                    $('#stok_1').text(parseFloat(parseFloat(data.stok_1) + parseFloat($('#volume_1').val())));
                    $('#stok_2').text(parseFloat(parseFloat(data.stok_2) + parseFloat($('#volume_2').val())));
                }
            }
        });
    }

    function stok(mesin, barang, warna, gudang, motif, beam, grade) {
        var id = $('#id').val();
        if (id) {
            getStok(mesin, barang, warna, gudang, motif, beam, grade, id);
        } else {
            getStok(mesin, barang, warna, gudang, motif, beam, grade);
        }
    }
</script>
