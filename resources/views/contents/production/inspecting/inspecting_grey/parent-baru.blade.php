<div class="modal fade modal-fade-in-scale-up" id="modal-kelola-baru" aria-hidden="true" role="dialog" tabindex="-1"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModalBaru()">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title-baru">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form-baru" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id_baru">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tanggal</label>
                            <input type="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm"
                                onchange="" name="tanggal" id="tanggal_baru" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label>Gudang</label>
                            <select class="form-control" name="id_gudang" id="gudang_baru">
                                <option value="5">Gudang Inspecting</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select class="form-control" id="barang_baru" onchange="getBarangBaru($(this))">
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 1</label>
                            <input type="number" class="form-control form-control-sm" name="group_1" id="group_1_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_a"
                                id="group_1_grade_a_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_b"
                                id="group_1_grade_b_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_1_grade_c"
                                id="group_1_grade_c_baru">
                        </div>
                        <input type="hidden" name="group_1_grade_total" id="group_1_grade_total_baru">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 2</label>
                            <input type="number" class="form-control form-control-sm" name="group_2" id="group_2_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_a"
                                id="group_2_grade_a_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_b"
                                id="group_2_grade_b_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_2_grade_c"
                                id="group_2_grade_c_baru">
                        </div>
                        <input type="hidden" name="group_2_grade_total" id="group_2_grade_total_baru">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Group 3</label>
                            <input type="number" class="form-control form-control-sm" name="group_3" id="group_3_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>A</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_a"
                                id="group_3_grade_a_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>B</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_b"
                                id="group_3_grade_b_baru">
                        </div>
                        <div class="form-group col-md-2">
                            <label>C</label>
                            <input type="number" class="form-control form-control-sm" name="group_3_grade_c"
                                id="group_3_grade_c_baru">
                        </div>
                        <input type="hidden" name="group_3_grade_total" id="group_3_grade_total_baru">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label>Volume</label>
                            </div>
                            <div class="col text-right">
                                Stok : <span id="stok_1_baru" class="text-warning">0</span>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input type="number" value="" class="form-control number-only" name="volume_1"
                                id="volume_1_baru" readonly>
                            <input type="hidden" value="4" name="id_satuan_1" id="id_satuan_1_baru">
                            <div class="input-group-append">
                                <div class="input-group-text">Pcs</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Panjang Sarung</label>
                            <input type="number" value="" class="form-control number-only"
                                name="panjang_sarung" id="panjang_sarung">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Keterangan</label>
                            <input type="text" value="" class="form-control" name="keterangan"
                                id="keterangan">
                        </div>
                    </div>
                </form>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close-baru" class="btn btn-default btn-pure"
                    onclick="closeModalBaru()">Batal</button>
                <button type="button" id="btn-simpan-baru" class="btn btn-primary"
                    onclick="simpanBaru($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $('#barang_baru').select2({
            dropdownParent: $('#modal-kelola-baru'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: `{{ url('production/inspect_grey_2/get-barang') }}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: `${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_mesin},${data.tanggal_potong}`,
                                text: `${data.nama_mesin} | ${data.no_beam} | ${data.no_kikw} | ${data.id_songket ? data.no_kiks + '|' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif}${data.tanggal_potong ? ' | ' + data.tanggal_potong_text : ''}`,
                                data: {
                                    no_kiks: data.no_kiks,
                                    id_barang: data.id_barang,
                                    id_warna: data.id_warna,
                                    id_motif: data.id_motif,
                                    id_beam: data.id_beam,
                                    id_songket: data.id_songket,
                                    id_mesin: data.id_mesin,
                                    total: data.total,
                                    code: data.code,
                                    tanggal_potong: data.tanggal_potong,
                                },
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                error: () => {},
                cache: true
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                var $result = $(
                    `<span>${data.text.replace(data.data.no_kiks, `<span style="color: blue;">${data.data.no_kiks}</span>`)}</span>`
                );
                return $result;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

    });

    function getBarangBaru(elem) {
        let val = elem.select2('data')[0];
        var total = 0;
        if (val) {
            total = val.data.total;
        }
        $('#stok_1_baru').text(total);
    }

    function getTotalBaru() {
        var group_1 = parseFloat($('#group_1_baru').val()) || 0;
        var group_2 = parseFloat($('#group_2_baru').val()) || 0;
        var group_3 = parseFloat($('#group_3_baru').val()) || 0;

        var total = group_1 + group_2 + group_3;
        $('#volume_1_baru').val(total);
    }

    function getTotalGroupBaru(id) {
        var group = parseFloat($(`#group_${id}_baru`).val()) || 0;
        var grade_a = parseFloat($(`#group_${id}_grade_a_baru`).val()) || 0;
        var grade_b = parseFloat($(`#group_${id}_grade_b_baru`).val()) || 0;
        var grade_c = parseFloat($(`#group_${id}_grade_c_baru`).val()) || 0;

        var total = grade_a + grade_b + grade_c;

        if (total > group) {
            $(`#group_${id}_grade_a_baru`).val(0);
            $(`#group_${id}_grade_b_baru`).val(0);
            $(`#group_${id}_grade_c_baru`).val(0);

            alert(`Jumlah potongan tiap Grade pada Group ${id} tidak boleh melebihi jumlah potongan Group ${id}`);
        }
        $(`#group_${id}_grade_total_baru`).val(total)

    }

    $('#group_1_baru, #group_2_baru, #group_3_baru').on('keyup', function() {
        getTotalBaru();
    });

    $('#group_1_grade_a_baru, #group_1_grade_b_baru, #group_1_grade_c_baru').on('keyup', function() {
        getTotalGroupBaru(1);
    });
    $('#group_2_grade_a_baru, #group_2_grade_b_baru, #group_2_grade_c_baru').on('keyup', function() {
        getTotalGroupBaru(2);
    });
    $('#group_3_grade_a_baru, #group_3_grade_b_baru, #group_3_grade_c_baru').on('keyup', function() {
        getTotalGroupBaru(3);
    });


    function tambahBaru(elem) {
        $('.modal-title-baru').text('Tambah');
        $('#modal-kelola-baru').modal('show');
        $('#barang_baru').empty();
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#id_baru').val('');
        $('#id_group_1_baru').val('');
        $('#id_group_2_baru').val('');
        $('#id_group_3_baru').val('');
        $('#group_1_baru').val(0);
        $('#group_1_grade_a_baru').val(0);
        $('#group_1_grade_b_baru').val(0);
        $('#group_1_grade_c_baru').val(0);
        $('#group_2_baru').val(0);
        $('#group_2_grade_a_baru').val(0);
        $('#group_2_grade_b_baru').val(0);
        $('#group_2_grade_c_baru').val(0);
        $('#group_3_baru').val(0);
        $('#group_3_grade_a_baru').val(0);
        $('#group_3_grade_b_baru').val(0);
        $('#group_3_grade_c_baru').val(0);
        $('#volume_1_baru').val(0);
        $('#stok_1_baru').text(0);
        $('#group_1_grade_total_baru').val(0);
        $('#group_2_grade_total_baru').val(0);
        $('#group_3_grade_total_baru').val(0);
    }

    function editBaru(id) {
        $('.modal-title-baru').text('Edit');
        $('#modal-kelola-baru').modal('show');
        $.get(`{{ url('production/inspect_grey_2/get-data/${id}') }}`, function(data) {
            $('#id_baru').val(data.id);
            $('#tanggal_baru').val(data.tanggal);
            $(`#barang_baru`).select2("trigger", "select", {
                data: {
                    id: `${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_mesin},${data.tanggal_potong}`,
                    text: `${data.nama_mesin} | ${data.no_beam} | ${data.no_kikw} | ${data.id_songket ? data.no_kiks+'|' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif}${data.tanggal_potong ? ' | ' + data.tanggal_potong_text : ''}`,
                    data: {
                        no_kiks: data.no_kiks,
                        id_barang: data.id_barang,
                        id_warna: data.id_warna,
                        id_motif: data.id_motif,
                        id_beam: data.id_beam,
                        id_songket: data.id_songket,
                        id_mesin: data.id_mesin,
                        total: data.total,
                        code: data.code_keluar,
                        tanggal_potong: data.tanggal_potong,
                    },
                }
            });
            $('#volume_1_baru').val(data.volume_1);
            $('#group_1_baru').val(data.group_1);
            $('#group_1_grade_a_baru').val(data.group_1_grade_a);
            $('#group_1_grade_b_baru').val(data.group_1_grade_b);
            $('#group_1_grade_c_baru').val(data.group_1_grade_c);
            $('#group_2_baru').val(data.group_2);
            $('#group_2_grade_a_baru').val(data.group_2_grade_a);
            $('#group_2_grade_b_baru').val(data.group_2_grade_b);
            $('#group_2_grade_c_baru').val(data.group_2_grade_c);
            $('#group_3_baru').val(data.group_3);
            $('#group_3_grade_a_baru').val(data.group_3_grade_a);
            $('#group_3_grade_b_baru').val(data.group_3_grade_b);
            $('#group_3_grade_c_baru').val(data.group_3_grade_c);


            $('#group_1_grade_total_baru').val(data.group_1_grade_total);
            $('#group_2_grade_total_baru').val(data.group_2_grade_total);
            $('#group_3_grade_total_baru').val(data.group_3_grade_total);
            $('#panjang_sarung').val(data.panjang_sarung);
            $('#keterangan').val(data.keterangan);
        });
    }

    function closeModalBaru() {
        $('#modal-kelola-baru').modal('hide');
        $('#barang_baru').empty();
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#id_baru').val('');
        $('#id_group_1_baru').val('');
        $('#id_group_2_baru').val('');
        $('#id_group_3_baru').val('');
        $('#group_1_baru').val(0);
        $('#group_1_grade_a_baru').val(0);
        $('#group_1_grade_b_baru').val(0);
        $('#group_1_grade_c_baru').val(0);
        $('#group_2_baru').val(0);
        $('#group_2_grade_a_baru').val(0);
        $('#group_2_grade_b_baru').val(0);
        $('#group_2_grade_c_baru').val(0);
        $('#group_3_baru').val(0);
        $('#group_3_grade_a_baru').val(0);
        $('#group_3_grade_b_baru').val(0);
        $('#group_3_grade_c_baru').val(0);
        $('#volume_1_baru').val(0);
        $('#stok_1_baru').text(0);
        $('#group_1_grade_total_baru').val(0);
        $('#group_2_grade_total_baru').val(0);
        $('#group_3_grade_total_baru').val(0);
        $('#panjang_sarung').val('');
        $('#keterangan').val('');
    }

    function simpanBaru(this_) {
        var form = $('#form-baru')[0];
        var formData = new FormData(form);
        var barang = $('#barang_baru').select2('data')[0];
        if (barang) {
            for (var key in barang.data) {
                if (barang.data.hasOwnProperty(key)) {
                    formData.append(`barang[${key}]`, barang.data[key]);
                }
            }
        }
        var id = formData.get('id');
        var uri = (id) ? 'update' : 'simpan';
        $.ajax({
            url: `{{ url('production/inspect_grey_2/${uri}') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(respon) {
                if (respon.success == true) {
                    closeModalBaru();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: respon.message,
                    }).then((result) => {
                        table.ajax.reload();
                    });
                } else {
                    $('#modal-kelola-baru').modal('hide');
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
                        $('#modal-kelola-baru').modal('show');
                        table.ajax.reload();
                    });
                }
            }
        });
    }
</script>
