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
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDistribusiPakan">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Tipe</th>
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
            tableAjax = $('#tableDistribusiPakan').DataTable({
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
                    data: 'nomor',
                    name: 'nomor',
                    searchable: false
                }, {
                    data: 'tipe',
                    name: 'tipe',
                    searchable: false,
                    render: (data) => {
                        let textHtml = data.toUpperCase();
                        if (data == 'shuttle') {
                            return `<span class="badge badge-outline badge-primary">${textHtml}</span>`;
                        } else if (data == 'rappier') {
                            return `<span class="badge badge-outline badge-danger">${textHtml}</span>`;
                        } else {
                            return `<span class="badge badge-outline badge-default">${textHtml}</span>`;
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
                    model: 'DistribusiPakan'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tableDistribusiPakanDetail').DataTable({
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
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
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
                        return `${formatNumber(volume, data.satuan_utama)}`;
                    }
                }, {
                    data: 'satuan_pilihan',
                    name: 'satuan_pilihan',
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

            let tipePraTenun = value.tipe_pra_tenun || '';
            let isSizing = value.is_sizing || '';
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
                    <input type="hidden" name="id_mesin" value="${idMesin}">
                    <input type="text" class="form-control" value="${namaMesin}" readonly />
                </div>`);
            }

            $('#wrapperFieldTipePraTenun').html('');
            if (tipePraTenun != '') {
                $('#wrapperFieldTipePraTenun').html(`<div class="form-group">
                    <label>Tipe Pra Tenun</label>
                    <input type="text" name="tipe_pra_tenun" class="form-control" value="${tipePraTenun}" readonly />
                </div>`);
            }

            fieldSizing(value.is_sizing);
        }

        function changeBarang(element) {
            if (element.val() == null) {
                resetForm();
                return;
            }

            let value = element.select2('data')[0];
            let satuan = value.id_satuan_1 || '';
            let satuanNama = value.nama_satuan_1 || ''
            let idSatuan2 = value.id_satuan_2 || '';
            let namaSatuan2 = value.nama_satuan_2 || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            let valueVolume2 = (value.volume_2 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let idWarna = value.id_warna || '';
            let namaWarna = value.nama_warna || '';
            let idBarang = value.id_barang || '';
            let idGudang = value.id_gudang || '';

            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[id_gudang]"]').val(idGudang);
            $('input[name="input[id_satuan_1]"]').val(satuan);
            $('#txt_satuan_1').val(satuanNama);
            $('input[name="input[volume_1]"]').val(valueVolume1 || valueStokUtama);

            $('#wrapperFieldWarna').html('')
            if (idWarna != '') {
                $('#wrapperFieldWarna').html(`<div class="form-group"><label>Warna</label><input type="hidden" name="input[id_warna]" value="${idWarna}">
                <input type="text" class="form-control" id="nama_warna" value="${namaWarna}" readonly></div>`);
            }

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

            if (typeof(value.stok_utama) !== 'undefined' && typeof(value.stok_pilihan) !== 'undefined') {
                setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
            }
        }
    </script>
@endsection
