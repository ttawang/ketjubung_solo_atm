@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambah', 'refresh']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDyeingGresik">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
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
        $(document).ready(function() {
            tableAjax = $('#tableDyeingGresik').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'tanggal',
                    name: 'tanggal',
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    searchable: false
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }],
                initComplete: () => {

                }
            })
        })

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailForm') }}/${id}`,
                data: {
                    id: id,
                    model: 'DyeingGresik'
                },
                callback: () => {
                    // initSelect2(false, false);
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);

            detailTable({
                code: 'BBDG'
            }, routeWithParam);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).data('table');
                let retrieve = $(this).data('retrieve');
                let objects = $(this).data();
                detailTable(objects, routeWithParam, table)
                if (retrieve) tableAjaxDetail.ajax.reload();
                $(this).data('retrieve', true)
            })
        }

        function detailTable(param, route, table = 'tableInput') {
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
                    data: param,
                },
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: selectedColumn(table),
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function selectedColumn(columnName = 'tableInput') {
            let column = {};
            column['tableInput'] = [{
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
                data: 'gudang',
                name: 'gudang',
                searchable: false
            }, {
                data: 'nama_barang',
                name: 'nama_barang',
                searchable: false
            }, {
                data: null,
                name: 'volume_1',
                searchable: false,
                render: (data) => {
                    return `${formatNumber(data.volume_1, data.satuan_utama)} ${data.satuan_utama}`;
                }
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false
            }];

            column['tableOutput'] = [{
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
                data: 'gudang',
                name: 'gudang',
                searchable: false
            }, {
                data: 'nama_barang',
                name: 'nama_barang',
                searchable: false
            }, {
                data: 'warna',
                name: 'warna',
                searchable: false
            }, {
                data: null,
                name: 'volume_1',
                searchable: false,
                render: (data) => {
                    return `${formatNumber(data.volume_1, data.satuan_utama)} ${data.satuan_utama}`;
                }
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false
            }];

            return column[columnName];
        }

        function changeGudang(element) {
            let isEdit = $('#idDetail').val() != '';
            if (!isEdit) {
                let value = element.val() || 9999;
                $('#select_gudang').val(value);
                select2Element('select_barang', true);
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
            }
        }

        function changeBarang(element) {
            if (element.val() == null) {
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang;
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;

            $('input[name="input[id_barang]"]').val(idBarang);

            if (typeof(value.stok_utama) != 'undefined') {
                $('#wrapperSuggestionStok1').html(`<div class="text-right">
                    <span class="font-size-12">Stok: ${valueStokUtama}</span>
                </div>`)
            }
        }

        function changeBarangTerima(element) {
            let value = element.select2('data')[0];
            let idBarang = value.id_barang;
            // let volume1 = value.volume_1;
            // let volume2 = value.volume_2;
            $('input[name="input[id_barang]"]').val(idBarang);
            // $('input[name="input[volume_1]"]').val(volume1);
            // $('input[name="input[volume_2]"]').val(volume2);
        }


        function showFormWarna(id) {
            $.ajax({
                url: `{{ url('helper/getWarnaForm') }}/${id}`,
                type: 'GET',
                data: {
                    form: 'gresik'
                },
                success: (render) => {
                    $('#wrapperWarna').html(render);
                },
                complete: () => {
                    $('#tableWarna').DataTable();
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function addFormWarna(element) {
            let id = element.attr('data-id') || '';
            let idDyeingDetail = element.attr('data-id-dyeing-detail');
            let idLogStok = element.attr('data-id-log-stok') || '';
            let idWarna = element.attr('data-id-warna') || '';
            let idSatuan = element.attr('data-id-satuan') || '';
            let volume = element.attr('data-volume') || '';
            let idPewarna = element.attr('data-id-pewarna') || '';
            let namaPewarna = element.attr('data-nama-pewarna') || '';
            let route = "{{ route('helper.storeWarna') }}";
            let tanggal = "{{ date('Y-m-d') }}";
            if (id != '') {
                let routeWitHParams = `{{ route('helper.storeWarna', ['%id']) }}`;
                route = routeWitHParams.replace('%id', id);
            }
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="${route}" onsubmit="submitFormWarna(event, $(this));" method="POST" class="formInput">
                    @if (isset($id)) @method('PATCH') @endif
                    <input type="hidden" name="input[id_dyeing_gresik_detail]" value="${idDyeingDetail}">
                    <input type="hidden" name="input[id_warna]" value="${idWarna}">
                    <input type="hidden" name="id_mapping_warna" value="${id}">
                    <input type="hidden" name="id_log_stok" value="${idLogStok}">
                    <input type="hidden" name="input[id_barang]" value="${idPewarna}">
                    <input type="hidden" name="model" value="DyeingGresikWarna">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="input[tanggal]" value="${tanggal}" required>
                    </div>
                    <div class="form-group">
                        <label>Pewarna</label>
                        <select id="select_warna" onchange="changeWarna($(this));" data-placeholder="-- Pilih Pewarna --"
                            data-route="{{ route('helper.getBarangWithStok') }}" data-id-gudang="2" data-filter="2"
                            class="form-control select2" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select class="form-control" name="input[id_satuan]" required>
                            <option value="2" ${idSatuan == '2' ? 'selected' : ''}>Kg</option>
                            <option value="5" ${idSatuan == '5' ? 'selected' : ''}>Gram</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Volume</label>
                        <input type="text" class="form-control number-only" name="input[volume]" value="${volume}" required>
                        <div id="wrapperSuggestionStok1"></div>
                    </div>
                </form>`,
                columnClass: 'col-md-4 col-md-offset-4',
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
                    this.$content.find('input.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });
                    select2Element('select_warna')
                    if (idPewarna != '') {
                        let selectedPewarna = {
                            select_warna: {
                                id: idPewarna,
                                text: namaPewarna
                            }
                        }
                        selectedOption(selectedPewarna);
                    }

                    let jc = this;
                    let checkOnceClick = 0;
                    $('.jconfirm-box').trigger('focus');
                    $(document).keydown(function(event) {
                        if (event.keyCode == 13 && checkOnceClick == 0) {
                            event.preventDefault();
                            jc.$$formSubmit.trigger('click');
                            ++checkOnceClick;
                        }
                    });

                    this.hideLoading(true);
                }
            });
        }

        function addFormResep(element) {
            let idDyeingDetail = element.attr('data-id-dyeing-detail');
            let idWarna = element.attr('data-id-warna') || '';
            let tanggal = "{{ date('Y-m-d') }}";
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="{{ route('helper.storeResepWarna') }}" onsubmit="submitFormWarna(event, $(this));" method="POST" class="formInput" style="height: 400px;">
                    <input type="hidden" name="input[id_dyeing_gresik_detail]" value="${idDyeingDetail}">
                    <input type="hidden" name="input[id_warna]" value="${idWarna}">
                    <input type="hidden" name="model" value="DyeingGresikWarna">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="input[tanggal]" value="${tanggal}" required>
                    </div>
                    <div class="form-group">
                        <label>Resep</label>
                        <select id="select_resep" name="id_resep" onchange="changeResep($(this));" data-placeholder="-- Pilih Resep --"
                            data-route="{{ route('helper.getResep') }}" class="form-control select2" required>
                        </select>
                    </div>
                    <div class="form-group" id="wrapperListResep"></div>
                </form>`,
                columnClass: 'col-md-4 col-md-offset-4',
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
                    $('#wrapperListResep').html('');
                    this.$content.find('input.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });
                    select2Element('select_resep')

                    let jc = this;
                    let checkOnceClick = 0;
                    $('.jconfirm-box').trigger('focus');
                    $(document).keydown(function(event) {
                        if (event.keyCode == 13 && checkOnceClick == 0) {
                            event.preventDefault();
                            jc.$$formSubmit.trigger('click');
                            ++checkOnceClick;
                        }
                    });

                    this.hideLoading(true);
                }
            });
        }

        function changeResep(element) {
            $('#wrapperListResep').html('');
            let value = element.select2('data')[0];
            if (value['resep_detail'].length > 0) {
                let template = `<label>Daftar Pewarna: </label>`;
                template += `<ul class="list-group list-group-full">`;
                value['resep_detail'].forEach((element, index) => {
                    template +=
                        `<li class="list-group-item">${++index}. ${element['rel_barang'].name} (${element.volume} ${element.satuan})</li>`
                });
                template += `</ul>`
                $('#wrapperListResep').html(template);
            }
        }

        function changeWarna(element) {
            let value = element.select2('data')[0];
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            if (typeof(value.id_barang) !== 'undefined') $('input[name="input[id_barang]"]').val(value.id_barang);
            if (typeof(value.stok_utama) != 'undefined' && typeof(value.stok_utama) != 'undefined') {
                setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
            }
        }

        function submitFormWarna(event, element) {
            event.preventDefault();
            $.ajax({
                url: element.attr('action'),
                type: element.attr('method'),
                data: element.serialize(),
                success: (response) => {
                    $('#tableWarna').DataTable().destroy();
                    showFormWarna(response['id']);
                    toastr.success(response['message']);
                },
                complete: () => {}
            })
        }

        function deleteFormWarna(element) {
            let routeDelete = "{{ route('helper.deleteWarna', ['%id']) }}";
            let routeWithParam = routeDelete.replace('%id', element.attr('data-id'));
            let idLogStok = element.attr('data-id-log-stok');
            let idDyeingDetail = element.attr('data-id-dyeing-detail');
            $.ajax({
                url: routeWithParam,
                type: "POST",
                data: {
                    id_log_stok: idLogStok,
                    _method: "DELETE",
                    model: "DyeingGresikWarna"
                },
                success: (response) => {
                    $('#tableWarna').DataTable().destroy();
                    showFormWarna(idDyeingDetail);
                    toastr.success(response['message']);
                },
                complete: () => {}
            })
        }
    </script>
@endsection
