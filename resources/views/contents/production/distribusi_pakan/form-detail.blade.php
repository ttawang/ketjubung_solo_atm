<div class="panel-body container-fluid">
    <form
        action="{{ isset($id) ? route('production.distribusi_pakan.update', $id) : route('production.distribusi_pakan.store') }}"
        onsubmit="submitFormDetail(event, $(this));" method="POST" class="formInput">
        @if (isset($id))
            @method('PATCH')
        @endif
        <input type="hidden" name="isDetail" value="true">
        <input type="hidden" name="id_distribusi_pakan" value="{{ $idParent }}">
        <input type="hidden" name="tipe" value="{{ $attr['tipe'] }}">
        {{-- <input type="hidden" name="code" value="{{ $attr['code'] }}"> --}}
        <input type="hidden" name="id_tenun">
        <div class="form-group row row-lg">
            <div class="col-md-12 col-lg-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Tanggal: </label>
                    <div class="col-md-9">
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            name="tanggal" required />
                    </div>
                </div>
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">No. KIKW </label>
                    <div class="col-md-9">
                        <select id="select_beam" name="id_beam" data-placeholder="-- Pilih No KIKW --"
                            allow-clear="false" data-flag="distribusi_pakan" onchange="changeNoBeam($(this))"
                            data-id-distribusi-pakan="{{ $idParent }}" data-route="{{ route('helper.getBeam') }}"
                            class="form-control select2" required></select>
                    </div>
                </div>
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Barang</label>
                    <div class="col-md-9">
                        <select id="select_barang" data-placeholder="-- Pilih Barang --"
                            onchange="changeBarang($(this))" data-route="{{ route('helper.getBarangWithStok') }}"
                            data-filter-code="{{ $attr['tipe'] == 'warna' ? 'BHD,BHDG' : $attr['code'] }}"
                            class="form-control select2" required>
                        </select>
                    </div>
                </div>
                {{-- <div id="wrapperFieldWarna"></div> --}}
            </div>
            <div class="col-md-12 col-lg-6">
                <div id="wrapperFieldNoBeam"></div>
                <div id="wrapperFieldMesin"></div>
                <div id="wrapperFieldTipePraTenun"></div>
                <div id="wrapperFieldSizing"></div>
            </div>
        </div>
        <div class="form-group row row-lg" id="wrapperFormInput">

        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <button type='button' class='btn btn-default btn-sm waves-effect waves-classic btn-back'
                    onclick='goToDetail("{{ $idParent }}");'>
                    <i class='icon md-arrow-left mr-2'></i> Kembali [HOME]
                </button>
                <button type='submit' id="btnSubmit"
                    class='btn btn-primary btn-sm waves-effect waves-classic float-right btn-submit'>
                    <i class='icon md-check mr-2'></i> Submit [END]
                </button>
            </div>
        </div>
    </form>
</div>
