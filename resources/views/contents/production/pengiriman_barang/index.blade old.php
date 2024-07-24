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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tablePengirimanBarang">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Tipe Pengiriman</th>
                                <th>Catatan</th>
                                <th width="100px">Aksi</th>
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
            tableAjax = $('#tablePengirimanBarang').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
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
                    data: 'nomor',
                    name: 'nomor'
                }, {
                    data: 'nama_tipe_pengiriman',
                    name: 'nama_tipe_pengiriman',
                    searchable: false
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
                    model: 'PengirimanBarang'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);

            detailTable({
                state: 'input',
                status: 'ASAL',
                column: data.column_name
            }, routeWithParam);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).attr('data-table');
                let retrieve = $(this).attr('data-retrieve');
                let objects = $(this).data();
                detailTable(objects, routeWithParam, table)
                if (retrieve) tableAjaxDetail.ajax.reload();
                $(this).attr('data-retrieve', 'true')
            })
        }

        function detailTable(param, route, table = 'tablePengirimanBarangDetailInput') {
            tableAjaxDetail = $(`#${table}`).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: `${route}`,
                    type: 'GET',
                    data: param
                },
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: selectedColumn(param.column),
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function selectedColumn(columnName = 'Default') {
            let column = {};
            column['Default'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_barang',
                name: 'nama_barang'
            }, {
                data: 'gudang',
                name: 'gudang',
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
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false
            }];

            column['Beam'] = [{
                data: null,
                searchable: false,
                className: 'text-center',
                render: (render, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: null,
                name: 'no_kikw',
                searchable: false,
                render: (data) => {
                    return data.no_beam + ' / ' + data.no_kikw;
                }
            }, {
                data: 'nama_barang',
                name: 'nama_barang'
            }, {
                data: 'gudang',
                name: 'gudang',
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
                data: 'catatan',
                name: 'catatan',
                searchable: false
            }, {
                data: 'aksi',
                name: 'aksi',
                searchable: false
            }];

            return column[columnName];
        }

        function checkingTipeLainnya(element) {
            let isChecked = element.is(':checked');
            let valueTipePengiriman = element.attr('data-id-tipe-pengiriman');
            let textTipePengiriman = element.attr('data-tipe-pengiriman');
            if (isChecked) {
                $('#wrapperTipePengiriman').html(`<input type="text" value="${textTipePengiriman}" id="select_tipe_pengiriman" 
                class="form-control" name="input[txt_tipe_pengiriman]" required>`);
            } else {
                $('#wrapperTipePengiriman').html(`<select name="input[id_tipe_pengiriman]" id="select_tipe_pengiriman"
                data-placeholder="-- Pilih Tipe Pengiriman --" data-route="{{ route('helper.getTipePengiriman') }}"
                class="form-control select2" required></select>`);
                select2Element('select_tipe_pengiriman');
                selectedOption({
                    select_tipe_pengiriman: {
                        id: valuePengiriman,
                        text: textTipePengiriman
                    }
                })
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
            let namaWarna = value.nama_warna || '';
            let namaMotif = value.nama_motif || '';
            let namaSatuan1 = value.nama_satuan_1 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_1 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let currentCode = value.code || 'PB';
            let isSizing = value.is_sizing || '';
            let idBeam = value.id_beam || '';
            let noBeam = value.no_beam || '';
            let idMesin = value.id_mesin || '';
            let namaMesin = value.nama_mesin || '';
            let noKikw = value.no_kikw || '';
            let tipePraTenun = value.tipe_pra_tenun || '';
            let idGrade = value.id_grade || '';
            let namaGrade = value.nama_grade || '';
            let idKualitas = value.id_kualitas || '';
            let namaKualitas = value.nama_kualitas || '';

            $('input[name="input[id_barang]"]').val(idBarang);
            if (idBeam != '') {
                $('#wrapperFieldBeam').html(`<div class="form-group">
                    <label>No. Beam / KIKW</label>
                    <input type="text" class="form-control" value="${noBeam} / ${noKikw}" readonly/>
                    <input type="hidden" name="input[id_beam]" value="${idBeam}" />
                </div>`);
            }

            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin</label>
                    <input type="text" class="form-control" value="${namaMesin}" readonly/>
                    <input type="hidden" name="input[id_mesin]" value="${idMesin}" />
                </div>`);
            }

            if (tipePraTenun != '') {
                $('#wrapperFieldTipePraTenun').html(`<div class="form-group">
                    <label>Tipe Pra Tenun</label>
                    <input type="text" name="input[tipe_pra_tenun]" class="form-control" value="${tipePraTenun}" readonly/>
                </div>`);
            }

            if (isSizing != '') {
                $('#wrapperFieldSizing').html(`<div class="form-group">
                    <label>Sizing?</label>
                    <input type="text" class="form-control" value="${isSizing}" readonly />
                </div>`);
            }

            $('input[name="current_code"]').val(currentCode);
            let idGudangTable = parseInt($('#idGudangTable').val());
            let state = $('input[name="state"]').val();

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

            $('#wrapperFieldGrade').html('')
            if (idGrade != '') {
                $('#wrapperFieldGrade').html(`<div class="form-group"><label>Kualitas</label><input type="hidden" name="input[id_grade]" value="${idGrade}">
                <input type="text" class="form-control" id="nama_grade" value="${namaGrade}" readonly></div>`);
            }

            $('#wrapperFieldKualitas').html('')
            if (idKualitas != '') {
                $('#wrapperFieldKualitas').html(`<div class="form-group"><label>Jenis Cacat</label><input type="hidden" name="input[id_kualitas]" value="${idKualitas}">
                <input type="text" class="form-control" id="nama_kualitas" value="${namaKualitas}" readonly></div>`);
            }

            if (state == 'input') {
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

                if (typeof(value.stok_utama) != 'undefined' && typeof(value.stok_pilihan) != 'undefined') {
                    setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
                }

            } else {
                // RULE OF WAREHOUSE
                let arrGudangProses = [2, 3]; //dyeing, weaving
                let arrSatuanGudangDyeingUtama = [2]; // cones
                let arrSatuanGudangDyeingPilihan = [1]; // kg
                let arrSatuanGudangWeavingUtama = [1, 3, 4, 5]; // pcs, beam, palet
                let arrSatuanGudangWeavingPilihan = [2, 4]; // kg, pcs
                if (arrGudangProses.includes(idGudangTable)) {
                    if (idGudangTable == 2) {
                        if (!arrSatuanGudangDyeingUtama.includes(idSatuan1)) {
                            idSatuan1 = 1;
                            namaSatuan1 = 'kg'
                            valueVolume1 = '';
                        }
                        // if (!arrSatuanGudangDyeingPilihan.includes(idSatuan2)) {
                        //     idSatuan2 = 2;
                        //     namaSatuan2 = 'kg'
                        //     valueVolume2 = '';
                        // }
                    }
                }

                let objectsSelected = {
                    select_satuan_1: {
                        id: idSatuan1,
                        text: namaSatuan1
                    },
                }

                if (typeof(idSatuan2) !== 'undefined' && idSatuan2 != '') {
                    objectsSelected.select_satuan_2 = {
                        id: value.id_satuan_2,
                        text: value.nama_satuan_2
                    };
                }

                selectedOption(objectsSelected);

                // $('#txt_satuan_1').val(namaSatuan1);
                // $('#txt_satuan_2').val(namaSatuan2);
                // $('input[name="input[id_satuan_1]"]').val(idSatuan1);
                // $('input[name="input[id_satuan_2]"]').val(idSatuan2);
                $('input[name="input[volume_1]"]').val(valueVolume1);
                $('input[name="input[volume_2]"]').val(valueVolume2);
                // if (valueVolume1 != '' && valueVolume2 != '') setSuggestionStok(valueVolume1, valueVolume2, true);
            }

            let idTipe = $('input[name="id_tipe"]').val();
            fieldVolume(idBeam, idTipe == 7 || idTipe == 8);
        }
    </script>
@endsection
