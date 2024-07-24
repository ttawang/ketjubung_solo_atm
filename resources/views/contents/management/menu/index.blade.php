@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div class="panel-body mt-20">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambah']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="tableMenu" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Menu</th>
                                <th>Link</th>
                                <th>Parent</th>
                                <th>Prefix</th>
                                <th>Icon</th>
                                <th>Urutan</th>
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
        $(document).ready(function() {
            tableAjax = $('#tableMenu').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                order: [],
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
                    data: 'link',
                    name: 'link'
                }, {
                    data: 'rel_parent.name',
                    name: 'rel_parent.name',
                    orderable: false,
                    searchable: false,
                    render: (parent) => {
                        return parent ?? '-';
                    }
                }, {
                    data: 'prefix',
                    name: 'prefix'
                }, {
                    data: 'icon',
                    name: 'icon',
                    orderable: false,
                    searchable: false,
                    render: (icon) => {
                        let useIcon = icon ?? 'fa-folder';
                        return `<i class="fas ${useIcon}"></i>`;
                    }
                }, {
                    data: 'sort_number',
                    name: 'sort_number'
                }, {
                    data: 'aksi',
                    name: 'aksi'
                }],
                initComplete: () => {

                }
            })
        })
    </script>
@endsection
