@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambah', 'refresh']) !!}
                    <button type="button" class="btn btn-secondary btn-sm waves-effect waves-classic float-left mr-2"
                        onclick="cetakAll($(this))"><i class="icon md-print mr-2"></i> Cetak</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDistribusiPakan">
                        <thead>
                            <tr>
                                <th width="30px">#</th>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Tipe</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-loading">
            <div class="loader loader-grill"></div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var keys = {};
        let template = templateInput = "";
        let index = 1;

        function cetakAll(elem) {
            Swal.fire({
                title: 'Cetak',
                html: `
                    <label for="tglAwal">Tanggal Awal:</label>
                    <input type="date" id="tglAwal" class="swal2-input">
                    <br>
                    <label for="tglAkhir">Tanggal Akhir:</label>
                    <input type="date" id="tglAkhir" class="swal2-input">
                `,
                confirmButtonText: 'Cetak',
                focusConfirm: false,
                preConfirm: () => {
                    var tglAwal = document.getElementById('tglAwal').value;
                    var tglAkhir = document.getElementById('tglAkhir').value;

                    if (!tglAwal || !tglAkhir) {
                        Swal.showValidationMessage('Tanggal awal dan akhir harus diisi');
                    }

                    return {
                        tglAwal: tglAwal,
                        tglAkhir: tglAkhir,
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = {
                        tglAwal: result.value.tglAwal,
                        tglAkhir: result.value.tglAkhir,
                    };
                    var uri = `{{ url('production/proses/cetak-distribusi-pakan') }}`;
                    window.open(uri + '?' + new URLSearchParams(data), '_blank');
                }
            });
        }


        $(document).keydown(function(e) {
            keys[e.which] = true;
            if (keys[35]) {
                e.preventDefault();
                $('.btn-create, .btn-submit').click();
            }

            if (keys[36]) {
                e.preventDefault();
                $('.btn-back').click();
            }

            if (keys[113]) {
                e.preventDefault();
                $('.btn-refresh').click();
            }

            e.stopPropagation();
        });

        $(document).keyup(function(e) {
            delete keys[e.which];
        });

        function addFormView(element) {
            let callback = () => {
                $(document).on('focus', '.select2-selection.select2-selection--single', function(e) {
                    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
                });

                $('select[name="id_beam"]').focus();
            };

            addForm(element, true, false, callback);
        }

        $(document).ready(function() {
            tableAjax = $('#tableDistribusiPakan').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: [{
                        data: null,
                        searchable: false,
                        className: 'text-center',
                        render: (render, type, row, meta) => {
                            return `<button type='button' id='btnCetak' data-model='DistribusiPakan' data-id='${row.id}' class='btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic' onclick='cetakForm($(this));'>
                        <i class='icon md-print'></i>
                    </button>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: (render, type, row, meta) => {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: 'tanggal_custom',
                        name: 'tanggal',
                        searchable: false
                    }, {
                        data: 'nomor',
                        name: 'nomor',
                        searchable: false
                    }, {
                        data: 'tipe',
                        name: 'tipe',
                        searchable: false,
                        render: (data) => {
                            let textHtml = data.toUpperCase();
                            if (data == 'shuttle') {
                                return `<span class="badge badge-outline badge-primary">${textHtml}</span>`;
                            } else if (data == 'rappier') {
                                return `<span class="badge badge-outline badge-danger">${textHtml}</span>`;
                            } else {
                                return `<span class="badge badge-outline badge-default">${textHtml}</span>`;
                            }
                        }
                    }, {
                        data: 'catatan',
                        name: 'catatan',
                        searchable: false
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        searchable: false
                    }
                ],
                initComplete: () => {

                }
            })
        })

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailForm') }}/${id}`,
                data: {
                    id: id,
                    model: 'DistribusiPakan'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tableDistribusiPakanDetail').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: `${routeWithParam}`,
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: 'volume_1',
                    name: 'volume_1',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume, data.satuan_utama)}`;
                    }
                }, {
                    data: 'satuan_utama',
                    name: 'satuan_utama',
                    searchable: false
                }, {
                    data: 'volume_2',
                    name: 'volume_2',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume, data.satuan_utama)}`;
                    }
                }, {
                    data: 'satuan_pilihan',
                    name: 'satuan_pilihan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }],
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function changeNoBeam(element) {
            if (element.val() == null) {
                resetFormBeam();
                return;
            }

            let value = element.select2('data')[0];
            let noBeam = value.no_beam || '';
            let idTenun = value.id_tenun;
            $('input[name="id_tenun"]').val(idTenun);

            let idMesin, namaMesin = "";
            if (typeof(value['id_mesin']) !== 'undefined' && typeof(value['mesin']) !== 'undefined') {
                idMesin = value['id_mesin'];
                namaMesin = value['mesin'];
            } else {
                idMesin = value['relMesinHistoryLatest']['id_mesin'];
                namaMesin = value['relMesinHistoryLatest']['rel_mesin']['name'];
            }

            let tipePraTenun = value.tipe_pra_tenun || '';
            let isSizing = value.is_sizing || '';
            $('#wrapperFieldNoBeam').html('');
            if (noBeam != '') {
                $('#wrapperFieldNoBeam').html(`
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">No. Beam: </label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="${noBeam}" readonly />
                    </div>
                </div>`);
            }

            $('#wrapperFieldMesin').html('');
            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Mesin: </label>
                    <div class="col-md-9">
                        <input type="hidden" name="id_mesin" value="${idMesin}">
                        <input type="text" class="form-control" value="${namaMesin}" readonly />
                    </div>
                </div>`);
            }

            $('#wrapperFieldTipePraTenun').html('');
            $('#wrapperFieldSizing').html('');
            if (tipePraTenun != '') {
                let sizingTemplate = '';
                if (value.is_sizing != null && value.is_sizing != '') {
                    sizingTemplate = `
                    <div class="col-md-4">
                    <div class="form-group form-material row">
                        <label class="col-md-6 col-form-label">Sizing: </label>
                        <div class="col-md-6">
                            <input type="text" name="is_sizing" class="form-control" value="${value.is_sizing}" readonly />
                        </div>
                    </div></div>`
                }

                $('#wrapperFieldTipePraTenun').html(`
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-material row">
                            <label class="col-md-5 col-form-label">Tipe Pra Tenun: </label>
                            <div class="col-md-7">
                                <input type="text" name="tipe_pra_tenun" class="form-control" value="${tipePraTenun}" readonly />
                            </div>
                        </div>
                    </div>
                    ${sizingTemplate}
                </div>`);
            }
            $('#select_barang').focus();
            // selectedBarangPakan(element.val());
        }

        function selectedBarangPakan(idBeam) {
            let code = $('input[name="code"]').val();
            $.ajax({
                url: `{{ url('helper/selectedBarangPakan') }}/${idBeam}/${code}`,
                type: "GET",
                success: (response) => {
                    $('#wrapperFormInput').html('');
                    (response.select_barang.length > 0) ? selectedOption(response): $('#select_barang').empty()
                        .val('');
                }
            })
        }

        function changeBarang(element) {
            let data = element.select2('data')[0];
            let tipe = $('input[name="tipe"]').val();
            let classDanger = data.stok_utama == 0 || data.stok_pilihan == 0 ? 'text-danger' : '';
            let label =
                `<span class='${classDanger}'>Stok : ${data.stok_utama} ${data.nama_satuan_1}/${data.stok_pilihan} ${data.nama_satuan_2}</span>`;
            addFormQty({
                stokVolume1: `${data.stok_utama} ${data.nama_satuan_1}`,
                stokVolume2: `${data.stok_pilihan} ${data.nama_satuan_2}`,
                idSatuan1: data.id_satuan_1,
                idSatuan2: data.id_satuan_2,
                namaSatuan1: data.nama_satuan_1,
                namaSatuan2: data.nama_satuan_2,
                namaBarang: data.nama_barang,
                idGudang: data.id_gudang,
                idBarang: data.id_barang,
                idWarna: data.id_warna,
                code: data.code,
                labelStok: label,
                tipe: tipe
            });
        }

        function addFormQty(objects = {}) {
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Jumlah Pakan',
                content: `<div class="form-group">
                        <label>Volume (${objects.namaSatuan1})</label>
                        <input type="text" class="form-control number-only" id="qty1" required>
                        <div class="text-right">
                            <span class="font-size-12">Stok: ${objects.stokVolume1}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Volume (${objects.namaSatuan2})</label>
                        <input type="text" class="form-control number-only" id="qty2" required>
                        <div class="text-right">
                            <span class="font-size-12">Stok: ${objects.stokVolume2}</span>
                        </div>
                    </div>`,
                columnClass: 'col-md-4 col-md-offset-4',
                typeAnimated: true,
                type: 'dark',
                theme: 'material',
                onOpenBefore: function() {
                    this.showLoading(true);
                },
                buttons: {
                    cancel: {
                        text: 'Cancel [ESC]',
                        action: () => $('#select_barang').focus()
                    },
                    submit: {
                        text: 'Submit [ENTER]',
                        btnClass: 'btn-blue',
                        action: function() {
                            let qty1 = $('#qty1').val();
                            let qty2 = $('#qty2').val();
                            template = '';
                            templateInput = `
                                <div class="input-group mb-2">
                                    <input type="text" name="input[${index}][volume_1]" class="form-control number-only col-md-8 mr-2" value="${qty1}" class="form-control" required>
                                    <input type="hidden" name="input[${index}][id_satuan_1]" value="${objects.idSatuan1}">
                                    <input type="text" class="form-control col-md-4" value="${objects.namaSatuan1}" disabled>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="input[${index}][volume_2]" class="form-control number-only col-md-8 mr-2" value="${qty2}" class="form-control" required>
                                    <input type="hidden" name="input[${index}][id_satuan_2]" value="${objects.idSatuan2}">
                                    <input type="text" class="form-control col-md-4" value="${objects.namaSatuan2}" disabled>
                                </div>`;

                            template += `<div class="col-md-3" id="wrapperItem${index}">
                                <div class="form-group">
                                    <a href="javascript:void(0);" onclick="$('#wrapperItem${index}').remove();" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic float-right"><i class="icon md-close"></i></a>
                                    <label><b>${objects.namaBarang}</b> <br> ${objects.labelStok}</label>
                                    <input type="hidden" name="input[${index}][code]" value="${objects.code}">
                                    <input type="hidden" name="input[${index}][id_gudang]" value="${objects.idGudang}">
                                    <input type="hidden" name="input[${index}][id_barang]" value="${objects.idBarang}">
                                    <input type="hidden" name="input[${index}][id_warna]" value="${objects.idWarna}">
                                    ${templateInput}
                                </div>
                            </div>`;

                            $('#wrapperFormInput').append(template);
                            $('#select_barang').focus();
                            ++index;
                            return true;
                        }
                    },
                },
                onContentReady: function() {
                    this.$content.find('input.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });

                    let jc = this;
                    let checkOnceClick = 0;
                    $('.jconfirm-box').trigger('focus');
                    $('#qty1').trigger('focus');
                    $(document).keydown(function(event) {
                        keys[event.which] = true;

                        if (keys[13] && checkOnceClick == 0) {
                            event.preventDefault();
                            jc.$$submit.trigger('click');
                            ++checkOnceClick;
                        }

                        if (keys[27]) jc.$$cancel.trigger('click');
                    });

                    this.hideLoading(true);
                }
            });
        }

        function changeBarang2(element) {
            let datas = element.select2('data');
            let tipe = $('input[name="tipe"]').val();
            $('#wrapperFieldIdBarang').html('');
            let template = "",
                templateInput = "";
            datas.forEach((el, i) => {
                let templateInput = "";
                let labelStok = "";
                let classDanger = el.stok_utama == 0 || el.stok_pilihan == 0 ? 'text-danger' : '';
                labelStok =
                    `<span class='${classDanger}'>Stok : ${el.stok_utama} ${el.nama_satuan_1}/${el.stok_pilihan} ${el.nama_satuan_2}</span>`;
                if (tipe == 'shuttle') {
                    templateInput = `
                    <div class="input-group">
                        <input type="text" name="input[${i}][volume_1]" class="form-control number-only col-md-8 mr-2" class="form-control" required>
                        <input type="hidden" name="input[${i}][id_satuan_1]" value="${el.id_satuan_1}">
                        <input type="text" class="form-control col-md-4" value="pcs" disabled>
                    </div>
                    <div class="input-group">
                        <input type="text" name="input[${i}][volume_2]" class="form-control number-only col-md-8 mr-2" class="form-control" required>
                        <input type="hidden" name="input[${i}][id_satuan_2]" value="${el.id_satuan_2}">
                        <input type="text" class="form-control col-md-4" value="kg" disabled>
                    </div>`;
                } else {
                    templateInput = `
                    <div class="input-group mb-2">
                        <input type="text" name="input[${i}][volume_1]" class="form-control number-only col-md-8 mr-2" class="form-control" required>
                        <input type="hidden" name="input[${i}][id_satuan_1]" value="${el.id_satuan_1}">
                        <input type="text" class="form-control col-md-4" value="cones" disabled>
                    </div>
                    <div class="input-group">
                        <input type="text" name="input[${i}][volume_2]" class="form-control number-only col-md-8 mr-2" class="form-control" required>
                        <input type="hidden" name="input[${i}][id_satuan_2]" value="${el.id_satuan_2}">
                        <input type="text" class="form-control col-md-4" value="kg" disabled>
                    </div>`;

                    // onKeyup="maxInputNumber($(this));" data-max="${el.stok_pilihan}"
                }

                template += `<div class="col-md-3">
                    <div class="form-group">
                        <label><b>${el.nama_barang}</b> <br> ${labelStok}</label>
                        <input type="hidden" name="input[${i}][code]" value="${el.code}">
                        <input type="hidden" name="input[${i}][id_gudang]" value="${el.id_gudang}">
                        <input type="hidden" name="input[${i}][id_barang]" value="${el.id_barang}">
                        <input type="hidden" name="input[${i}][id_warna]" value="${el.id_warna}">
                        ${templateInput}
                    </div>
                </div>`;
            });
            $('#wrapperFormInput').html(template);

            $('input.number-only').unbind().keypress(function(e) {
                var txt = String.fromCharCode(e.which);
                if (!txt.match(/[0-9.,]/)) {
                    return false;
                }
            });
        }

        function submitFormDetail(event, this_) {
            event.preventDefault();
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Konfirmasi Tambah Distribusi Pakan?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'saveorupdate',
                callback: () => {
                    let isDetail = $('input[name="isDetail"]').val();
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        success: (idParent) => {
                            toastr.success('Data Successfully Saved!');
                            goToDetail(idParent);
                        },
                        complete: () => {}
                    })
                }
            });
        }


        function changeNoBeamEdit(element) {
            if (element.val() == null) {
                resetFormBeam();
                return;
            }

            let value = element.select2('data')[0];
            let noBeam = value.no_beam || '';

            let idMesin, namaMesin = "";
            if (typeof(value['id_mesin']) !== 'undefined' && typeof(value['mesin']) !== 'undefined') {
                idMesin = value['id_mesin'];
                namaMesin = value['mesin'];
            } else {
                idMesin = value['relMesinHistoryLatest']['id_mesin'];
                namaMesin = value['relMesinHistoryLatest']['rel_mesin']['name'];
            }

            let tipePraTenun = value.tipe_pra_tenun || '';
            let isSizing = value.is_sizing || '';
            $('#wrapperFieldNoBeam').html('');
            if (noBeam != '') {
                $('#wrapperFieldNoBeam').html(`<div class="form-group">
                    <label>No. Beam</label>
                    <input type="text" class="form-control" value="${noBeam}" readonly />
                </div>`);
            }

            $('#wrapperFieldMesin').html('');
            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin</label>
                    <input type="hidden" name="id_mesin" value="${idMesin}">
                    <input type="text" class="form-control" value="${namaMesin}" readonly />
                </div>`);
            }

            $('#wrapperFieldTipePraTenun').html('');
            if (tipePraTenun != '') {
                $('#wrapperFieldTipePraTenun').html(`<div class="form-group">
                    <label>Tipe Pra Tenun</label>
                    <input type="text" name="tipe_pra_tenun" class="form-control" value="${tipePraTenun}" readonly />
                </div>`);
            }

            fieldSizing(value.is_sizing);
        }

        function changeBarangEdit(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            let value = element.select2('data')[0];
            let satuan = value.id_satuan_1 || '';
            let satuanNama = value.nama_satuan_1 || ''
            let idSatuan2 = value.id_satuan_2 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_2 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let idWarna = value.id_warna || '';
            let namaWarna = value.nama_warna || '';
            let idBarang = value.id_barang || '';
            let idGudang = value.id_gudang || '';
            let code = value.code;

            $('input[name="input[code]"]').val(code);
            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[id_gudang]"]').val(idGudang);
            $('input[name="input[id_satuan_1]"]').val(satuan);
            $('#txt_satuan_1').val(satuanNama);
            $('input[name="input[volume_1]"]').val(valueVolume1 || '');

            $('#wrapperFieldWarna').html('')
            if (idWarna != '') {
                $('#wrapperFieldWarna').html(`<div class="form-group"><label>Warna</label><input type="hidden" name="input[id_warna]" value="${idWarna}">
                <input type="text" class="form-control" id="nama_warna" value="${namaWarna}" readonly></div>`);
            }

            $('#wrapperFieldSatuan2').html('');
            if (valueStokPilihan != '' || valueVolume2 != '') {
                $('#wrapperFieldSatuan2').html(`<div class="form-group">
                    <label>Satuan 2 (Pilihan)</label>
                    <input type="hidden" name="input[id_satuan_2]" value="${idSatuan2}">
                    <input type="text" class="form-control" id="txt_satuan_2" value="${namaSatuan2}" readonly>
                </div>
                <div class="form-group">
                    <label>Volume 2</label>
                    <input type="text" class="form-control number-only" name="input[volume_2]" value="${valueVolume2 || ''}" required>
                    <div id="wrapperSuggestionStok2"></div>
                </div>`);
            }

            if (typeof(value.stok_utama) !== 'undefined' && typeof(value.stok_pilihan) !== 'undefined') {
                setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
            }
        }
    </script>
@endsection
