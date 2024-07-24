<div class="tab-pane active" id="tabInput" role="tabpanel">
    <div class="form-group row">
        <div class="col-md-12">
            @if (Auth::user()->roles_name !== 'validator')
                @if (!$data->validated_at)
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                        onclick="tambah($(this))">
                        <i class="icon md-plus mr-2"></i> Tambah Barang</button>
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
                        <th>Tgl. Potong</th>
                        <th>Mesin</th>
                        <th>No. KIKW</th>
                        <th>No. KIKS</th>
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
                    <input type="hidden" name="id_jahit_p2" id="id_jahit_p2">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="input">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <select class="form-control" name="id_gudang" id="gudang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" id="barang" onchange="getBarang($(this))">
                        </select>
                    </div>
                    <input type="hidden" name="id_mesin" id="id_mesin">
                    <input type="hidden" name="id_barang" id="id_barang">
                    <input type="hidden" name="id_warna" id="id_warna">
                    <input type="hidden" name="id_motif" id="id_motif">
                    <input type="hidden" name="id_beam" id="id_beam">
                    <input type="hidden" name="id_songket" id="id_songket">
                    <input type="hidden" name="id_grade" id="id_grade">
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
        $('#id_jahit_p2').val(id);
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
        selectGudang(`jahit_p2/get-gudang`, {
            tipe: tipe,
            id: id
        });
        $('#barang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `jahit_p2/get-barang`,
                data: function(d) {
                    d.tipe = tipe;
                    d.id = id;
                    d.id_gudang = $('#gudang').val();

                    return d;
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: `${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_grade},${data.tanggal_potong}`,
                                text: `${data.id_mesin ? data.nama_mesin+' | ' : ''}${data.id_beam ? data.nomor_kikw + ' | ' : ''}${data.id_songket ? data.nomor_kiks + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif} | ${data.nama_grade}${data.tanggal_potong ? ' | '+data.tanggal_potong_text:''}`,
                                data: {
                                    id_mesin: data.id_mesin,
                                    id_barang: data.id_barang,
                                    id_warna: data.id_warna,
                                    id_motif: data.id_motif,
                                    id_beam: data.id_beam,
                                    id_songket: data.id_songket,
                                    id_grade: data.id_grade,
                                    tanggal_potong: data.tanggal_potong,
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
    });

    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: `jahit_p2/table/${mode}/${id}/${tipe}`,
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
                    data: 'mesin',
                    name: 'mesin'
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

        if (val) {
            var mesin = val.data.id_mesin;
            var barang = val.data.id_barang;
            var warna = val.data.id_warna;
            var motif = val.data.id_motif;
            var beam = val.data.id_beam;
            var songket = val.data.id_songket;
            var grade = val.data.id_grade;
            var tanggal_potong = val.data.tanggal_potong;

            $('#id_mesin').val(val.data.id_mesin);
            $('#id_barang').val(val.data.id_barang);
            $('#id_warna').val(val.data.id_warna);
            $('#id_motif').val(val.data.id_motif);
            $('#id_beam').val(val.data.id_beam);
            $('#id_songket').val(val.data.id_songket);
            $('#id_grade').val(val.data.id_grade);
            $('#tanggal_potong').val(tanggal_potong);

            var data = {
                tipe: tipe,
                id_mesin: mesin,
                id_barang: barang,
                id_warna: warna,
                id_gudang: gudang,
                id_motif: motif,
                id_beam: beam,
                id_songket: songket,
                id_grade: grade,
                tanggal_potong: tanggal_potong,
                id: id
            };
            getStok(data);
        }

    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        var data = {
            mode: mode,
            id: id
        };
        $.ajax({
            url: `jahit_p2/get-data`,
            data: data,
            type: 'GET',
            success: function(respon) {
                $('#id').val(respon.id);
                $('#tanggal').val(respon.tanggal);
                $(`#gudang`).select2("trigger", "select", {
                    data: {
                        id: respon.id_gudang,
                        text: respon.nama_gudang
                    }
                });
                editgudang = respon.id_gudang;
                $(`#barang`).select2("trigger", "select", {
                    data: {
                        id: `${respon.id_mesin},${respon.id_barang},${respon.id_warna},${respon.id_motif},${respon.id_beam},${respon.id_songket},${respon.id_grade},${respon.tanggal_potong}`,
                        text: `${respon.id_mesin ? respon.nama_mesin+' | ' : ''}${respon.id_beam ? respon.nomor_kikw + ' | ' : ''}${respon.id_songket ? respon.nomor_kiks + ' | ' : ''}${respon.nama_barang} | ${respon.nama_warna} | ${respon.nama_motif} | ${respon.nama_grade}${respon.tanggal_potong ? ' | '+respon.tanggal_potong_text:''}`,
                        data: {
                            id_mesin: respon.id_mesin,
                            id_barang: respon.id_barang,
                            id_warna: respon.id_warna,
                            id_motif: respon.id_motif,
                            id_beam: respon.id_beam,
                            id_songket: respon.id_songket,
                            id_grade: respon.id_grade,
                            tanggal_potong: respon.tanggal_potong,
                        }
                    }
                });
                editbarang =
                    `${respon.id_mesin},${respon.id_barang},${respon.id_warna},${respon.id_motif},${respon.id_beam},${respon.id_songket},${respon.id_grade},${respon.tanggal_potong}`;
                $('#volume_1').val(respon.volume_1);
                $('#volume_2').val(respon.volume_2);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#id_mesin').val('');
        $('#id_barang').val('');
        $('#id_warna').val('');
        $('#id_motif').val('');
        $('#id_beam').val('');
        $('#id_songket').val('');
        $('#id_grade').val('');
        $('#volume_1').val('');
        $('#stok_1').text(0);
        $('#barang').empty();
        $('#gudang').empty();
        $('#tanggal_potong').val('');
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#id_jahit_p2').val(id);
    }

    function getStok(data = {}) {
        $.ajax({
            url: `jahit_p2/get-stok-barang`,
            type: 'GET',
            data: data,
            success: function(respon) {
                var id = $('#id').val();
                if (id == "") {
                    $('#stok_1').text(respon.stok_1);
                    $('#stok_2').text(respon.stok_2);
                    $('#volume_1').val(respon.stok_1);
                    $('#volume_2').val(respon.stok_2);
                } else {
                    var tempbarang = `${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_grade},${data.tanggal_potong}`;
                    if (editgudang != data.id_gudang || editbarang != tempbarang) {
                        $('#stok_1').text(respon.stok_1);
                        $('#stok_2').text(respon.stok_2);
                    } else {
                        $('#stok_1').text(parseFloat(parseFloat(respon.stok_1) + parseFloat($('#volume_1')
                            .val())));
                        $('#stok_2').text(parseFloat(parseFloat(respon.stok_2) + parseFloat($('#volume_2')
                            .val())));
                    }
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
</script>
