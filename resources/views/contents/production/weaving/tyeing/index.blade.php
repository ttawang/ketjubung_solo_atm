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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableTyeing">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>No. Beam</th>
                                <th>No. Kikw</th>
                                <th>No. Loom</th>
                                <th>Sizing?</th>
                                <th>Jumlah (pcs)</th>
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
            tableAjax = $('#tableTyeing').DataTable({
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
                    data: 'tanggal_custom',
                    name: 'tanggal',
                    searchable: false
                }, {
                    data: 'no_beam',
                    name: 'no_beam',
                    searchable: false
                }, {
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'is_sizing',
                    name: 'is_sizing',
                    searchable: false
                }, {
                    data: 'jumlah_pcs',
                    name: 'jumlah_pcs',
                    searchable: false
                },{
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
                    model: 'Tyeing'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tableTyeingDetail').DataTable({
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
                    data: 'tanggal_custom',
                    name: 'tanggal',
                    searchable: false
                }, {
                    data: 'pekerja',
                    name: 'pekerja',
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

        function changeNoBeam(element) {
            if (element.val() == null) {
                resetFormBeam();
                return;
            }

            let value = element.select2('data')[0];
            let noBeam = value.no_beam || '';

            let idMesin, namaMesin = "";
            if (typeof(value['id_mesin']) !== 'undefined' && typeof(value['mesin']) !== 'undefined') {
                idMesin = value['id_mesin'];
                namaMesin = value['mesin'];
            } else {
                idMesin = value['relMesinHistoryLatest']['id_mesin'];
                namaMesin = value['relMesinHistoryLatest']['rel_mesin']['name'];
            }

            $('#wrapperFieldNoBeam').html('');
            if (noBeam != '') {
                $('#wrapperFieldNoBeam').html(`<div class="form-group">
                    <label>No. Beam</label>
                    <input type="text" class="form-control" value="${noBeam}" readonly />
                </div>`);
            }

            $('#wrapperFieldMesin').html('');
            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin</label>
                    <input type="hidden" name="id_mesin" value="${idMesin}" />
                    <input type="text" class="form-control" value="${namaMesin}" readonly />
                </div>`);
            }

            fieldSizing(value.is_sizing);
        }
    </script>
@endsection
