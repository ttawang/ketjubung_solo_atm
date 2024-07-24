<script type="text/javascript">
    function dateDB(dateString) {
        var dateArray = dateString.split("-");
        var dbDate = dateArray[2] + "-" + dateArray[1] + "-" + dateArray[0];
        return dbDate;
    }

    function validasi(this_) {
        var myModel = this_.data('model');
        var myId = this_.data('id');
        var myStat = this_.data('status');
        var URL = `proses/validasi/${myModel}/${myId}/${myStat}`;
        if (myStat === 'simpan') {
            var myTitle = "Simpan Validasi ?";
            var myText = "Data yang divalidasi tidak dapat diedit maupun dihapus";
            var myBtn = "Validasi";
        } else {
            var myTitle = "Batal Validasi ?";
            var myText = "Data yang divalidasi akan dapat diedit maupun dihapus kembali";
            var myBtn = "Batal Validasi";
        }
        Swal.fire({
            title: myTitle,
            text: myText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: myBtn,
            cancelButtonText: "Close",
            reverseButtons: true,
        }).then(function(result) {
            if (result.value === true) {
                var id = this_.data('id');
                $.ajax({
                    url: URL,
                    type: 'get',
                    success: function(respon) {
                        if (respon.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: respon.message,
                            }).then((result) => {
                                table.ajax.reload(null, false);
                            });
                        } else {
                            $('#modalKelola').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal divalidasi',
                            }).then((result) => {
                                table.ajax.reload(null, false);
                            });
                        }
                    }
                });
            }
        });
    }

    function selectGudang(uri, data = {}) {
        $('#gudang').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            allowClear: true,
            placeholder: "-- pilih --",
            ajax: {
                url: uri,
                data: function(d) {
                    return {
                        ...d,
                        ...data
                    };
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(data) {
                            return {
                                id: data.id_gudang,
                                text: data.nama_gudang
                            };
                        })
                    };
                },
                error: () => {},
                cache: true
            }
        });
    }

    function cetak(this_) {

        Swal.fire({
            title: 'Cetak',
            html: `
                <select name="tipe_cetak" id="tipe_cetak" class="swal2-input">
                    <option value="spk">Surat Perintah Kerja</option>
                    <option value="pspk">Penyelesaian Surat Perintah Kerja</option>
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
            var data = this_.data();
            data.tipe_cetak = result.value;
            var uri = `proses/cetak`;
            window.open(uri + '?' + new URLSearchParams(data), '_blank');
        });
    }

    function terimaSemuaBarangJasaLuar(this_) {
        Swal.fire({
            title: 'Tanggal Terima',
            html: '<input type="date" id="tanggalTerimaSemuaBarangJasaLuar" class="swal2-input">',
            focusConfirm: false,
            preConfirm: () => {
                var tanggal = document.getElementById('tanggalTerimaSemuaBarangJasaLuar').value;
                if (!tanggal) {
                    Swal.showValidationMessage('Tanggal harus diisi');
                }
                return tanggal;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var tanggal = result.value;
                var data = this_.data();
                data.tanggal = tanggal;
                $.ajax({
                    url: `proses/terima-semua-barang-jasa-luar`,
                    data: data,
                    type: 'GET',
                    success: function(respon) {
                        if (respon.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: respon.message,
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: respon.message,
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON.messages,
                        }).then((result) => {
                            table.ajax.reload();
                        });
                    }
                });
            }
        });
    }

    function simpan(this_) {
        var form = $('#form')[0];
        var formData = new FormData(form);
        var addData = this_.data();
        for (var key in addData) {
            if (addData.hasOwnProperty(key)) {
                formData.append(key, addData[key]);
            }
        }
        $.ajax({
            url: `proses/simpan`,
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
                        table.ajax.reload();
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
                        table.ajax.reload();
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
                    table.ajax.reload();
                });
            }
        });
    }

    let jqueryConfirmRetur;

    function retur(element, isEdit = false) {
        let objects = element.data();
        let tanggal = objects.tanggal || "{{ date('Y-m-d') }}";
        jqueryConfirmRetur = $.confirm({
            title: '<i class="icon md-assignment mr-2"></i> Form Retur Jasa Luar',
            content: `<form action="{{ route('helper.returInspect') }}" onsubmit="submitReturInspect(event, $(this));" method="POST" class="formInput" style="height: 400px;">
                    <input type="hidden" name="input[id_retur_inspekting]" id="id_inspecting_retur" value="${objects.idInspecting || ''}">
                    <input type="hidden" name="input[model_inspect]" value="${objects.modelInspect}">
                    <input type="hidden" name="input[model_jasa_luar]" value="${objects.modelJasaLuar}">
                    <input type="hidden" name="input[primary_id]" value="id_${objects.tableParent}">
                    <input type="hidden" name="input[id_log_stok_inspect_keluar]" value="${objects.idLogstokInspectKeluar}">
                    <input type="hidden" name="input[id_log_stok_inspect_masuk]" value="${objects.idLogstokInspectMasuk}">
                    <input type="hidden" name="input[id_log_stok_jasa_luar]" value="${objects.idLogstokJasaLuar}">
                    <input type="hidden" name="data">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="input[tanggal]" value="${tanggal}" required>
                    </div>
                    <div class="form-group">
                        <label>Gudang</label>
                        <input type="text" class="form-control" value="Gudang Finishing" disabled>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select id="select_barang_retur" data-placeholder="-- Pilih Barang --" onchange="getBarangRetur($(this))"
                        class="form-control select2" required></select>
                        <div class="text-right">
                            Stok: <span class="font-size-12" id="stok_retur">0</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Volume</label>
                        <input type="text" class="form-control number-only" name="input[volume]" id="volume" value="${objects.volume || ''}" required>
                    </div>
                </form>`,
            columnClass: 'col-md-4 col-md-offset-4',
            backgroundDismiss: true,
            closeIcon: true,
            typeAnimated: true,
            type: 'dark',
            theme: 'material',
            onOpenBefore: function() {
                this.showLoading(true);
            },
            buttons: {
                cancel: function() {
                    //close
                },
                formSubmit: {
                    text: 'Submit',
                    btnClass: 'btn-blue',
                    action: function() {
                        const form = $(`.formInput`);
                        let reportValidity = form[0].reportValidity();
                        if (!reportValidity) return false;
                        form.submit();
                    }
                },
            },
            onContentReady: function() {
                let jc = this;
                this.$content.find('input.number-only').keypress(function(e) {
                    var txt = String.fromCharCode(e.which);
                    if (!txt.match(/[0-9.,]/)) {
                        return false;
                    }
                });

                $('#select_barang_retur').select2({
                    dropdownParent: $('.formInput'),
                    width: '100%',
                    allowClear: true,
                    placeholder: "-- pilih --",
                    ajax: {
                        url: `${objects.route}`,
                        beforeSend: () => {},
                        data: function(d) {
                            d.id_gudang = 6;
                            return d;
                        },
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(data) {
                                    let idParent = codeKirim = codeJasaLuar =
                                        codeInspect = '';
                                    if (objects.modelJasaLuar == 'P1Detail') {
                                        idParent = data.id_p1;
                                        codeKirim = 'JS';
                                        codeJasaLuar = 'P1';
                                        codeInspect = 'IP1';
                                    }

                                    if (objects.modelJasaLuar == 'P2Detail') {
                                        idParent = data.id_p2;
                                        codeKirim = 'DR';
                                        codeJasaLuar = 'P2';
                                        codeInspect = 'IP2';
                                    }

                                    if (objects.modelJasaLuar == 'FinishingCabutDetail') {
                                        idParent = data.id_finishing_cabut;
                                        codeKirim = 'IP1';
                                        codeJasaLuar = 'FC';
                                        codeInspect = 'IFC';
                                    }

                                    return {
                                        id: `${idParent},${data.id_mesin},${data.id_barang},${data.id_warna},${data.id_motif},${data.id_beam},${data.id_songket},${data.id_grade},${data.tanggal_potong}`,
                                        text: `${data.nomor} | ${data.id_mesin ? data.nama_mesin+' | ' : ''}${data.id_beam ? data.nomor_kikw + ' | ' : ''}${data.id_songket ? data.nomor_kiks + ' | ' : ''}${data.nama_barang} | ${data.nama_warna} | ${data.nama_motif} | ${data.nama_grade}${data.tanggal_potong ? ' | '+data.tanggal_potong_text:''}`,
                                        data: {
                                            id_parent: idParent,
                                            id_mesin: data.id_mesin,
                                            id_barang: data.id_barang,
                                            id_warna: data.id_warna,
                                            id_motif: data.id_motif,
                                            id_beam: data.id_beam,
                                            id_songket: data.id_songket,
                                            id_grade: data.id_grade,
                                            id_grade_awal: data.id_grade,
                                            id_gudang: data.id_gudang,
                                            code_kirim: codeKirim,
                                            code_jasa_luar: codeJasaLuar,
                                            code_inspect: codeInspect,
                                            tanggal_potong: data.tanggal_potong,
                                        }
                                    };
                                }),
                                pagination: {
                                    more: data.next_page_url ? true : false
                                }
                            };
                        },
                        cache: true,
                    }
                });

                if (isEdit) {
                    $.ajax({
                        url: `{{ route('helper.getSelectedBarangInspecting') }}`,
                        beforeSend: () => {},
                        data: {
                            id: objects.idInspecting,
                            model: objects.modelInspect
                        },
                        type: 'GET',
                        success: function(response) {
                            editgudang = 6;
                            editbarang =
                                `${response['data'].id_parent},${response['data'].id_mesin},${response['data'].id_barang},${response['data'].id_warna},${response['data'].id_motif},${response['data'].id_beam},${response['data'].id_songket},${response['data'].id_grade},${response['data'].id_grade_awal}`;

                            if (objects.modelJasaLuar == 'P1Detail') {
                                response['data'].code_kirim = 'JS';
                                response['data'].code_jasa_luar = 'P1';
                                response['data'].code_inspect = 'IP1';
                            }

                            if (objects.modelJasaLuar == 'P2Detail') {
                                response['data'].code_kirim = 'DR';
                                response['data'].code_jasa_luar = 'P2';
                                response['data'].code_inspect = 'IP2';
                            }

                            if (objects.modelJasaLuar == 'FinishingCabutDetail') {
                                response['data'].code_kirim = 'IP1';
                                response['data'].code_jasa_luar = 'FC';
                                response['data'].code_inspect = 'IFC';
                            }

                            $(`#select_barang_retur`).select2("trigger", "select", {
                                data: response
                            });
                        }
                    });
                } else {
                    jc.hideLoading(true);
                }

                let checkOnceClick = 0;
                $('.jconfirm-box').trigger('focus');
                $(document).keydown(function(event) {
                    if (event.keyCode == 13 && checkOnceClick == 0) {
                        event.preventDefault();
                        jc.$$formSubmit.trigger('click');
                        ++checkOnceClick;
                    }
                });
            }
        });
    }

    function getBarangRetur(this_) {
        let val = this_.select2('data')[0];
        var id = $('#id_inspecting_retur').val();
        if (val) {
            var id_parent = val.data.id_parent;
            var mesin = val.data.id_mesin;
            var barang = val.data.id_barang;
            var warna = val.data.id_warna;
            var motif = val.data.id_motif;
            var beam = val.data.id_beam;
            var songket = val.data.id_songket;
            var id_gudang = val.data.id_gudang;
            var codeKirim = val.data.code_kirim;
            var codeJasaLuar = val.data.code_jasa_luar;
            var codeInspect = val.data.code_inspect;
            var tanggal_potong = val.data.tanggal_potong;

            if (id == "") {
                var grade = val.data.id_grade;
                var grade_awal = val.data.id_grade;
            } else {
                var grade = val.data.id_grade;
                var grade_awal = val.data.id_grade_awal;
            }

            let checkStok = {
                id_parent: id_parent,
                id_mesin: mesin,
                id_barang: barang,
                id_warna: warna,
                id_gudang: id_gudang,
                id_motif: motif,
                id_beam: beam,
                id_songket: songket,
                id_grade: grade,
                id_grade_awal: grade_awal,
                tanggal_potong: tanggal_potong,
                id: id,
            };

            let params = checkStok;
            params.code_kirim = codeKirim;
            params.code_jasa_luar = codeJasaLuar;
            params.code_inspect = codeInspect;
            $('input[name="data"]').val(JSON.stringify(params));

            getStok(checkStok, true);
        }

    }

    function submitReturInspect(event, this_) {
        event.preventDefault();
        $.ajax({
            url: this_.attr('action'),
            type: this_.attr('method'),
            data: this_.serialize(),
            success: (message) => {
                toastr.success(message);
                table.ajax.reload();
            }
        })
    }
</script>
