<form
    action="{{ isset($id) ? route('production.pengiriman_sarung.update', $id) : route('production.pengiriman_sarung.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_pengiriman_sarung]" value="{{ $idParent }}">
    <input type="hidden" name="input[code]">
    <input type="hidden" name="id_log_stok" value="{{ $attr['idLogStok'] }}">
    <input type="hidden" name="tanggal" id="tanggal" value="{{ $data->tanggal }}">
    <input type="hidden" id="select_gudang" value="99999">
    <input type="hidden" name="input[id_barang]">
    <div class="form-group">
        <label>Gudang</label>
        <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
            data-placeholder="-- Pilih Gudang --" data-model="LogStokPenerimaan"
            data-route="{{ route('helper.getGudang') }}" class="form-control select2"></select>
    </div>
    <div class="form-group">
        <label>Barang</label>
        <select id="select_barang" onchange="changeBarang($(this));" data-filter-code="{{ $attr['codeSelect'] }}"
            @if ($data->tipe != 'LOKAL') data-filter-owner="{{ $data->tipe_selected ?? 'SOLO' }}" @endif
            data-placeholder="-- Pilih Barang --" data-route="{{ route('helper.getBarangWithStok') }}"
            class="form-control select2" required>
        </select>
    </div>
    <div id="wrapperFieldBeam"></div>
    <div id="wrapperFieldSongket"></div>
    <div id="wrapperFieldTanggalPotong"></div>
    <div id="wrapperFieldMesin"></div>
    <div id="wrapperFieldTipePraTenun"></div>
    <div id="wrapperFieldSizing"></div>
    <div id="wrapperFieldWarna"></div>
    <div id="wrapperFieldMotif"></div>
    <div id="wrapperFieldGrade"></div>
    <div id="wrapperFieldKualitas"></div>
    <div class="form-group">
        <label>Satuan 1 (Utama)</label>
        <input type="hidden" name="input[id_satuan_1]">
        <input type="text" class="form-control" id="txt_satuan_1" readonly>
    </div>
    <div class="form-group">
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]" required>
        <div id="wrapperSuggestionStok1"></div>
    </div>
    <div id="wrapperFieldSatuan2"></div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10"></textarea>
    </div>
</form>
