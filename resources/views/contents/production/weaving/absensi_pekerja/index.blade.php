@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambah', 'refresh'], '', ' Absensi', [], false, $checkRangeDateButton) !!}
                    <button type="button" class="btn btn-default btn-sm waves-effect waves-classic float-left mr-2"
                        id="btnTambahLembur" onclick="addFormLembur($(this));"
                        {{ $checkRangeDateButton == 'disabled' ? '' : 'disabled' }}>
                        <i class="icon md-hourglass-alt mr-2"></i>
                        Tambah Lembur
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableAbsensiPekerja">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>No. Register</th>
                                <th>Pekerja</th>
                                <th>Group</th>
                                <th>No. Loom</th>
                                <th>Shift</th>
                                <th>Lembur</th>
                                <th width="70px">Aksi</th>
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
        let start = moment().startOf('week').add(1, 'days');
        let end = moment().startOf('week').add(6, 'days');
        $(document).ready(function() {
            tableAjax = $('#tableAbsensiPekerja').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                dom: 'frtip',
                ajax: {
                    url: "{{ getCurrentRoutes() }}",
                    type: "GET",
                    data: function(e) {
                        e.tanggal = $('#filterTanggal').val() || '';
                        e.id_group = $('#filterGroup').val() || '';

                        let checkFilterMesin = $('select[name="filterMesin[]"]').length > 0;
                        if (checkFilterMesin) e.id_mesin = $("[name='filterMesin[]']").map(function() {
                            return $(this).val();
                        }).get()

                        e.shift = $('#filterShift').val() || '';
                    }
                },
                lengthMenu: [15, 25, 50],
                columns: [{
                    data: null,
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
                    data: 'no_register',
                    name: 'no_register',
                    searchable: false
                }, {
                    data: 'pekerja',
                    name: 'pekerja',
                    searchable: false
                }, {
                    data: 'group',
                    name: 'group',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'shift',
                    name: 'shift',
                    searchable: false
                }, {
                    data: 'lembur',
                    name: 'lembur',
                    searchable: false
                }, {
                    data: null,
                    render: (data) => {
                        let btnDelete = '';
                        if (data.lembur == 'YA') {
                            btnDelete = `<a href="javascript:void(0);"
                                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                                data-toggle="tooltip" data-arr-id="${data.arr_id}" onclick="deleteForm(${data.id_pekerja}, $(this));" data-original-title="Delete">
                                <i class="icon md-delete" aria-hidden="true"></i>
                            </a>`
                        }
                        return `<a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                            data-toggle="tooltip" data-filter[tanggal]="${data.tanggal}" data-filter[id_pekerja]="${data.id_pekerja}" data-filter[id_group]="${data.id_group}"
                            data-filter[shift]="${data.shift}" onclick="editForm($(this));" data-original-title="Edit">
                            <i class="icon md-edit" aria-hidden="true"></i>
                        </a> ${btnDelete}`
                    },
                    searchable: false
                }],
                initComplete: () => {
                    $('#tableAbsensiPekerja_filter').html(`
                    <div class='row'>
                        <div class='col-sm-12 col-md-9 text-left'>
                            <label>
                                <input type="date" id="filterTanggal" class="form-control form-control-sm" style="margin-left:inherit !important;" placeholder="" aria-controls="tableAbsensiPekerja">
                            </label>
                            <label>
                                <select id="filterGroup" class="form-control form-control-sm" aria-controls="tableAbsensiPekerja">
                                    <option value="">-- Pilih Group --</option>
                                    <option value="1">Group 1</option>
                                    <option value="2">Group 2</option>
                                    <option value="3">Group 3</option>
                                </select>
                            </label>
                            <label>
                                <select id="filterShift" class="form-control form-control-sm" aria-controls="tableAbsensiPekerja">
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="PAGI">PAGI</option>
                                    <option value="MALAM">MALAM</option>
                                    <option value="SIANG">SIANG</option>
                                </select>
                            </label>
                            <label>
                                <select name="filterMesin[]" multiple class="form-control form-control-sm select2" data-jenis="LOOM" data-route="{{ route('helper.getMesin') }}" data-placeholder="-- Pilih Mesin --" aria-controls="tableAbsensiPekerja">
                                </select>
                            </label>
                            <label>
                                <button type="button" class="btn btn-default btn-sm waves-effect waves-classic" onclick="tableAjax.ajax.reload();">
                                    <i class="icon md-filter-list mr-2"></i>
                                    Filter
                                </button>
                            </label>
                        </div>
                        <div class='col-sm-12 col-md-3'>
                            <label>Search:
                                <input type="search" onkeyup="onSearching($(this));" class="form-control form-control-sm" placeholder="Nama Pekerja, No. Register" aria-controls="tableAbsensiPekerja">
                            </label>
                        </div>
                    </div>`);

                    initSelect2(false, false, '200px');
                }
            })
        })

        function onSearching(element) {
            tableAjax.search(element.val()).draw();
        }

        function changeShift(element) {
            $('#wrapperShiftManual').html('');
            if (element.val() == 'manual') {
                $('#wrapperShiftManual').html(`
                <div class="form-group">
                    <label>Group 1</label>
                    <select name="shift[1]" id="select_shift_1" data-placeholder="-- Pilih Shift --"
                        class="form-control select2" required>
                        <option value=""></option>
                        <option value="PAGI">PAGI</option>
                        <option value="SIANG">SIANG</option>
                        <option value="MALAM">MALAM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Group 2</label>
                    <select name="shift[2]" id="select_shift_2" data-placeholder="-- Pilih Shift --"
                        class="form-control select2" required>
                        <option value=""></option>
                        <option value="PAGI">PAGI</option>
                        <option value="SIANG">SIANG</option>
                        <option value="MALAM">MALAM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Group 3</label>
                    <select name="shift[3]" id="select_shift_3" data-placeholder="-- Pilih Shift --"
                        class="form-control select2" required>
                        <option value=""></option>
                        <option value="PAGI">PAGI</option>
                        <option value="SIANG">SIANG</option>
                        <option value="MALAM">MALAM</option>
                    </select>
                </div>
                `);
                initSelect2();
            }
        }

        function setTextDate(start, end) {
            let valueDate =
                `${start.format('DD-MM-YYYY')} - ${end.format('DD-MM-YYYY')}`;
            $('#reportrange span').html(valueDate);
            $('input[name="range"]').val(valueDate);
        }

        function changePekerja(element) {
            let value = element.select2('data')[0];
            let idGroup = value.id_group;
            let namaGroup = value.nama_group;
            $('input[name="input[id_group]"]').val(idGroup);
            $('#txt_group').val(namaGroup);
        }

        function changeDate(start, end) {
            $('#reportrange').daterangepicker({
                parentEl: $('.jconfirm'),
                opens: 'left',
                locale: {
                    format: 'DD-MM-YYYY',
                },
                startDate: start,
                endDate: end,
                minDate: moment().startOf('week'),
                maxDate: moment().startOf('week').add(6,
                    'days'
                ),
            }, setTextDate);

            setTextDate(start, end);
        }

        function submitForm(event, this_) {
            event.preventDefault();
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                success: (message) => {
                    $('#btnTambah').prop('disabled', true);
                    $('#btnTambahLembur').prop('disabled', false);
                    toastr.success(message);
                    refreshTable(false);
                },
                complete: () => {}
            })
        }

        function addForm(this_, isDetail = false) {
            let data = this_.data();
            data.isDetail = isDetail;
            let route = `{{ getCurrentRoutes('create') }}`;
            if (typeof(data.route) !== 'undefined' && data.route != '') route = data.route;
            $.ajax({
                url: route,
                type: 'GET',
                data: data,
                dataType: 'json',
                success: (response) => {
                    showForm({
                        content: response['render'],
                        callback: () => {
                            setTextDate(start, end);
                            // changeDate(start, end);
                        }
                    });
                }
            })
        }

        function addFormLembur(element) {
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `@include('contents.production.weaving.absensi_pekerja.form-lembur')`,
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
                    initSelect2();

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

        function editForm(this_) {
            let data = this_.data();
            let route = `{{ getCurrentRoutes('edit', ['%id']) }}`;
            let routeWithParam = route.replace('%id', data.idPekerja);
            $.ajax({
                url: routeWithParam,
                type: 'GET',
                data: data,
                dataType: 'json',
                success: (response) => {
                    showForm({
                        content: response['render'],
                        callback: () => {
                            selectedOption(response['selected']);
                        }
                    });
                }
            })
        }

        function deleteForm(id, this_) {
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Delete Data?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'delete',
                callback: () => {
                    let routeDelete = "{{ getCurrentRoutes('destroy', ['%id']) }}";
                    let routeWithParam = routeDelete.replace('%id', id);
                    let data = this_.data();
                    data._method = 'DELETE';
                    if (typeof(data.route) !== 'undefined' && data.route != '') {
                        let customRoute = data.route;
                        routeWithParam = customRoute.replace('%id', id);
                    }
                    $.ajax({
                        url: routeWithParam,
                        type: "POST",
                        data: data,
                        success: (responseText) => {
                            toastr.success(responseText);
                            refreshTable(false);
                        },
                        error: (res) => {
                            toastr.error(res['responseText']);
                        },
                        complete: () => {}
                    })
                }
            });
        }
    </script>
@endsection
