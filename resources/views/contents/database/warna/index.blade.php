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

            <div class="form-group row">
                <div class="col-md-12">
                    <table id="tableWarna" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Alias</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <h3>
                        <span class="badge badge-outline badge-default"><i class="icon md-assignment mr-2"></i> Resep
                            Pewarnaan</span>
                    </h3>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                        data-route="{{ route('database.resep.create') }}" onclick="addForm($(this), false);"><i
                            class="icon md-plus mr-2"></i> Tambah Resep</button>
                    <button type="button" onclick="tableAjaxResep.ajax.reload();"
                        class="btn btn-default btn-sm waves-effect waves-classic float-right"><i
                            class="icon md-refresh-sync spin mr-2"></i> Refresh</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table id="tableResep" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Jenis Benang</th>
                                <th>Aksi</th>
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
        let tableAjaxResep;
        $(document).ready(function() {
            tableAjax = $('#tableWarna').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: {
                    url: "{{ getCurrentRoutes() }}",
                    complete: () => {
                        (typeof(tableAjaxResep) === 'undefined') ?
                        tableResep(): tableAjaxResep.ajax.reload();
                    }
                },
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
                    name: 'name',
                    searchable: false
                }, {
                    data: 'alias',
                    name: 'alias',
                    searchable: false
                }, {
                    data: 'jenis',
                    name: 'jenis',
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

        function tableResep() {
            tableAjaxResep = $('#tableResep').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ route('database.resep.index') }}",
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
                    name: 'name',
                    searchable: false
                }, {
                    data: 'jenis_benang',
                    name: 'jenis_benang',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false

                }],
                initComplete: () => {

                }
            })
        }

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailFormDatabase') }}/${id}`,
                data: {
                    id: id,
                    model: 'Resep',
                    extrafix: 'resep'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ route('database.resep.show', ['%id']) }}`;
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
