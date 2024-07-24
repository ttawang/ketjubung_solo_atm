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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableTenun">
                        <thead>
                            <tr>
                                {{-- <th width="30px">#</th> --}}
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>No. Beam</th>
                                <th>No. Kikw</th>
                                <th>No. Loom</th>
                                <th>Warna</th>
                                <th>Pemotongan</th>
                                <th>Status</th>
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
        let jumlahBeam = 0;
        let boolStokSongket = [true];
        let attachOptionSongket = "";
        $(document).ready(function() {
            tableAjax = $('#tableTenun').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: [/* {
                        data: null,
                        searchable: false,
                        className: 'text-center',
                        render: (render, type, row, meta) => {
                            return `<button type='button' id='btnCetak' data-model='Tenun' data-id='${row.id}' class='btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic' onclick='cetakForm($(this));'>
                        <i class='icon md-print'></i>
                    </button>`;
                        }
                    }, */
                    {
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
                        data: 'warna',
                        name: 'warna',
                        searchable: false
                    }, {
                        data: null,
                        name: 'pemotongan',
                        searchable: false,
                        render: (data) => {
                            return data['jumlah_beam'] + '/' + parseFloat(data['rel_tenun_detail_sum_volume_1'] || 0)
                        }
                    }, {
                        data: null,
                        searchable: false,
                        render: (data) => {
                            if (data.is_finish) {
                                return `<span class="badge badge-outline badge-primary">Selesai</span>`;
                            } else {
                                return `<span class="badge badge-outline badge-warning">Belum Selesai</span>`;
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
                    }
                ],
                initComplete: () => {

                }
            })
        })

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailForm') }}/${id}`,
                data: {
                    id: id,
                    model: 'Tenun'
                },
                callback: (response) => {
                    let checkBeamFinish = response['data'].is_finish;
                    $('.btnFinish').prop('disabled', checkBeamFinish);
                    if (checkBeamFinish) $('button.btn-template').replaceWith('');
                    select2Mesin();
                }
            })
        }

        function select2Mesin(isReset = false) {
            select2Element('select_mesin', isReset, false);
            let objects = {
                'select_mesin': {
                    id: $('#hidden_id_mesin').val(),
                    text: $('#hidden_mesin').val()
                }
            }
            selectedOption(objects);
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            jumlahBeam = parseFloat(data.jumlah_beam) || 0;

            detailTable({
                table: 'tableTenunInput',
                isFinish: data.is_finish ? 'true' : 'false',
                jumlah_beam: data.jumlah_beam,
                form: 'input'
            }, routeWithParam);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).attr('data-table');
                let column = $(this).attr('data-column');
                let retrieve = $(this).attr('data-retrieve') == 'true';
                let objects = $(this).data();
                objects.jumlah_beam = data.jumlah_beam;
                detailTable(objects, routeWithParam, table, column)
                if (retrieve) tableAjaxDetail.ajax.reload();
                $(this).attr('data-retrieve', 'true')
            })
        }

        function detailTable(param, route, table = 'tableTenunInput', column = 'TenunInput') {
            tableAjaxDetail = $(`#${table}`).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: `${route}`,
                    beforeSend: () => {
                        jumlahBeam = parseFloat(param.jumlah_beam);
                        boolStokSongket = [true];
                        $('.panel').addClass('is-loading');
                    },
                    type: 'GET',
                    data: param
                },
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: selectedColumn(column),
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function changeNoBeam(element) {
            let value = element.select2('data')[0];
            if (element.val() == null) resetFormBeam();
            let noBeam = value.no_beam || '';
            let namaMesin = (typeof(value['mesin']) !== 'undefined') ?
                value['mesin'] : value['relMesinHistoryLatest']['rel_mesin']['name'];
            let tipePraTenun = value.tipe_pra_tenun || '';
            $('#wrapperFieldNoBeam').html('');
            if (noBeam != '') {
                $('#wrapperFieldNoBeam').html(`<div class="form-group">
                    <label>No. Beam</label>
                    <input type="text" class="form-control" value="${noBeam}" readonly />
                </div>`);
            }

            $('#wrapperFieldMesin').html('');
            if (namaMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin</label>
                    <input type="text" class="form-control" value="${namaMesin}" readonly />
                </div>`);
            }

            $('#wrapperFieldTipePraTenun').html('');
            if (tipePraTenun != '') {
                $('#wrapperFieldTipePraTenun').html(`<div class="form-group">
                    <label>Tipe Pra Tenun</label>
                    <input type="text" class="form-control" value="${tipePraTenun}" readonly />
                </div>`);
            }

            fieldSizing(value.is_sizing);
        }

        function editForm(id, isDetail = false, this_) {
            let route = `{{ getCurrentRoutes('edit', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            let data = this_.data();
            if (typeof(data.route) !== 'undefined' && data.route != '') {
                let customRoute = data.route;
                routeWithParam = customRoute.replace('%id', id);
            }
            data.isDetail = isDetail;
            $.ajax({
                url: routeWithParam,
                type: 'GET',
                data: data,
                dataType: 'json',
                success: (response) => {
                    showForm({
                        content: response['render'],
                        callback: () => {
                            if (isDetail) $('#select_tipe_barang')
                                .val(response['data']['code'])
                                .change();
                            selectedOption(response['selected']);
                        }
                    });
                }
            })
        }

        function submitFormDetail(event, this_) {
            event.preventDefault();
            let objects = this_.serializeArray();
            objects.push({
                name: 'input[id_mesin]',
                value: $('#hidden_id_mesin').val()
            });
            // let checkMesin = $('#select_mesin').val();
            // if (typeof(checkMesin) !== 'undefined') {
            //     objects.push({
            //         name: 'input[id_mesin]',
            //         value: $('#select_mesin').val()
            //     })
            // }
            let isDetail = $('input[name="isDetail"]').val();
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: objects,
                success: (message) => {
                    toastr.success(message);
                    refreshTable(isDetail == 'true');
                },
                complete: () => {}
            })
        }

        function changeTipeBarang(element) {
            $('#select_barang').val(null).empty();
            let value = element.val();
            let idBeam = element.attr('data-id-beam');
            $('#select_barang').data('filter-code', value);
            if (value == 'DPR' || value == 'DPS' || value == 'DPRT' || value == 'DPST') {
                $('#select_barang').data('filter-beam', idBeam);
                $('#select_barang').removeData('filter-id-mesin');
            } else if (value == 'BBTS' || value == 'BBTST') {
                let idMesin = $('#hidden_id_mesin').val();
                $('#select_barang').data('filter-id-mesin', idMesin);
                $('#select_barang').removeData('filter-beam');
            } else {
                $('#select_barang').removeData('filter-id-mesin');
                $('#select_barang').removeData('filter-beam');
            }
        }

        function changeBarang(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang || '';
            let idWarna = value.id_warna || '';
            let idMotif = value.id_motif || '';
            let idSatuan1 = value.id_satuan_1 || '';
            let idSatuan2 = value.id_satuan_2 || '';
            let namaSatuan1 = value.nama_satuan_1 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let idGrade = value.id_grade || '';
            let idKualitas = value.id_kualitas || '';
            let namaWarna = value.nama_warna || '';
            let namaMotif = value.nama_motif || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_1 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let idBeam = value.id_beam || '';

            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[code]"]').val(value.code);
            $('input[name="input[id_beam]"]').val(idBeam);

            $('#wrapperFieldWarna').html('')
            if (idWarna != '') {
                $('#wrapperFieldWarna').html(`<div class="form-group"><label>Warna</label><input type="hidden" name="input[id_warna]" value="${idWarna}">
                <input type="text" class="form-control" id="nama_warna" value="${namaWarna}" readonly></div>`);
            }

            $('#wrapperFieldMotif').html('')
            if (idMotif != '') {
                $('#wrapperFieldMotif').html(`<div class="form-group"><label>Motif</label><input type="hidden" name="input[id_motif]" value="${idMotif}">
                <input type="text" class="form-control" id="nama_motif" value="${namaMotif}" readonly></div>`);
            }

            $('input[name="input[id_satuan_1]"]').val(idSatuan1);
            $('#txt_satuan_1').val(namaSatuan1);
            $('input[name="input[volume_1]"]').val(valueVolume1 || valueStokUtama);

            $('#wrapperFieldSatuan2').html('');
            if (valueStokPilihan != '' || valueVolume2 != '') {
                $('#wrapperFieldSatuan2').html(`<div class="form-group">
                    <label>Satuan 2 (Pilihan)</label>
                    <input type="hidden" name="input[id_satuan_2]" value="${idSatuan2}">
                    <input type="text" class="form-control" id="txt_satuan_2" value="${namaSatuan2}" readonly>
                </div>
                <div class="form-group">
                    <label>Volume 2</label>
                    <input type="text" class="form-control number-only" name="input[volume_2]" value="${valueVolume2 || valueStokPilihan}" required>
                    <div id="wrapperSuggestionStok2"></div>
                </div>`);
            }

            if (typeof(value.stok_utama) != 'undefined' && typeof(value.stok_utama) != 'undefined') {
                setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
            }

            fieldVolume(idBeam, value.code != 'BBTL' && value.code != 'BBTS');
        }

        function changeSongket(element) {
            let value = element.select2('data')[0];
            let stokSongket = value.stok;
            if (typeof(stokSongket) !== 'undefined') {
                if (stokSongket <= 0) {
                    toastr.warning('Stok songket tersebut sudah habis!');
                    $('input[name="input[volume_1]"]').data('max', 0);
                } else {
                    $('input[name="input[volume_1]"]').data('max', stokSongket);
                }
                setSuggestionStok(stokSongket);
            }
        }

        function changeBarangTurun(element) {
            if (element.val() == null) {
                resetForm();
                $('input[name="input[id_warna]"]').val('');
                $('input[name="input[id_motif]"]').val('');
                return;
            }

            let value = element.select2('data')[0];
            let idDetail = $('#idDetail').val();
            let idBarang = value.id_barang || '';
            let idWarna = value.id_warna || '';
            let idMotif = value.id_motif || '';
            let idSatuan1 = value.id_satuan_1 || '';
            let idSatuan2 = value.id_satuan_2 || '';
            let namaSatuan1 = value.nama_satuan_1 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_1 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let idBeam = value.id_beam || '';

            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[code]"]').val(`${value.code}T`);
            $('input[name="input[id_beam]"]').val(idBeam);
            $('input[name="input[id_warna]"]').val(idWarna);
            $('input[name="input[id_motif]"]').val(idMotif);

            $('input[name="input[id_satuan_1]"]').val(idSatuan1);
            $('#txt_satuan_1').val(namaSatuan1);
            $('input[name="input[volume_1]"]').val(valueVolume1);

            $('#wrapperFieldSatuan2').html('');
            if (valueVolume2 != '') {
                if (idDetail == '' && (value.code == 'BBTS' || value.code == 'BBTST')) valueVolume2 = '';
                $('#wrapperFieldSatuan2').html(`<div class="form-group">
                    <label>Satuan 2 (Pilihan)</label>
                    <input type="hidden" name="input[id_satuan_2]" value="${idSatuan2}">
                    <input type="text" class="form-control" id="txt_satuan_2" value="${namaSatuan2}" readonly>
                </div>
                <div class="form-group">
                    <label>Volume 2</label>
                    <input type="text" class="form-control number-only" name="input[volume_2]" value="${valueVolume2}" required>
                    <div id="wrapperSuggestionStok2"></div>
                </div>`);
            }

            fieldVolume(idBeam, value.code != 'BBTL' && value.code != 'BBTLT');

            if (value.code == 'BBTS' || value.code == 'BBTST') $('input[name="input[volume_1]"]').prop('readonly', true);
        }

        function changeGroup(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            $('#select_pekerja').empty().val('').data('id-group', element.val() || 0);
        }

        function changeGrade(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            let value = element.select2('data')[0];
            $('#wrapperFieldKualitas').html('');
            if (element.val() != null && element.val() != 1) {
                $('#wrapperFieldKualitas').html(`<div class="form-group">
                    <label>Kualitas</label>
                    <select id="select_kualitas" name="input[id_kualitas]" data-id-grade="${value.id}"
                        data-placeholder="-- Pilih Kualitas --" data-route="{{ route('helper.getKualitas') }}"
                        class="form-control select2" required>
                    </select>
                </div>`);
                select2Element('select_kualitas', true)
            }
        }

        function selectedColumn(columnName = 'TenunInput') {
            let column = {};
            column['TenunInput'] = [{
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
                data: 'nama_barang',
                name: 'nama_barang',
                searchable: false
            }, {
                data: 'mesin',
                name: 'mesin',
                searchable: false
            }, {
                data: 'volume_1',
                name: 'volume_1',
                searchable: false,
                render: (volume, display, data) => {
                    return `${formatNumber(volume, data.satuan_utama)}`;
                }
            }, {
                data: 'satuan_utama',
                name: 'satuan_utama',
                orderable: false,
                searchable: false
            }, {
                data: 'volume_2',
                name: 'volume_2',
                searchable: false,
                render: (volume, display, data) => {
                    return `${formatNumber(volume, data.satuan_pilihan)}`;
                }
            }, {
                data: 'satuan_pilihan',
                name: 'satuan_pilihan',
                searchable: false
            }, {
                data: 'proses',
                name: 'proses',
                searchable: false,
                render: (proses, display, data) => {
                    if (proses == 'Diturunkan') {
                        return '<span class="badge badge-outline badge-danger">Diturunkan</span>';
                    } else if (proses == 'Diproses') {
                        if (data['code'] == 'BBTS') {
                            let currStokSongket = parseFloat(data['volume_2']);
                            let stokSongketDipakai = data['rel_songket_potong'].reduce(function(a, b) {
                                return parseFloat(a) + parseFloat(b['volume_1']);
                            }, 0);
                            boolStokSongket.push(currStokSongket <= stokSongketDipakai);
                            return (currStokSongket <= stokSongketDipakai) ?
                                '<span class="badge badge-outline badge-default">Stok Habis</span>' :
                                '<span class="badge badge-outline badge-primary">Diproses</span>';
                        }
                        return '<span class="badge badge-outline badge-primary">Diproses</span>';
                    }
                }
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false,
                render: (html, display, data) => {
                    if (data['proses'] == 'Diturunkan' || data['rel_songket_potong'].length > 0) return '';

                    if (!data['rel_tenun']['is_finish']) {
                        if (data['code'] == 'BBTS' && data['rel_beam']['finish'] == 1) {
                            return `<a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure" data-id-beam[]="${data['id_beam']}" data-id-tenun="${data['id_tenun']}" data-rollback="true" data-tipe-beam="songket" 
                            btn-default on-default waves-effect waves-classic" data-toggle="tooltip" data-original-title="Rollback" onclick="rollbackBeam($(this));">
                            <i class="icon md-refresh-sync-alert" aria-hidden="true"></i>
                        </a>`;
                        } else {
                            return html;
                        }
                    } else {
                        return html;
                    }
                },
            }];

            column['TenunOutput'] = [{
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
                    data: 'songket',
                    name: 'songket',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'volume_1',
                    name: 'volume_1',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume)}`;
                    }
                }, {
                    data: 'sisa',
                    name: 'sisa',
                    searchable: false,
                    // render: (data) => {
                    //     jumlahBeam = jumlahBeam - data.volume_1;
                    //     $('#spanSisaBeam').text(`Sisa Beam : ${jumlahBeam} Pcs`);
                    //     return parseFloat(jumlahBeam);
                    // }
                },
                //  {
                //     data: 'aksi',
                //     name: 'aksi',
                //     searchable: false
                // }
            ];

            column['TenunDiturunkan'] = [{
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
                data: 'nama_barang',
                name: 'nama_barang'
            }, {
                data: 'mesin',
                name: 'mesin',
                searchable: false
            }, {
                data: 'volume_1',
                name: 'volume_1',
                searchable: false,
                render: (volume, display, data) => {
                    return `${formatNumber(volume, data.satuan_utama)}`;
                }
            }, {
                data: 'satuan_utama',
                name: 'satuan_utama',
                searchable: false
            }, {
                data: 'volume_2',
                name: 'volume_2',
                searchable: false,
                render: (volume, display, data) => {
                    return `${formatNumber(volume, data.satuan_pilihan)}`;
                }
            }, {
                data: 'satuan_pilihan',
                name: 'satuan_pilihan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false,
                render: (html, display, data) => {
                    if (!data['rel_tenun']['is_finish']) {
                        if (data['code'] == 'BBTS' && data['rel_beam']['finish'] == 1) {
                            return `<a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-pure" data-id-beam[]="${data['id_beam']}" data-id-tenun="${data['id_tenun']}" data-rollback="true" data-tipe-beam="songket" 
                            btn-default on-default waves-effect waves-classic" data-toggle="tooltip" data-original-title="Rollback" onclick="rollbackBeam($(this));">
                            <i class="icon md-refresh-sync-alert" aria-hidden="true"></i>
                        </a>`;
                        } else {
                            return html;
                        }
                    } else {
                        return html;
                    }
                },
            }];

            return column[columnName];
        }

        function finishBeam(element) {
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Beam telah selesai?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'saveorupdate',
                textButton: 'Selesai',
                callback: () => {
                    submitPostFinishBeam(element.data());
                }
            });
        }

        function rollbackBeam(element) {
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Mengembalikan Proses Beam?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'delete',
                textButton: 'Rollback',
                callback: () => {
                    submitPostFinishBeam(element.data());
                }
            });
        }

        function finishSongket(element) {
            jqueryConfirm = $.confirm({
                title: '<i class="icon md-assignment mr-2"></i> Form',
                content: `<form action="{{ route('helper.checkedBeam') }}" method="POST" class="formInput" style="height: 200px;">
                        <input type="hidden" name="rollback" value="${element.data('rollback')}">
                        <input type="hidden" name="idTenun" value="${element.data('id-tenun')}">
                        <input type="hidden" name="tipeBeam" value="${element.data('tipe-beam')}">
                        <div class="form-group">
                            <label>Beam Songket</label>
                            <select id="select_beam_songket" multiple name="idBeam[]" data-id-tenun="${element.data('id-tenun')}"
                                data-route="{{ route('helper.getBeamSongket') }}" data-placeholder="-- Pilih Beam Songket --"
                                class="form-control select2" required>
                            </select>
                        </div>
                    </form>`,
                columnClass: 'col-md-4 col-md-offset-4',
                typeAnimated: true,
                type: 'dark',
                theme: 'material',
                onOpenBefore: function() {
                    this.showLoading();
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
                            submitPostFinishBeam(form.serializeArray());
                        }
                    },
                },
                onContentReady: function() {
                    select2Element('select_beam_songket');

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

                    jqueryConfirm.hideLoading();
                }
            });
        }

        function submitPostFinishBeam(objects) {
            $.ajax({
                url: "{{ route('helper.checkedBeam') }}",
                type: 'POST',
                data: objects,
                dataType: 'json',
                success: (response) => {
                    toastr.success(response['message']);
                    goToDetail(response['id']);
                }
            })
        }

        function changeMesin(element) {
            let value = element.val();
            let currIdMesin = $('#hidden_id_mesin').val();
            let currIdBeam = $('#hidden_id_beam').val();
            let currIdTenun = $('#hidden_id').val();
            $('#wrapperButtonApplyMesin').html('');
            if (value != currIdMesin) {
                $('#wrapperButtonApplyMesin').html(`<button type="button" data-input[id_mesin]="${value}" data-input[id_beam]="${currIdBeam}" class="btn btn-primary btn-sm waves-effect waves-classic" 
                onclick="applyMesin($(this), ${currIdTenun});">Apply</button>`)
            }
        }

        function applyMesin(element, idTenun) {
            let objects = element.data();
            $.ajax({
                url: "{{ route('helper.applyMesin') }}",
                type: "POST",
                data: objects,
                success: (message) => {
                    goToDetail(idTenun);
                    toastr.success(message);
                },
                complete: () => {}
            })
        }
    </script>
@endsection
