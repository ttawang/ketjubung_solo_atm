@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importPenerimaanBarang') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Penerimaan Barang (PB)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importDoubling') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Doubling (PB Doubling)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importBenangGreyDyeing') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Benang Grey Dyeing (BBD)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importDyeingOvercone') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Hasil Dyeing Overcone (DO)</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importHasilDyeing') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Benang Hasil Dyeing (BHD)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importBarangWarping') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Barang Warping (BBW)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importBarangLusi') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Beam Lusi (BL)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importBarangSongket') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Beam Songket (BS)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importPakanShuttle') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Pakan Shuttle (BPS)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importPakanRappier') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Pakan Rappier (BPR)</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importTenunLusi') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Tenun Lusi (BBTL)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importTenunSongket') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Tenun Songket (BBTS)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelPakan') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Pakan (DPR & DPS)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelInspekting') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Inspekting (BGIG)</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelDudulan') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Dudulan (BGD)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelInspectDudulan') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Inspect Dudulan (BGID)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelJahitSambung') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Jahit Sambung (JS)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelP1') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit P1</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelFinishingCabut') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Finishing Cabut</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelDrying') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Drying (DR)</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelP2') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit P2</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelJahit') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Jahit</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelJahitP2') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Jahit (JP2)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelBenangWarnaPakan') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Benang Warna Pakan (BBWP)</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelInspectP2') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Inspect P2 (IP2)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelInpectP1') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Inspect P1 (IP1)</button>
                            </div>
                        </form>
                    </div>
                    <div class="form-group row">
                        <form action="{{ route('helper.importExcelChemicalFinishing') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <input type="file" name="file_excel" required>
                                <button type="submit" class="btn btn-primary btn-sm waves-effect waves-classic">
                                    Submit Chemical Finishing (CF)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambahForm', 'refresh']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableSaldoawal">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Gudang</th>
                                <th>Nama Barang</th>
                                <th>No. Beam</th>
                                <th>No. Kikw</th>
                                <th>Mesin</th>
                                <th>Warna</th>
                                <th>Motif</th>
                                <th>Volume 1</th>
                                <th>Volume 2</th>
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
        let timer, isViewOnly;
        $(document).ready(function() {
            tableAjax = $('#tableSaldoawal').DataTable({
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
                        e.code = $('#filterProses').val() || '';
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
                    data: 'gudang',
                    name: 'gudang',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
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
                    data: 'motif',
                    name: 'motif',
                    searchable: false
                }, {
                    data: 'volume_1',
                    name: 'volume_1',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume, data.satuan_utama)} ${data.satuan_utama || ''}`;
                    }
                }, {
                    data: 'volume_2',
                    name: 'volume_2',
                    searchable: false,
                    render: (volume, display, data) => {
                        return `${formatNumber(volume, data.satuan_pilihan)} ${data.satuan_pilihan || ''}`;
                    }
                }, {
                    data: 'production_code',
                    name: 'production_code',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }],
                initComplete: () => {
                    $('#tableSaldoawal_filter').html(`
                    <div class='row'>
                        <div class='col-sm-12 col-md-9 text-left'>
                            <label>
                                <select id="filterProses" class="form-control form-control-sm" data-dropdown-parent="false" data-placeholder="-- Pilih Proses --" aria-controls="tableSaldoawal">
                                    <option value="">-- Pilih Proses --</option>
                                    @foreach (SaldoAwalCodeText() as $code => $item)
                                        <option value="{{ $code }}">{{ $item }}</option>
                                    @endforeach
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
                                <input type="search" onkeyup="onSearching($(this));" class="form-control form-control-sm" placeholder="" aria-controls="tableSaldoawal">
                            </label>
                        </div>
                    </div>`);
                }
            })
        })

        function onSearching(element) {
            tableAjax.search(element.val()).draw();
        }

        function showFormView(id = '', viewOnly = false) {
            detailForm({
                url: `{{ url('helper/detailFormView') }}/${id}`,
                data: {
                    id: id,
                    model: 'SaldoAwal',
                    view: true,
                    root: 'inventory'
                },
                callback: () => {
                    $('.input_barang').prop('disabled', true);
                    $('.input_beam').prop('disabled', true);
                    $('input.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });
                    isViewOnly = viewOnly;
                    if (viewOnly) {
                        $('#btnSubmit').replaceWith('');
                        $('input').prop('disabled', true);
                        $('select').prop('disabled', true);
                    }
                }
            })
        }

        function goToDetail(id) {
            showFormView(id);
        }

        function changeProses(element) {
            clearTimeout(timer);
            $('.panel').addClass('is-loading');
            timer = setTimeout(function() {
                console.log(isViewOnly);
                if (!isViewOnly) {
                    let isEdit = typeof($('[name="_method"]').val()) !== undefined;
                    let value = element.select2('data')[0];
                    let selectGudang = $('#select_gudang');
                    let selectBarang = $('#select_barang');
                    let selectWarna = $('#select_warna');
                    let selectMotif = $('#select_motif');
                    let selectGrade = $('#select_grade');
                    let selectKualitas = $('#select_kualitas');
                    let selectTipeBeam = $('#select_tipe_beam');
                    let selectMesin = $('#select_mesin');
                    let code = element.val();
                    if (code == 'PB') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                    } else if (code == 'BBD') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                    } else if (code == 'DO') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                        selectWarna.prop('disabled', false);
                    } else if (code == 'BHD') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', true);

                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 1,
                                text: 'cones'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="1">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="2">`);

                    } else if (code == 'BBW') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                        selectWarna.prop('disabled', false);
                    } else if (code == 'BBWS') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                        selectWarna.prop('disabled', false);
                    } else if (code == 'BL' || code == 'BBTL') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 3).prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', false);

                        $(`#volume_1`).val(1).prop('readonly', true);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 3,
                                text: 'beam'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 4,
                                text: 'pcs'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="3">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="4">`);

                        $('#tipe_beam').val('LUSI').prop('readonly', true);
                        selectMesin.data('jenis', 'LOOM');
                        $('.input_beam').prop('disabled', false);
                    } else if (code == 'BS' || code == 'BBTS') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 4).prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', false);

                        $(`#volume_1`).val(1).prop('readonly', true);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 3,
                                text: 'beam'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 4,
                                text: 'pcs'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="3">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="4">`);

                        $('#tipe_beam').val('SONGKET').prop('readonly', true);
                        selectMesin.data('jenis', 'LOOM');
                        $('.input_beam').prop('disabled', false);
                    } else if (code == 'BBWP') {
                        selectGudang.prop('disabled', false);
                        selectBarang.prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', true);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 1,
                                text: 'cones'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="1">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="2">`);
                    } else if (code == 'BPS') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 5).prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', true);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 4,
                                text: 'pcs'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="4">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="2">`);
                    } else if (code == 'BPR') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 5).prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        selectMotif.prop('disabled', true);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 1,
                                text: 'cones'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="1">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="2">`);
                    } else if (code == 'BO') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 6).prop('disabled', false);
                        selectWarna.prop('disabled', false);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 1,
                                text: 'cones'
                            }
                        });
                        $(`#select_satuan_2`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="1">`);
                        $(`#select_satuan_2`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_2]" value="2">`);
                    } else if (code == 'DW') {
                        $(`#select_gudang`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'Gudang Dyeing'
                            }
                        });
                        selectGudang.prop('disabled', true).after(
                            `<input type="hidden" name="input[id_gudang]" value="2">`);
                        selectBarang.data('filter-tipe', 2).prop('disabled', false);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="2">`);
                        $(`#volume_2`).prop('disabled', true);
                        $(`#select_satuan_2`).prop('disabled', true);
                    } else if (code == 'CJ') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 2).prop('disabled', false);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="2">`);
                        $(`#volume_2`).prop('disabled', true);
                        $(`#select_satuan_2`).prop('disabled', true);
                    } else if (code == 'CD') {
                        selectGudang.prop('disabled', false);
                        selectBarang.data('filter-tipe', 2).prop('disabled', false);
                        $(`#select_satuan_1`).select2("trigger", "select", {
                            data: {
                                id: 2,
                                text: 'kg'
                            }
                        });
                        $(`#select_satuan_1`).prop('disabled', true).after(
                            `<input type="hidden" name="input[id_satuan_1]" value="2">`);
                        $(`#volume_2`).prop('disabled', true);
                        $(`#select_satuan_2`).prop('disabled', true);
                    }
                }
                $('.panel').removeClass('is-loading');
            }, 500)
        }

        function onCheckNoKikw(element) {
            let param = element.val();
            clearTimeout(timer);
            timer = setTimeout(function validate() {
                $.ajax({
                    url: "{{ route('helper.getNomorKikw') }}",
                    type: "GET",
                    data: {
                        param: param
                    },
                    success: (data) => {
                        let oldKikw = element.data('no-kikw');
                        if (param == oldKikw) {
                            $('#txtAlertNoKikw').html(
                                `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                            );
                            $('#btnSubmit').prop('disabled', false);
                        } else {
                            if (data.length > 0) {
                                $('#txtAlertNoKikw').html(
                                    `<small class="text-danger">*) Nomor Kikw sudah digunakan!</small>`
                                );
                                $('#btnSubmit').prop('disabled', true);
                            } else {
                                $('#txtAlertNoKikw').html(
                                    `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                                );
                                $('#btnSubmit').prop('disabled', false);
                            }
                        }

                        if (param == '') $('#txtAlertNoKikw').html('');
                    }
                })
            }, 800);
        }

        function changeGrade(element) {
            let value = element.val() || 9999;
            $('#select_kualitas').data('id-grade', value);
        }

        function submitForm(event, this_) {
            event.preventDefault();
            $.ajax({
                url: this_.attr('action'),
                beforeSend: () => {
                    $('#btnSubmit').prop('disabled', true);
                },
                type: this_.attr('method'),
                data: this_.serialize(),
                success: (message) => {
                    toastr.success(message);
                    closeForm();
                },
                complete: () => {
                    $('#btnSubmit').prop('disabled', false);
                    $('.panel').removeClass('is-loading');
                }
            })
            /*showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Konfirmasi Tambah Saldoawal?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'saveorupdate',
                textButton: 'Selesai',
                callback: () => {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        success: (message) => {
                            toastr.success(message);
                            // closeForm();
                        },
                        // complete: () => {}
                    })
                }
            });*/
        }

        function closeForm() {
            $('.page-header-actions').html('');
            formWrapper.html('');
            mainWrapper.show();
            tableAjax.ajax.reload();
            tableAjaxDetail?.destroy();
            $('input').prop('disabled', false);
            $('select').prop('disabled', false);
        }
    </script>
@endsection
