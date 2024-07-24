<div class="panel-body container-fluid">
    <form action="{{ isset($id) ? route('inventory.saldoawal.update', $id) : route('inventory.saldoawal.store') }}"
        onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
        @if (isset($id))
            @method('PATCH')
            <input type="hidden" name="id_log_stok" value="{{ $data->id_log_stok ?? '' }}">
            <input type="hidden" name="id_beam" value="{{ $data->id_beam ?? '' }}">
            <input type="hidden" name="id_nomor_kikw" value="{{ $data->relBeam()->value('id_nomor_kikw') }}">
            <input type="hidden" name="id_history_mesin" value="{{ $data->relMesinHistoryLatest()->value('id') }}">
        @endif
        <div class="form-group row row-lg">
            <div class="col-md-12 col-lg-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Tanggal: </label>
                    <div class="col-md-9">
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            name="input[tanggal]" required />
                    </div>
                </div>
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Proses: </label>
                    <div class="col-md-9">
                        {{-- <select id="select_proses" onchange="changeProses($(this))" name="input[code]"
                            data-route="{{ route('helper.getCode') }}" data-dropdown-parent="false"
                            data-placeholder="-- Pilih Proses --" class="form-control select2">
                        </select> --}}
                        <select id="select_proses" onchange="changeProses($(this))" name="input[code]"
                            data-dropdown-parent="false" data-placeholder="-- Pilih Proses --"
                            class="form-control select2" {{ isset($id) ? 'disabled' : 'required' }}>
                            <option value=""></option>
                            @foreach (SaldoAwalCodeText() as $code => $item)
                                <option value="{{ $code }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row row-lg">
            <div class="col-md-12 col-lg-6">
                <div class="example-wrap">
                    <h4 class="example-title"><i class="icon md-assignment-o"></i> Barang / Jenis Benang</h4>
                    <div class="example">
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Gudang: </label>
                            <div class="col-md-9">
                                <select id="select_gudang" name="input[id_gudang]" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Gudang --" data-route="{{ route('helper.getGudang') }}"
                                    class="form-control select2 input_barang"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Barang: </label>
                            <div class="col-md-9">
                                <select id="select_barang" data-dropdown-parent="false" name="input[id_barang]"
                                    data-filter-tipe='1' data-placeholder="-- Pilih Barang --"
                                    data-route="{{ route('helper.getBarang') }}"
                                    class="form-control select2 input_barang"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Warna: </label>
                            <div class="col-md-9">
                                <select id="select_warna" name="input[id_warna]" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Warna --" data-route="{{ route('helper.getWarna') }}"
                                    class="form-control select2"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Motif: </label>
                            <div class="col-md-9">
                                <select id="select_motif" name="input[id_motif]" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Motif --" data-route="{{ route('helper.getMotif') }}"
                                    class="form-control select2"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Kualitas: </label>
                            <div class="col-md-9">
                                <select id="select_grade" onchange="changeGrade($(this))" name="input[id_grade]"
                                    data-dropdown-parent="false" data-placeholder="-- Pilih Kualitas --"
                                    data-route="{{ route('helper.getGrade') }}"
                                    class="form-control select2 input_barang"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Jenis Cacat: </label>
                            <div class="col-md-9">
                                <select id="select_kualitas" onchange="changeGrade($(this))" name="input[id_kualitas]"
                                    data-id-grade="9999" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Jenis Cacat --"
                                    data-route="{{ route('helper.getKualitas') }}"
                                    class="form-control select2 input_barang"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-material row">
                                    <label class="col-md-6 col-form-label">Volume 1: </label>
                                    <div class="col-md-6">
                                        <input type="text" name="input[volume_1]" id="volume_1"
                                            class="form-control number-only" value="{{ $data->volume_1 ?? '' }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-material row">
                                    <div class="col-md-7">
                                        <select id="select_satuan_1" name="input[id_satuan_1]"
                                            data-dropdown-parent="false" data-placeholder="-- Pilih Satuan 1 --"
                                            data-route="{{ route('helper.getSatuan') }}" class="form-control select2"
                                            required></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-material row">
                                    <label class="col-md-6 col-form-label">Volume 2: </label>
                                    <div class="col-md-6">
                                        <input type="text" name="input[volume_2]" id="volume_2"
                                            class="form-control number-only" value="{{ $data->volume_2 ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-material row">
                                    <div class="col-md-7">
                                        <select id="select_satuan_2" name="input[id_satuan_2]"
                                            data-dropdown-parent="false" data-placeholder="-- Pilih Satuan 2 --"
                                            data-route="{{ route('helper.getSatuan') }}"
                                            class="form-control select2"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="example-wrap">
                    <h4 class="example-title"><i class="icon md-assignment-o"></i> Beam</h4>
                    <div class="example">
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Tipe Beam: </label>
                            <div class="col-md-9">
                                <input type="text" name="beam[tipe_beam]" id="tipe_beam" value="{{ (isset($id)) ? $data->relBeam()->value('tipe_beam') : '' }}"
                                    class="form-control input_beam">
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Nomor Beam: </label>
                            <div class="col-md-9">
                                <select id="select_nomor_beam" name="beam[id_nomor_beam]" data-finish="1"
                                    data-dropdown-parent="false" data-placeholder="-- Pilih Nomor Beam --"
                                    data-route="{{ route('helper.getNomorBeam') }}"
                                    class="form-control select2 input_beam"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Nomor Kikw: </label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input_beam"
                                    data-no-kikw="{{ isset($id) ? $data->throughNomorKikw()->value('name') : '' }}"
                                    name="txt_nomor_kikw" id="no_kikw_baru" onkeyup="onCheckNoKikw($(this));"
                                    value="{{ isset($id) ? $data->throughNomorKikw()->value('name') : '' }}"
                                    autocomplete="false">
                                <span id="txtAlertNoKikw"></span>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Mesin: </label>
                            <div class="col-md-9">
                                <select id="select_mesin" name="input[id_mesin]" data-dropdown-parent="false"
                                    data-placeholder="-- Pilih Mesin --" data-route="{{ route('helper.getMesin') }}"
                                    class="form-control select2 input_beam"></select>
                            </div>
                        </div>
                        <div class="form-group form-material row">
                            <label class="col-md-3 col-form-label">Tipe Pra Tenun: </label>
                            <div class="col-md-9">
                                <select id="select_tipe_pra_tenun" name="beam[tipe_pra_tenun]"
                                    data-dropdown-parent="false" data-placeholder="-- Pilih Tipe Pra Tenun --"
                                    class="form-control select2 input_beam">
                                    <option value=""></option>
                                    <option value="CUCUK">CUCUK</option>
                                    <option value="TYEING">TYEING</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <button type='button' class='btn btn-default btn-sm waves-effect waves-classic'
                    onclick='closeForm($(this));'>
                    <i class='icon md-arrow-left mr-2'></i> Kembali
                </button>
                <button type='submit' id="btnSubmit"
                    class='btn btn-primary btn-sm waves-effect waves-classic float-right'>
                    <i class='icon md-check mr-2'></i> Submit
                </button>
            </div>
        </div>
    </form>
</div>
