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
                    <table id="tableBarang" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Nomor</th>
                                <th>Tipe</th>
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
            tableAjax = $('#tableBarang').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [15, 25, 50],
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
                    data: 'nomor',
                    name: 'nomor'
                }, {
                    data: 'tipe',
                    name: 'tipe',
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
    </script>
@endsection
