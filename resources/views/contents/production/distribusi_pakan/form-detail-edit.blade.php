<form
    action="{{ isset($id) ? route('production.distribusi_pakan.update', $id) : route('production.distribusi_pakan.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_distribusi_pakan]" value="{{ $idParent }}">
    <input type="hidden" name="id_tenun_detail" value="{{ $data->id_tenun_detail }}">
    <input type="hidden" name="id_log_stok_keluar" value="{{ $attr['idLogStokKeluar'] }}">
    <input type="hidden" name="id_log_stok_masuk" value="{{ $attr['idLogStokMasuk'] }}">
    <input type="hidden" name="id_log_stok_tenun" value="{{ $attr['idLogStokTenun'] }}">
    <input type="hidden" name="tipe" value="{{ $attr['tipe'] }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="input[id_gudang]">
    <input type="hidden" name="input[code]">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    <div class="form-group">
        <label>No. KIKW</label>
        <select id="select_beam" name="input[id_beam]" data-placeholder="-- Pilih No KIKW --"
            data-flag="distribusi_pakan" onchange="changeNoBeamEdit($(this))" data-route="{{ route('helper.getBeam') }}"
            class="form-control select2" required></select>
    </div>
    <div id="wrapperFieldNoBeam"></div>
    <div id="wrapperFieldMesin"></div>
    <div id="wrapperFieldTipePraTenun"></div>
    <div id="wrapperFieldSizing"></div>
    <div class="form-group">
        <label>Barang</label>
        <select id="select_barang" data-placeholder="-- Pilih Barang --" onchange="changeBarangEdit($(this))"
            data-route="{{ route('helper.getBarangWithStok') }}" data-filter-code="{{ $attr['tipe'] == 'warna' ? 'BHD,BHDG' : $attr['code'] }}"
            class="form-control select2" required>
        </select>
    </div>
    <div id="wrapperFieldWarna"></div>
    <div class="form-group">
        <label>Satuan</label>
        <input type="hidden" name="input[id_satuan_1]">
        <input type="text" id="txt_satuan_1" class="form-control" readonly>
    </div>
    <div class="form-group">
        <label>Volume</label>
        <input type="text" class="form-control number-only" name="input[volume_1]" required>
        <div id="wrapperSuggestionStok1"></div>
    </div>
    <div id="wrapperFieldSatuan2"></div>
</form>
