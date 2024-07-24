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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDyeingJasaLuar">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>No. SPK</th>
                                <th>Vendor</th>
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
            tableAjax = $('#tableDyeingJasaLuar').DataTable({
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
                    data: 'supplier',
                    name: 'supplier',
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
                    model: 'DyeingJasaLuar'
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
                status: 'KIRIM'
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

        function detailTable(param, route, table = 'tableKirim') {
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

        function selectedColumn(columnName = 'tableKirim') {
            let column = {};
            column['tableKirim'] = [{
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

            column['tableTerima'] = [{
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
                data: null,
                name: 'volume_2',
                searchable: false,
                render: (data) => {
                    return `${formatNumber(data.volume_2, data.satuan_pilihan)} ${data.satuan_pilihan}`;
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
            let idGudang = value.id_gudang;
            $('input[name="input[id_gudang]"]').val(idGudang);
            $('input[name="input[id_barang]"]').val(idBarang);
        }
    </script>
@endsection
