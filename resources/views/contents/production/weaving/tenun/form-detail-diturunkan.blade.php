<input type="hidden" id="select_gudang" value="99999">
<input type="hidden" name="input[id_barang]">
<input type="hidden" name="id_log_stok_penerimaan" value="{{ $data->id_log_stok_penerimaan ?? '' }}">
<div class="form-group">
    <label>Tanggal</label>
    <input type="date" class="form-control" name="input[tanggal]" value="{{ $attr['tanggal'] }}" required>
</div>
<input type="hidden" name="input[id_gudang]" value="4">
<input type="hidden" name="input[code]">
<div class="form-group">
    <label>Tipe Barang</label>
    <select id="select_tipe_barang" data-id-beam="{{ $data->id_beam }}" data-placeholder="-- Pilih Tipe Barang --"
        onchange="changeTipeBarang($(this));" class="form-control select2" required>
        <option value=""></option>
        @if ($attr['form'] == 'diturunkan')
            <option value="BBTLT">Lusi</option>
        @endif
        <option value="BBTST">Songket</option>
        <option value="DPST">Pakan Shuttle</option>
        <option value="DPRT">Pakan Rappier atau Benang Warna</option>
        <option value="BOT">Leno</option>
    </select>
</div>
<input type="hidden" name="input[id_warna]">
<input type="hidden" name="input[id_motif]">
<div class="form-group">
    <label>Barang</label>
    <select id="select_barang" data-flag="turun" onchange="changeBarangTurun($(this));" data-filter-code="99999"
        data-id-tenun="{{ $idParent }}" data-placeholder="-- Pilih Barang --"
        data-route="{{ route('helper.getBarangTenun') }}" class="form-control select2" required>
    </select>
</div>
<div id="wrapperFieldWarna"></div>
<div id="wrapperFieldMotif"></div>
<div class="form-group">
    <label>Satuan 1 (Utama)</label>
    <input type="hidden" name="input[id_satuan_1]">
    <input type="text" class="form-control" id="txt_satuan_1" readonly>
</div>
<div class="form-group">
    <label>Volume 1</label>
    <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control fieldVolume number-only"
        name="input[volume_1]" required>
    <div id="wrapperSuggestionStok1"></div>
</div>
<div id="wrapperFieldSatuan2"></div>
