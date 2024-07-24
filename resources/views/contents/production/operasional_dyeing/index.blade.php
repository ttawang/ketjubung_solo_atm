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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0"
                        id="tableOperasionalDyeing">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Proses</th>
                                <th>Catatan</th>
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
            tableAjax = $('#tableOperasionalDyeing').DataTable({
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
                    data: 'tanggal',
                    name: 'tanggal',
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    searchable: false
                }, {
                    data: 'proses',
                    name: 'proses',
                    searchable: false,
                    render: (data) => {
                        if (data == 'LIMBAH') {
                            return `<span class="badge badge-outline badge-primary">${data}</span>`;
                        } else if (data == 'CUCI MESIN') {
                            return `<span class="badge badge-outline badge-danger">${data}</span>`;
                        }
                    }
                }, {
                    data: 'catatan',
                    name: 'catatan',
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
                    model: 'OperasionalDyeing'
                },
                callback: () => {
                    // initSelect2(false, false);
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $(`#tableOperasionalDyeingDetail`).DataTable({
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
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: null,
                    name: 'volume',
                    searchable: false,
                    render: (data) => {
                        return `${data.volume} ${data.satuan_utama}`;
                    }
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

        function changeGudang(element) {
            let isEdit = $('#idDetail').val() != '';
            if (!isEdit) {
                let value = element.val() || 9999;
                $('#select_gudang').val(value);
                select2Element('select_barang', true);
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
            }
        }

        function changeBarang(element) {
            if (element.val() == null) {
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang;
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let volume1 = value.volume_1 || '';
            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[volume]"]').val(volume1);

            if (typeof(value.stok_utama) != 'undefined') {
                $('#wrapperSuggestionStok1').html(`<div class="text-right">
                    <span class="font-size-12">Stok: ${valueStokUtama}</span>
                </div>`);
            }
        }

        function addFormResep(element) {
            let idOperasionalDyeing = element.attr('data-id-operasional-dyeing');
            let tanggal = element.attr('data-tanggal');
            $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="{{ route('helper.storeResepWarnaOD') }}" onsubmit="submitFormWarna(event, $(this));" method="POST" class="formInput" style="height: 400px;">
                    <input type="hidden" name="input[id_operasional_dyeing]" value="${idOperasionalDyeing}">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="input[tanggal]" value="${tanggal}" required>
                    </div>
                    <div class="form-group">
                        <label>Resep</label>
                        <select id="select_resep" name="id_resep" onchange="changeResep($(this));" data-placeholder="-- Pilih Resep --"
                            data-route="{{ route('helper.getResep') }}" class="form-control select2" required>
                        </select>
                    </div>
                    <div class="form-group" id="wrapperListResep"></div>
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
                    $('#wrapperListResep').html('');
                    this.$content.find('input.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });
                    select2Element('select_resep')

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

        function changeResep(element) {
            $('#wrapperListResep').html('');
            let value = element.select2('data')[0];
            if (value['resep_detail'].length > 0) {
                let template = `<label>Daftar Pewarna: </label>`;
                template += `<ul class="list-group list-group-full">`;
                value['resep_detail'].forEach((element, index) => {
                    template +=
                        `<li class="list-group-item">${++index}. ${element['rel_barang'].name} (${element.volume} ${element.satuan})</li>`
                });
                template += `</ul>`
                $('#wrapperListResep').html(template);
            }
        }

        function submitFormWarna(event, element) {
            event.preventDefault();
            $.ajax({
                url: element.attr('action'),
                type: element.attr('method'),
                data: element.serialize(),
                success: (response) => {
                    toastr.success(response);
                    tableAjaxDetail.ajax.reload();
                },
                complete: () => {}
            })
        }
    </script>
@endsection
