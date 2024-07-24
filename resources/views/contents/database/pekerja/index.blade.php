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
                    <table id="tablePekerja" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Register</th>
                                <th>Nama</th>
                                <th>No. Hp</th>
                                <th>Group</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
            {{-- <div class="form-group row">
                <div class="col-md-12">
                    <h3>
                        <span class="badge badge-outline badge-default"><i class="icon md-assignment mr-2"></i> Group</span>
                    </h3>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
                        data-route="{{ route('database.group.create') }}" onclick="addForm($(this), false);"><i
                            class="icon md-accounts mr-2"></i> Tambah Group</button>
                    <button type="button" onclick="tableAjaxPekerja.ajax.reload();"
                        class="btn btn-default btn-sm waves-effect waves-classic float-right"><i
                            class="icon md-refresh-sync spin mr-2"></i> Refresh</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table id="tableGroup" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div> --}}
        </div>
        <div class="panel-loading">
            <div class="loader loader-grill"></div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        let tableAjaxPekerja;
        $(document).ready(function() {
            tableAjax = $('#tablePekerja').DataTable({
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
                    data: 'no_register',
                    name: 'no_register',
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name',
                    searchable: false
                }, {
                    data: 'no_hp',
                    name: 'no_hp',
                    searchable: false
                }, {
                    data: 'group',
                    name: 'group',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }]
            })

            tableAjaxPekerja = $('#tableGroup').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ route('database.group.index') }}",
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
                    model: 'Group',
                    extrafix: 'group'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ route('database.group.show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $(`#tableGroupDetail`).DataTable({
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
                    data: 'pekerja',
                    name: 'pekerja',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }],
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }
    </script>
@endsection
