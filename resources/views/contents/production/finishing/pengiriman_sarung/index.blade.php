@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">

            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-retrieve="true" data-tipe="LOKAL" data-table="tablePengirimanSarung"
                            data-toggle="tab" href="#tabLokal" role="tab">
                            <i class="icon md-refresh-sync spin mr-2"></i> Lokal Gresik
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-retrieve="false" data-tipe="LUAR" data-table="tablePengirimanSarungLuar"
                            data-id-pengiriman-barang="" data-toggle="tab" href="#tabLuar" role="tab">
                            <i class="icon md-refresh-sync spin mr-2"></i> Luar Gresik & Finished Goods
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-20">
                    <div class="tab-pane active" id="tabLokal" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! App\Helpers\Template::tools(['tambah', 'refresh'], '', 'Data', ['tipe' => 'LOKAL']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-striped" cellspacing="0"
                                    id="tablePengirimanSarung">
                                    <thead>
                                        <tr>
                                            <th width="30px">#</th>
                                            <th width="30px">No</th>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Total Pcs</th>
                                            <th data-visible="false">Tipe</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabLuar" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! App\Helpers\Template::tools(['tambah', 'refresh'], '', 'Data', ['tipe' => 'LUAR']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-striped" cellspacing="0"
                                    id="tablePengirimanSarungLuar">
                                    <thead>
                                        <tr>
                                            <th width="30px">#</th>
                                            <th width="30px">No</th>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Total Pcs</th>
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
            let route = "{{ getCurrentRoutes() }}";

            initTable({
                tipe: 'LOKAL'
            }, route);

            $('ul[role="tablist"] li a').on('click', function() {
                let table = $(this).data('table');
                let retrieve = $(this).data('retrieve');
                let objects = $(this).data();
                initTable(objects, route, table)
                if (retrieve) tableAjax.ajax.reload();
                $(this).data('retrieve', true)
            })
        })

        function initTable(param, route, table = 'tablePengirimanSarung') {
            tableAjax = $(`#${table}`).DataTable({
                retrieve: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: {
                    url: "{{ getCurrentRoutes() }}",
                    type: 'GET',
                    data: function(e) {
                        e.tipe = param.tipe;
                    }
                },
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: [{
                    data: null,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return `<button type='button' id='btnCetak' data-model='PengirimanSarung' data-tipe='${row.tipe}' data-tipe_selected='${row.tipe_selected}' data-id='${row.id}' class='btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic' onclick='cetakForm($(this));'>
                        <i class='icon md-print'></i>
                    </button>`;
                    }
                }, {
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
                    name: 'nomor',
                    searchable: false
                }, {
                    data: null,
                    name: 'total_pcs',
                    searchable: false,
                    render: (data) => {
                        return formatNumber(data.rel_pengiriman_sarung_detail_sum_volume_1);
                    }
                }, {
                    data: 'tipe_selected',
                    name: 'tipe_selected',
                    searchable: false,
                    render: (data) => {
                        if (data == 'GRESIK') {
                            return `<span class="badge badge-outline badge-primary">${data}</span>`;
                        } else if (data == 'FINISHEDGOODS') {
                            return `<span class="badge badge-outline badge-warning">${data}</span>`;
                        } else {
                            return '';
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
        }

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailForm') }}/${id}`,
                data: {
                    id: id,
                    model: 'PengirimanSarung'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tablePengirimanSarungDetail').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: `${routeWithParam}`,
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, "All"]
                ],
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: 'warna',
                    name: 'warna',
                    searchable: false
                }, {
                    data: 'motif',
                    name: 'motif',
                    searchable: false
                }, {
                    data: 'mesin',
                    name: 'mesin',
                    searchable: false
                }, {
                    data: 'no_kikw',
                    name: 'no_kikw',
                    searchable: false
                }, {
                    data: 'no_kiks',
                    name: 'no_kiks',
                    searchable: false
                }, {
                    data: 'tanggal_potong',
                    name: 'tanggal_potong',
                    searchable: false
                }, {
                    data: 'grade',
                    name: 'grade',
                    searchable: false
                }, {
                    data: 'volume_1',
                    name: 'volume_1',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume, data.satuan_utama)} ${data.satuan_utama}`;
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
                    $('.panel').removeClass('is-loading');
                }
            })
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
            let idSongket = value.id_songket || '';
            let TanggalPotong = value.tanggal_potong || '';
            let idMesin = value.id_mesin || '';
            let namaMesin = value.nama_mesin || '';
            let noBeam = value.no_beam || '';
            let noKikw = value.no_kikw || '';
            let noKiks = value.no_kiks || '';
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
            if (idSongket != '') {
                $('#wrapperFieldSongket').html(`<div class="form-group">
                    <label>KIKS</label>
                    <input type="text" class="form-control" value="${noKiks}" readonly/>
                    <input type="hidden" name="input[id_songket]" value="${idSongket}" />
                </div>`);
            }
            if (TanggalPotong != '') {
                $('#wrapperFieldTanggalPotong').html(`<div class="form-group">
                    <label>Tgl. Potong</label>
                    <input type="text" class="form-control" value="${TanggalPotong}" readonly/>
                    <input type="hidden" name="input[tanggal_potong]" value="${TanggalPotong}" />
                </div>`);
            }

            if (idMesin != '') {
                $('#wrapperFieldMesin').html(`<div class="form-group">
                    <label>Mesin (Loom)</label>
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

            $('input[name="input[code]"]').val(currentCode);
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
        }
    </script>
@endsection
