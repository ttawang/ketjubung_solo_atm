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
                    <table id="tableResepChemicalFinishing" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Sarung</th>
                                <th>Motif</th>
                                <th width="100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
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
            tableAjax = $('#tableResepChemicalFinishing').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
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
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },{
                    data: 'motif',
                    name: 'motif'
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
                url: `{{ url('helper/detailFormDatabase') }}/${id}`,
                data: {
                    id: id,
                    model: 'ResepChemicalFinishing',
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ route('database.resep_chemical_finishing.show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $(`#tableResepDetail`).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: routeWithParam,
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
                    data: 'satuan',
                    name: 'satuan',
                    searchable: false
                }, {
                    data: 'volume',
                    name: 'volume',
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
    </script>
@endsection
