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
                    <table id="tableRoles" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Role</th>
                                <th>Deskripsi</th>
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
            tableAjax = $('#tableRoles').DataTable({
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
                    data: 'description',
                    name: 'description',
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

        function checkedBox(this_) {
            let checked = this_.is(':checked');
            let value = this_.val();
            let id_group = $('#id_group').val();
            $.ajax({
                url: "{{ route('helper.mappingmenu.checked') }}",
                type: "POST",
                data: {
                    is_checked: checked,
                    value: value,
                    id_group: id_group
                },
                success: (responseText) => {
                    toastr.success(responseText);
                    mapingMenuForm(id_group);
                },
                error: (res) => {
                    toastr.error(res['responseText']);
                }
            })
        }

        function mapingMenuForm(id) {
            $.ajax({
                url: `{{ url('helper/getMappingMenuForm') }}/${id}`,
                type: 'GET',
                dataType: 'html',
                success: (htmlResponse) => {
                    mainWrapper.hide();
                    formWrapper.html(htmlResponse);
                },
                error: () => {
                    toastr.error('Add Form is Not Successfuly Rendered!');
                },
                complete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }
    </script>
@endsection
