@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row row-lg">
                <div class="col-md-12 col-lg-6">
                    <form id="formExport" action="{{ route('helper.exportStokopname') }}" enctype="multipart/form-data"
                        target="_blank">
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Tanggal: </label>
                            <div class="col-md-9">
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Gudang: </label>
                            <div class="col-md-9">
                                <select id="select_gudang" name="id_gudang" data-dropdown-parent="false"
                                    data-placeholder="-- Semua Gudang --" data-route="{{ route('helper.getGudang') }}"
                                    class="form-control select2"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Proses: </label>
                            <div class="col-md-9">
                                <select id="select_proses" name="code" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Proses --" class="form-control select2" required>
                                    <option value=""></option>
                                    @foreach (StokopnameCodeText() as $code => $item)
                                        <option value="{{ $code }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                    <form id="formImport" action="{{ route('helper.importStokopname') }}"
                        onsubmit="importExcel(event, $(this));" method="POST" target="_blank"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">File Upload: </label>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                                        <input type="text" id="spanFile" class="form-control" readonly="">
                                        <span class="input-group-append">
                                            <span class="btn btn-primary btn-file waves-effect waves-classic">
                                                <i class="icon md-upload" aria-hidden="true"></i>
                                                <input type="file" id="fileExcel"
                                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                                    name="file_excel" required>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group form-material row">
                        <div class="col-md-12">
                            <button form="formExport" type="submit"
                                class="btn btn-warning btn-sm waves-effect waves-classic">
                                <i class="icon md-download mr-1"></i> Download Template Stokopname</button>
                            <button form="formImport" type="submit"
                                class="btn btn-primary btn-sm waves-effect waves-classic">
                                <i class="icon md-upload mr-1"></i> Import Stokopname</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['refresh']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableStokopname">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Proses</th>
                                <th width="120px">Aksi</th>
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
        let timer;
        $(document).ready(function() {
            initSelect2();
            tableAjax = $('#tableStokopname').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
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
                    data: 'proses',
                    name: 'proses',
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
                    model: 'Stokopname',
                    customView: 'contents.inventory.stokopname.detail'
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $('#tableStokopnameDetail').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: `${routeWithParam}`,
                    type: 'GET',
                    data: (e) => {
                        e.code = data.code;
                    }
                },
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: selectedColumn(data.code),
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function selectedColumn(code = 'PB') {
            let column = {};
            if (checkCodeStokopnames(code, 'class1')) {
                column = [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: null,
                    name: 'stok_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }];
            } else if (checkCodeStokopnames(code, 'class2')) {
                column = [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: 'warna',
                    name: 'warna',
                    searchable: false
                }, {
                    data: null,
                    name: 'stok_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stok_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }];
            } else if (checkCodeStokopnames(code, 'class3')) {
                column = [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false
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
                    data: 'tipe_pra_tenun',
                    name: 'tipe_pra_tenun',
                    searchable: false
                }, {
                    data: 'is_sizing',
                    name: 'is_sizing',
                    searchable: false
                }, {
                    data: null,
                    name: 'stok_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stok_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_2',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_2} ${data.satuan_pilihan}`;
                    }
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }];
            } else if (checkCodeStokopnames(code, 'class4')) {
                column = [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false
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
                    data: 'grade',
                    name: 'grade',
                    searchable: false
                }, {
                    data: 'kualitas',
                    name: 'kualitas',
                    searchable: false
                }, {
                    data: null,
                    name: 'stok_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stok_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'stokopname_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.stokopname_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'selisih_1',
                    searchable: false,
                    render: (data) => {
                        return `${data.selisih_1} ${data.satuan_utama}`;
                    }
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }];
            }

            return column;
        }

        function importExcel(event, this_) {
            event.preventDefault();
            let formData = new FormData(this_[0]);
            $.ajax({
                url: this_.attr('action'),
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: (message) => {
                    toastr.success(message);
                    tableAjax.ajax.reload();
                },
                complete: () => {
                    $('#spanFile').val('');
                    $('#fileExcel').val('');
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
            let valueVolume2 = (value.volume_2 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let currentCode = value.code || 'PB';
            let isSizing = value.is_sizing || '';
            let idBeam = value.id_beam || '';
            let noBeam = value.no_beam || '-';
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
            $('input[name="input[stokopname_1]"]').val(valueVolume1 || valueStokUtama);

            $('#wrapperFieldStokopname2').html('');
            if (valueStokPilihan != '' || valueVolume2 != '') {
                $('#wrapperFieldStokopname2').html(`<div class="form-group">
                    <label>Satuan 2 (Pilihan)</label>
                    <input type="hidden" name="input[id_satuan_2]" value="${idSatuan2}">
                    <input type="text" class="form-control" id="txt_satuan_2" value="${namaSatuan2}" readonly>
                </div>
                <div class="form-group">
                    <label>Stokopname 2</label>
                    <input type="text" class="form-control number-only" name="input[stokopname_2]" value="${valueVolume2 || valueStokPilihan}" required>
                </div>`);
            }
        }
    </script>
@endsection
