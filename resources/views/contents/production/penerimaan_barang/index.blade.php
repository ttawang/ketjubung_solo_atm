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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tablePenerimaanBarang">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="30px">No</th>
                                <th>Tanggal Terima</th>
                                <th>No. PO</th>
                                <th>Supplier</th>
                                <th>No. Kendaraan</th>
                                <th>Supir</th>
                                <th>No. TTBM</th>
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
            tableAjax = $('#tablePenerimaanBarang').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: [{
                    data: null,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return `<button type='button' id='btnCetak' data-model='PenerimaanBarang' data-id='${row.id}' class='btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic' onclick='cetakForm($(this));'>
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
                    data: 'tanggal_terima_custom',
                    name: 'tanggal_terima',
                    searchable: false
                }, {
                    data: 'no_po',
                    name: 'no_po',
                    searchable: false
                }, {
                    data: 'supplier',
                    name: 'supplier',
                    searchable: false
                }, {
                    data: 'no_kendaraan',
                    name: 'no_kendaraan',
                    searchable: false
                }, {
                    data: 'supir',
                    name: 'supir',
                    searchable: false
                }, {
                    data: 'no_ttbm',
                    name: 'no_ttbm',
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
                    model: 'PenerimaanBarang'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tablePenerimaanBarangDetail').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: `${routeWithParam}`,
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
                    },
                    // {
                    //     data: 'volume_2',
                    //     name: 'volume_2',
                    //     searchable: false
                    // }, {
                    //     data: 'satuan_pilihan',
                    //     name: 'satuan_pilihan',
                    //     searchable: false
                    // }, 
                    {
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
                    $('.panel').removeClass('is-loading');
                }
            })
        }


        function changeBarang(element) {
            let value = element.select2('data')[0];
            (value.id_tipe != 1) ? $('#wrapperField').html(''): $('#select_gudang').change();
        }
    </script>
@endsection
