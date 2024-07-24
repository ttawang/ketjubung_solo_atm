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
                    <th>Barang</th>
                    <th>Warna</th>
                    <th>Gudang</th>
                    <th>Volume (Pcs)</th>
                    <th>Volume (kg)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
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
            <form class="form-horizontal" id="form" action="{{ route('production.pakan.simpan.shuttle') }}"
                onsubmit="simpanPakanShuttle(event,$(this))" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding-bottom: 20px;">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_pakan" id="id_pakan">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="output">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            onchange="" name="tanggal" id="tanggal" required />
                    </div>

                    <div class="form-group">
                        <label>Gudang</label>
                        <input type="text" value="Gudang Pakan" class="form-control" disabled>
                        {{-- <select class="form-control" name="id_gudang" id="gudang" required>
                            <option value="0">-- pilih gudang --</option>
                        </select> --}}
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Barang (Rappier)</label>
                            <select class="form-control select2" name="id_barang" id="barang" required>
                                <option value="0">-- pilih barang --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Warna</label>
                            <select class="form-control select2" name="id_warna" id="warna" required>
                                <option value="0">-- pilih warna --</option>
                            </select>
                        </div>
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
                            <input type="text" value="" class="form-control number-only" name="volume_1"
                                id="volume_1" required>
                            <div class="input-group-append">
                                <div class="input-group-text">Cones</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume 2</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_2" class="text-warning">0</span>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" value="" onkeyup="convertKgPcs($(this))"
                                class="form-control number-only" name="volume_2" id="volume_2" required>
                            <div class="input-group-append">
                                <div class="input-group-text">Kg</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Palet</label>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" value="" class="form-control number-only" name="pcs"
                                id="jumlah_pcs" readonly>
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-close" class="btn btn-default btn-pure"
                        onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
        $('#gudang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `pakan/get-gudang/${tipe}/${id}`,
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
                url: `pakan/get-barang/${tipe}/${id}`,
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
        $('#warna').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `pakan/get-warna/${tipe}/${id}`,
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

        $('.select2').change(function() {
            getStokRappier({
                code: 'BPR',
                id_gudang: 7,
                id_barang: $('#barang').val(),
                id_warna: $('#warna').val(),
                id_satuan_1: 1,
                id_satuan_2: 2,
            });
        })
    });

    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `pakan/table/${mode}/${id}/${tipe}`,
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
                    data: 'warna',
                    name: 'warna'
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
        $('#id_pakan').val(id);
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        $('#id_pakan').val(id);
        var id_detail = this_.data('id');
        $.get(`pakan/get-data/${id_detail}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            if (data.code == 'BPS') {
                $('#proses').val('shuttle').trigger('change');
            } else {
                $('#proses').val('rappier').trigger('change');
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
            $('#warna').select2("trigger", "select", {
                data: {
                    id: data.id_warna,
                    text: data.rel_warna.alias
                }
            });
            $('#volume_1').val(data.volume_1);
            $('#volume_2').val(data.volume_2);
        });
    }

    /* $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_pakan').val(id);
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }); */

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#id_pakan').val(id);
        // $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#proses').val('rappier').trigger('change');
        $('#barang').val(0).trigger('change');
        $('#gudang').val(0).trigger('change');
        $('#warna').val(0).trigger('change');
        $('#motif').val(0).trigger('change');
        $('#volume_1').val('');
        $('#volume_2').val('');
    }

    function getStokRappier(objects = {}) {
        $.ajax({
            url: "{{ route('helper.getStokBarang') }}",
            type: "POST",
            data: objects,
            success: (response) => {
                $('#stok_1').text(response.stok_utama || 0);
                $('#stok_2').text(response.stok_pilihan || 0);
            }
        })
    }

    function convertKgPcs(element) {
        let valueKg = element.val();
        clearTimeout(timer);
        timer = setTimeout(function calcute() {
            let valuePcs = valueKg / 0.02;
            $('#jumlah_pcs').val(valuePcs);
        }, 800);
    }

    function simpanPakanShuttle(event, this_) {
        event.preventDefault();
        $.ajax({
            url: this_.attr('action'),
            type: this_.attr('method'),
            data: this_.serialize(),
            success: (message) => {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: message,
                }).then((result) => {
                    table.ajax.reload();
                });
            },
            complete: () => {}
        })
    }
</script>
