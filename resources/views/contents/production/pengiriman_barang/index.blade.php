@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-retrieve="true" data-column="columnKirim"
                            data-table="tablePengirimanBarang" data-toggle="tab" href="#tabKirim" role="tab">
                            <i class="icon md-refresh-sync spin mr-2"></i> Kirim
                        </a>
                    </li>
                    @if (Auth::user()->roles_id != 8)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-retrieve="false" data-column="columnTerima"
                                data-table="tablePengirimanBarangTerima" data-id-pengiriman-barang="" data-toggle="tab"
                                href="#tabTerima" role="tab">
                                <i class="icon md-refresh-sync spin mr-2"></i> Terima
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content py-20">
                    <div class="tab-pane active" id="tabKirim" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! App\Helpers\Template::tools(['tambah', 'refresh']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-striped" cellspacing="0"
                                    id="tablePengirimanBarang">
                                    <thead>
                                        <tr>
                                            <th width="30px"></th>
                                            <th width="30px">No</th>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Tipe Pengiriman</th>
                                            <th
                                                data-visible="{{ Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6 }}">
                                                Total Pcs</th>
                                            <th>Catatan</th>
                                            <th width="100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabTerima" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-12">
                                {{-- <select id="select_no_pengiriman" onchange="tableAjax.ajax.reload();"
                                    dropdown-parent="false" data-placeholder="-- Pilih No. Pengiriman --"
                                    data-route="{{ route('helper.getNotaPengiriman') }}"
                                    class="form-control select2 float-left" required>
                                </select> --}}
                                <button type="button" class="btn btn-sm btn-primary waves-effect waves-classic"
                                    onclick="cetak_spk($(this))">Cetak</button>
                                {!! App\Helpers\Template::tools(['approve', 'refresh']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-striped" cellspacing="0"
                                    id="tablePengirimanBarangTerima">
                                    <thead>
                                        <tr>
                                            <th width="30px">
                                                <div class="checkbox-custom checkbox-primary">
                                                    <input type="checkbox" id="inputChecked" onclick="checkingAll($(this))">
                                                    <label for="inputChecked"></label>
                                                </div>
                                            </th>
                                            <th width="30px">No</th>
                                            <th>No. Pengiriman</th>
                                            <th>Nama Barang</th>
                                            <th>Gudang Tujuan</th>
                                            <th>Volume 1</th>
                                            <th>Volume 2</th>
                                            <th>Tracking</th>
                                            <th>Catatan</th>
                                            <th width="100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
        var tableDetail = "";
        let idSelected = [];
        $(document).ready(function() {
            let route = "{{ getCurrentRoutes() }}";
            initTable({
                column: 'columnKirim'
            }, route);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).data('table');
                let retrieve = $(this).data('retrieve');
                let objects = $(this).data();

                if (table == 'tablePengirimanBarangTerima') initSelect2(false, false, '250px');

                initTable(objects, route, table)
                if (retrieve) tableAjax.ajax.reload();
                $(this).data('retrieve', true)
            })
        })

        function newDetail(id) {
            var data = $(this).data();
            data.id = id;
            var uri = "{{ url('production/bpht/detail') }}" + '?' + new URLSearchParams(data);
            $.ajax({
                url: uri,
                type: "get",
                dataType: "html",
                // sync: false,
                success: function(html) {
                    mainWrapper.hide();
                    formWrapper.html(html);
                },
                error: function() {
                    alert("Error");
                },
            });
        }

        function cetak_spk(this_) {
            // Memanggil AJAX untuk mendapatkan data select options
            $.ajax({
                url: 'proses/get-spk-pengiriman', // Gantilah dengan URL endpoint yang sesuai untuk mendapatkan data select
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var selectOptions = '';
                    response.forEach(function(item) {
                        selectOptions +=
                            `<option value="${item.id}">(${item.initial}) ${item.nomor}</option>`;
                    });

                    // Menampilkan modal dengan Swal
                    Swal.fire({
                        title: 'Cetak',
                        html: `
                            <select name="tipe_cetak" id="tipe_cetak" class="swal2-input">
                                ${selectOptions}
                            </select>
                        `,
                        width: '400px',
                        focusConfirm: false,
                        didOpen: () => {
                            // Inisialisasi Select2 setelah modal dibuka
                            $('#tipe_cetak').select2({
                                width: '100%', // Menyesuaikan lebar select2
                                dropdownParent: $('.swal2-container'),
                            });
                        },
                        preConfirm: () => {
                            var tipe_cetak = document.getElementById('tipe_cetak').value;
                            if (!tipe_cetak) {
                                Swal.showValidationMessage('tipe cetak harus diisi');
                            }
                            return tipe_cetak;
                        }
                    }).then((result) => {
                        var data = this_.data();
                        data.id = result.value;
                        data.model = 'PengirimanBarang'
                        var uri = `{{ url('cetak/pengirimanBarang') }}`;
                        window.open(uri + '?' + new URLSearchParams(data), '_blank');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching select options:', error);
                }
            });
        }

        function initTable(param, route, table = 'tablePengirimanBarang') {
            tableAjax = $(`#${table}`).DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                // retrieve: true,
                language: {
                    searchPlaceholder: 'Nomor Pegiriman'
                },
                ajax: {
                    url: `${route}`,
                    type: 'GET',
                    data: function(e) {
                        e.column = param.column;
                        e.idPengirimanBarang = $('#select_no_pengiriman').val();
                    }
                },
                lengthMenu: [15, 25, 50, 100],
                columns: setColumn(param.column),
                initComplete: () => {

                }
            })
        }

        function setColumn(columnName = 'columnKirim') {
            let column = {};
            column['columnKirim'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return `<button type='button' id='btnCetak' data-model='PengirimanBarang' data-id='${row.id}' class='btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic' onclick='cetakForm($(this));'>
                        <i class='icon md-print'></i>
                    </button>`;
                }
            }, {
                data: null,
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
                data: 'nama_tipe_pengiriman',
                name: 'nama_tipe_pengiriman',
                searchable: false
            }, {
                data: null,
                name: 'total_pcs',
                searchable: false,
                render: (data) => {
                    return data.total_pcs || 0;
                }
            }, {
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false
            }];

            column['columnTerima'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    let checkboxWrapper = `<div class="checkbox-custom checkbox-primary">
                        <input type="checkbox" id="inputChecked${row.id}" value="${row.id}" onclick="checking()" name="selected_id[]">
                        <label for="inputChecked${row.id}"></label>
                    </div>`;
                    return (row.accepted_at) ? '' : checkboxWrapper;
                }
            }, {
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'no_pengiriman',
                name: 'no_pengiriman',
                searchable: false
            }, {
                data: 'nama_barang',
                name: 'nama_barang',
                searchable: false
            }, {
                data: 'gudang',
                name: 'gudang',
                searchable: false
            }, {
                data: null,
                searchable: false,
                render: (data) => {
                    return `${data.volume_1} ${data.nama_satuan_1}`;
                }
            }, {
                data: null,
                searchable: false,
                render: (data) => {
                    return `${data.volume_2 || ''} ${data.nama_satuan_2 || ''}`;
                }
            }, {
                data: null,
                searchable: false,
                render: (data) => {
                    return (data.accepted_at) ?
                        `<span class="badge badge-outline badge-primary">DITERIMA</span>` :
                        `<span class="badge badge-outline badge-warning">PENDING</span>`;
                }
            }, {
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false,
                render: (render, type, data, meta) => {
                    let checkButton = `<button type="button" onclick="acceptForm(${data.id});" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                        <i class="icon md-check mr-2"></i>
                    </button>`;
                    let viewButton = `<button type="button" onclick="acceptForm(${data.id}, true);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                        <i class="icon md-eye mr-2"></i>
                    </button>`;
                    let cancelButton = `<button type="button" data-id="${data.id}" data-id-log-stok="${data.id_log_stok || ''}" data-id-parent-detail="${data.id_parent_detail}" onclick="rejectForm($(this));" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                        <i class="icon md-close mr-2"></i>
                    </button>`;
                    if (data.validated_at != null) {
                        return viewButton;
                    } else {
                        if (data.accepted_at) {
                            return viewButton + '' + cancelButton;
                        } else {
                            return render;
                        }
                    }
                }
            }];

            return column[columnName];
        }

        function checkingAll(element) {
            let isChecked = element.is(':checked');
            $('input[type="checkbox"]').prop('checked', isChecked);
            checking(true);
        }

        function checking(state = false) {
            let idSelected = $("input[name='selected_id[]']:checked").map(function() {
                return $(this).val();
            }).get();
            $('#btnApprove').prop('disabled', idSelected.length <= 1);
            if (!state) $('#inputChecked').prop('checked', false);
        }

        function acceptForm(id, view = false) {
            let route = `{{ route('helper.acceptFormView', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            $.ajax({
                url: routeWithParam,
                type: 'GET',
                dataType: 'json',
                success: (response) => {
                    let dataForm = {
                        title: 'Form Penerimaan Barang',
                        content: response['render'],
                        textSubmit: 'Terima',
                        callback: () => {
                            selectedOption(response['selected']);
                            if (view) {
                                $('input').prop('disabled', true);
                                $('select').prop('disabled', true);
                                $('textarea').prop('disabled', true);
                                $('.jconfirm-buttons > button:eq(1)').remove();
                            }
                        },
                        cancelCallback: () => {
                            $('input').prop('disabled', false);
                            $('select').prop('disabled', false);
                        }
                    };

                    if (view) dataForm.textCancel = 'Close';
                    showForm(dataForm);
                }
            })
        }

        function rejectForm(element) {
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Konfirmasi Pembatalan?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'delete',
                textButton: 'Batalkan',
                callback: () => {
                    $.ajax({
                        url: `{{ route('helper.reject') }}`,
                        type: 'POST',
                        data: element.data(),
                        success: (response) => {
                            tableAjax.ajax.reload();
                            toastr.success(response);
                        }

                    })
                }
            });
        }

        // ================== DETAIL ==================

        function goToDetail(id, elem) {
            if (elem.data) {
                if (elem.data('id_tipe_pengiriman') == 7 && id > 965) {
                    newDetail(id);
                } else {
                    let isValidator = "{{ Auth::user()->roles_id }}";
                    detailForm({
                        url: `{{ url('helper/detailForm') }}/${id}`,
                        data: {
                            id: id,
                            model: 'PengirimanBarang',
                            customView: isValidator == 8 ?
                                'contents.production.pengiriman_barang.detail-validator' : ''
                        }
                    })
                }
            } else {
                if (elem.id_tipe_pengiriman) {
                    if (elem.id_tipe_pengiriman == 7 && id > 965) {
                        newDetail(id);
                    } else {
                        let isValidator = "{{ Auth::user()->roles_id }}";
                        detailForm({
                            url: `{{ url('helper/detailForm') }}/${id}`,
                            data: {
                                id: id,
                                model: 'PengirimanBarang',
                                customView: isValidator == 8 ?
                                    'contents.production.pengiriman_barang.detail-validator' : ''
                            }
                        })
                    }
                } else {
                    let isValidator = "{{ Auth::user()->roles_id }}";
                    detailForm({
                        url: `{{ url('helper/detailForm') }}/${id}`,
                        data: {
                            id: id,
                            model: 'PengirimanBarang',
                            customView: isValidator == 8 ?
                                'contents.production.pengiriman_barang.detail-validator' : ''
                        }
                    })
                }
            }
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);

            detailTable({
                state: 'input',
                status: 'ASAL',
                column: data.column_name
            }, routeWithParam);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).attr('data-table');
                let retrieve = $(this).attr('data-retrieve');
                let objects = $(this).data();
                detailTable(objects, routeWithParam, table)
                if (retrieve) tableAjaxDetail.ajax.reload();
                $(this).attr('data-retrieve', 'true')
            })
        }

        function detailTable(param, route, table = 'tablePengirimanBarangDetailInput') {
            tableAjaxDetail = $(`#${table}`).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: `${route}`,
                    type: 'GET',
                    data: param
                },
                lengthMenu: [15, 25, 50],
                columns: selectedColumn(param.column),
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function selectedColumn(columnName = 'Default') {
            let column = {};
            column['Default'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_barang',
                name: 'nama_barang',
                searchable: false
            }, {
                data: 'gudang',
                name: 'gudang',
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
                    return `${formatNumber(volume, data.satuan_pilihan)}`;
                }
            }, {
                data: 'satuan_pilihan',
                name: 'satuan_pilihan',
                searchable: false
            }, {
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false,
                render: (render, type, data, meta) => {
                    let checkPengirimanDiterima = data.rel_detail_tujuan_count || 0;
                    if (checkPengirimanDiterima > 0) {
                        return `<span class="badge badge-outline badge-success">Diterima</span>`;
                    } else {
                        return render;
                    }
                }
            }];

            column['Beam'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: null,
                name: 'no_kikw',
                searchable: false,
                render: (data) => {
                    let noBeam = data.no_beam || '-';
                    let noKikw = data.no_kikw || '-';
                    return noBeam + ' / ' + noKikw;
                }
            }, {
                data: 'nama_barang',
                name: 'nama_barang'
            }, {
                data: 'gudang',
                name: 'gudang',
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
                    return `${formatNumber(volume, data.satuan_pilihan)}`;
                }
            }, {
                data: 'satuan_pilihan',
                name: 'satuan_pilihan',
                searchable: false
            }, {
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false,
                render: (render, type, data, meta) => {
                    let checkPengirimanDiterima = data.rel_detail_tujuan_count || 0;
                    if (checkPengirimanDiterima > 0) {
                        return `<span class="badge badge-outline badge-success">Diterima</span>`;
                    } else {
                        return render;
                    }
                }
            }];

            return column[columnName];
        }

        function checkingTipeLainnya(element) {
            let isChecked = element.is(':checked');
            let valueTipePengiriman = element.attr('data-id-tipe-pengiriman');
            let textTipePengiriman = element.attr('data-tipe-pengiriman');
            if (isChecked) {
                $('#wrapperTipePengiriman').html(`<input type="text" value="${textTipePengiriman}" id="select_tipe_pengiriman" 
                class="form-control" name="input[txt_tipe_pengiriman]" required>`);
            } else {
                $('#wrapperTipePengiriman').html(`<select name="input[id_tipe_pengiriman]" id="select_tipe_pengiriman"
                data-placeholder="-- Pilih Tipe Pengiriman --" data-route="{{ route('helper.getTipePengiriman') }}"
                class="form-control select2" required></select>`);
                select2Element('select_tipe_pengiriman');
                selectedOption({
                    select_tipe_pengiriman: {
                        id: valuePengiriman,
                        text: textTipePengiriman
                    }
                })
            }
        }

        function changeBarang(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang || '';
            let idWarna = value.id_warna || '';
            let idMotif = value.id_motif || '';
            let idSatuan1 = value.id_satuan_1 || '';
            let idSatuan2 = value.id_satuan_2 || '';
            let namaWarna = value.nama_warna || '';
            let namaMotif = value.nama_motif || '';
            let namaSatuan1 = value.nama_satuan_1 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_2 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let currentCode = value.code || 'PB';
            let isSizing = value.is_sizing || '';
            let idBeam = value.id_beam || '';
            let idSongket = value.id_songket || '';
            let TanggalPotong = value.tanggal_potong || '';
            let noBeam = value.no_beam || '-';
            let idMesin = value.id_mesin || '';
            let namaMesin = value.nama_mesin || '';
            let noKikw = value.no_kikw || '';
            let noKiks = value.no_kiks || '';
            let tipePraTenun = value.tipe_pra_tenun || '';
            let idGrade = value.id_grade || '';
            let namaGrade = value.nama_grade || '';
            let idKualitas = value.id_kualitas || '';
            let namaKualitas = value.nama_kualitas || '';

            $('input[name="input[id_barang]"]').val(idBarang);
            if (idBeam != '') {
                $('#wrapperFieldBeam').html(`<div class="form-group">
                    <label>No. Beam / KIKW</label>
                    <input type="text" class="form-control" value="${noBeam} / ${noKikw}" readonly/>
                    <input type="hidden" name="input[id_beam]" value="${idBeam}" />
                </div>`);
            }

            if (idSongket != '') {
                $('#wrapperFieldSongket').html(`<div class="form-group">
                    <label>KIKS</label>
                    <input type="text" class="form-control" value="${noKiks}" readonly/>
                    <input type="hidden" name="input[id_songket]" value="${idSongket}" />
                </div>`);
            }
            if (TanggalPotong != '') {
                $('#wrapperFieldTanggalPotong').html(`<div class="form-group">
                    <label>Tgl. Potong</label>
                    <input type="text" class="form-control" value="${TanggalPotong}" readonly/>
                    <input type="hidden" name="input[tanggal_potong]" value="${TanggalPotong}" />
                </div>`);
            }

            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin</label>
                    <input type="text" class="form-control" value="${namaMesin}" readonly/>
                    <input type="hidden" name="input[id_mesin]" value="${idMesin}" />
                </div>`);
            }

            if (tipePraTenun != '') {
                $('#wrapperFieldTipePraTenun').html(`<div class="form-group">
                    <label>Tipe Pra Tenun</label>
                    <input type="text" name="input[tipe_pra_tenun]" class="form-control" value="${tipePraTenun}" readonly/>
                </div>`);
            }

            if (isSizing != '') {
                $('#wrapperFieldSizing').html(`<div class="form-group">
                    <label>Sizing?</label>
                    <input type="text" class="form-control" value="${isSizing}" readonly />
                </div>`);
            }

            $('input[name="current_code"]').val(currentCode);
            let idGudangTable = parseInt($('#idGudangTable').val());
            let state = $('input[name="state"]').val();

            $('#wrapperFieldWarna').html('')
            if (idWarna != '') {
                $('#wrapperFieldWarna').html(`<div class="form-group"><label>Warna</label><input type="hidden" name="input[id_warna]" value="${idWarna}">
                <input type="text" class="form-control" id="nama_warna" value="${namaWarna}" readonly></div>`);
            }

            $('#wrapperFieldMotif').html('')
            if (idMotif != '') {
                $('#wrapperFieldMotif').html(`<div class="form-group"><label>Motif</label><input type="hidden" name="input[id_motif]" value="${idMotif}">
                <input type="text" class="form-control" id="nama_motif" value="${namaMotif}" readonly></div>`);
            }

            $('#wrapperFieldGrade').html('')
            if (idGrade != '') {
                $('#wrapperFieldGrade').html(`<div class="form-group"><label>Kualitas</label><input type="hidden" name="input[id_grade]" value="${idGrade}">
                <input type="text" class="form-control" id="nama_grade" value="${namaGrade}" readonly></div>`);
            }

            $('#wrapperFieldKualitas').html('')
            if (idKualitas != '') {
                $('#wrapperFieldKualitas').html(`<div class="form-group"><label>Jenis Cacat</label><input type="hidden" name="input[id_kualitas]" value="${idKualitas}">
                <input type="text" class="form-control" id="nama_kualitas" value="${namaKualitas}" readonly></div>`);
            }

            if (state == 'input') {
                $('input[name="input[id_satuan_1]"]').val(idSatuan1);
                $('#txt_satuan_1').val(namaSatuan1);
                $('input[name="input[volume_1]"]').val(valueVolume1 || valueStokUtama);

                $('#wrapperFieldSatuan2').html('');
                if (valueStokPilihan != '' || valueVolume2 != '') {
                    $('#wrapperFieldSatuan2').html(`<div class="form-group">
                    <label>Satuan 2 (Pilihan)</label>
                    <input type="hidden" name="input[id_satuan_2]" value="${idSatuan2}">
                    <input type="text" class="form-control" id="txt_satuan_2" value="${namaSatuan2}" readonly>
                </div>
                <div class="form-group">
                    <label>Volume 2</label>
                    <input type="text" class="form-control number-only" name="input[volume_2]" value="${valueVolume2 || valueStokPilihan}" required>
                    <div id="wrapperSuggestionStok2"></div>
                </div>`);
                }

                if (typeof(value.stok_utama) != 'undefined' && typeof(value.stok_pilihan) != 'undefined') {
                    setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
                }

            } else {
                // RULE OF WAREHOUSE
                let arrGudangProses = [2, 3]; //dyeing, weaving
                let arrSatuanGudangDyeingUtama = [2]; // cones
                let arrSatuanGudangDyeingPilihan = [1]; // kg
                let arrSatuanGudangWeavingUtama = [1, 3, 4, 5]; // pcs, beam, palet
                let arrSatuanGudangWeavingPilihan = [2, 4]; // kg, pcs
                if (arrGudangProses.includes(idGudangTable)) {
                    if (idGudangTable == 2) {
                        if (!arrSatuanGudangDyeingUtama.includes(idSatuan1)) {
                            idSatuan1 = 1;
                            namaSatuan1 = 'kg'
                            valueVolume1 = '';
                        }
                        // if (!arrSatuanGudangDyeingPilihan.includes(idSatuan2)) {
                        //     idSatuan2 = 2;
                        //     namaSatuan2 = 'kg'
                        //     valueVolume2 = '';
                        // }
                    }
                }

                let objectsSelected = {
                    select_satuan_1: {
                        id: idSatuan1,
                        text: namaSatuan1
                    },
                }

                if (typeof(idSatuan2) !== 'undefined' && idSatuan2 != '') {
                    objectsSelected.select_satuan_2 = {
                        id: value.id_satuan_2,
                        text: value.nama_satuan_2
                    };
                }

                selectedOption(objectsSelected);

                // $('#txt_satuan_1').val(namaSatuan1);
                // $('#txt_satuan_2').val(namaSatuan2);
                // $('input[name="input[id_satuan_1]"]').val(idSatuan1);
                // $('input[name="input[id_satuan_2]"]').val(idSatuan2);
                $('input[name="input[volume_1]"]').val(valueVolume1);
                $('input[name="input[volume_2]"]').val(valueVolume2);
                // if (valueVolume1 != '' && valueVolume2 != '') setSuggestionStok(valueVolume1, valueVolume2, true);
            }

            let idTipe = $('input[name="id_tipe"]').val();
            fieldVolume(idBeam, idTipe == 7 || idTipe == 8);
        }

        function addFormSendAll(element) {
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="{{ route('helper.sendAll') }}" onsubmit="submitFormSendAll(event, $(this));" method="POST" class="formInput">
                    <input type="hidden" name="id_pengiriman_barang" value="${element.data('id')}">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" value="${element.data('tanggal')}" id="tanggal" name="tanggal" required>
                    </div>`,
                columnClass: 'col-md-4 col-md-offset-4',
                typeAnimated: true,
                type: 'dark',
                theme: 'material',
                onOpenBefore: function() {
                    this.showLoading(true);
                },
                buttons: {
                    cancel: {},
                    submit: {
                        text: 'Send',
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

        function submitFormSendAll(event, element) {
            event.preventDefault();
            $.ajax({
                url: element.attr('action'),
                type: element.attr('method'),
                data: element.serialize(),
                success: (response) => {
                    tableAjaxDetail.ajax.reload();
                },
                complete: () => {}
            })
        }

        function sendAll(objects) {
            $.ajax({
                url: "{{ route('helper.sendAll') }}",
                type: "POST",
                data: objects,
                success: (response) => {
                    tableAjax.ajax.reload();
                },
                complete: () => {}
            })
        }

        function cancelSendAll(objects) {
            $.ajax({
                url: "{{ route('helper.cancelSendAll') }}",
                type: "POST",
                data: objects,
                success: (response) => {
                    toastr.success('Data is Successfully Canceled!');
                    tableAjaxDetail.ajax.reload();
                },
                complete: () => {}
            })
        }

        function submitForm(event, this_) {
            console.log(event);
            console.log(this_);
            event.preventDefault();
            let isDetail = $('input[name="isDetail"]').val();
            let tanggal = $('input[name="input[tanggal]"]').val();
            let idTipe = $('select[name="input[id_tipe_pengiriman]"]').val();
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                success: (response) => {
                    if (typeof(response['id']) !== 'undefined') {
                        toastr.success(response['message']);
                        sendAll({
                            id_pengiriman_barang: response['id'],
                            tanggal: tanggal,
                            id_tipe: idTipe
                        });
                    } else {
                        toastr.success(response);
                        refreshTable(isDetail == 'true');
                    }
                },
                complete: () => {}
            })
        }

        function deleteForm(id, isDetail = false, this_) {
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Delete Data?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'delete',
                callback: () => {
                    let routeDelete = "{{ getCurrentRoutes('destroy', ['%id']) }}";
                    let routeWithParam = routeDelete.replace('%id', id);
                    let data = this_.data();
                    if (typeof(data.route) !== 'undefined' && data.route != '') {
                        let customRoute = data.route;
                        routeWithParam = customRoute.replace('%id', id);
                    }
                    $.ajax({
                        url: routeWithParam,
                        type: "POST",
                        data: {
                            isDetail: isDetail,
                            _method: "DELETE"
                        },
                        success: (responseText) => {
                            let checkDetail = isDetail == 'true' || isDetail == true
                            let tanggal = $('input[name="tanggal"]').val() || '';
                            let idTipe = $('input[name="id_tipe_pengiriman"]').val() || '';
                            if (checkDetail && (idTipe == 4 || idTipe == 17)) {
                                toastr.success(responseText);
                                cancelSendAll({
                                    id: id,
                                    tanggal: tanggal,
                                    id_tipe: idTipe
                                });
                            } else {
                                toastr.success(responseText);
                                refreshTable(checkDetail);
                            }
                        },
                        complete: () => {}
                    })
                }
            });
        }
    </script>
@endsection
