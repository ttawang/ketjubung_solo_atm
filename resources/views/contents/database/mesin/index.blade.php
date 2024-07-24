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
                    <table id="tableMesin" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No Mesin</th>
                                <th>Jenis</th>
                                <th>Proses</th>
                                <th>Pekerja</th>
                                <th width="120px">Aksi</th>
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
        let jqueryConfirmPekerjaMesin;
        $(document).ready(function() {
            tableAjax = $('#tableMesin').DataTable({
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
                    data: 'tipe',
                    name: 'tipe'
                }, {
                    data: 'jenis',
                    name: 'jenis'
                }, {
                    data: 'pekerja_mesin',
                    name: 'pekerja_mesin',
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

        function addPekerjaMesin(idMesin, element) {
            jqueryConfirmPekerjaMesin = $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="{{ route('helper.storePekerjaMesin') }}" onsubmit="submitFormPekerjaMesin(event, $(this));" method="POST" class="formInput" style="height: 200px;">
                    <input type="hidden" name="input[id_mesin]" value="${idMesin}">
                    <div class="form-group">
                        <label>Pekerja</label>
                        <select id="select_pekerja" name="input[id_pekerja][]" multiple data-placeholder="-- Pilih Pekerja --"
                            data-route="{{ route('helper.getPekerja') }}" data-id-parent="${idMesin}" class="form-control select2" required>
                        </select>
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
                    $.ajax({
                        url: element.data('route'),
                        beforeSend: () => {},
                        type: 'GET',
                        success: (response) => {
                            select2Element('select_pekerja');
                            selectedOption(response)
                        },
                        complete: () => {

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
                    })
                }
            });
        }

        function submitFormPekerjaMesin(event, element) {
            event.preventDefault();
            $.ajax({
                url: element.attr('action'),
                type: element.attr('method'),
                data: element.serialize(),
                success: (response) => {
                    toastr.success(response);
                    tableAjax.ajax.reload();
                },
            })
        }
    </script>
@endsection
