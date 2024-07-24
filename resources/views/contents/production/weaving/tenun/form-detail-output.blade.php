@if (!isset($id))
    <input type="hidden" name="input[id_lusi_detail]" value="{{ $detailBeam->id }}">
@endif
<input type="hidden" id="select_gudang" value="99999">
<input type="hidden" name="input[id_barang]">
<input type="hidden" name="id_log_stok_penerimaan" value="{{ $data->id_log_stok_penerimaan ?? '' }}">
<div class="form-group">
    <label>Tanggal</label>
    <input type="date" class="form-control" name="input[tanggal]" value="{{ $attr['tanggal'] }}" required>
</div>
<input type="hidden" name="input[id_gudang]" value="4">
<div class="form-group">
    <label>Group</label>
    <select name="input[id_group]" id="select_group" onchange="changeGroup($(this))"
        data-placeholder="-- Pilih Group --" data-route="{{ route('helper.getGroup') }}"
        class="form-control select2"></select>
</div>
<div class="form-group">
    <label>Pekerja</label>
    <select name="input[id_pekerja]" id="select_pekerja" data-id-group="99999" data-flag="tenun"
        data-placeholder="-- Pilih Pekerja --" data-route="{{ route('helper.getPekerja') }}"
        class="form-control select2"></select>
</div>
<input type="hidden" name="input[code]" value="BG">
<div class="form-group">
    <label>Barang</label>
    <select name="input[id_barang]" id="select_sarung" data-filter-tipe="7" data-placeholder="-- Pilih Barang --"
        data-route="{{ route('helper.getBarang') }}" class="form-control select2" required>
    </select>
</div>
<div class="form-group">
    <label>Songket</label>
    <select name="input[id_songket_detail]" data-flag="output" data-filter-code="BBTS" id="select_songket"
        onchange="changeSongket($(this));" data-placeholder="-- Pilih Songket --"
        data-route="{{ route('helper.getBarangTenun') }}" data-id-tenun="{{ $idParent }}"
        class="form-control select2" required>
    </select>
</div>
<div class="form-group">
    <label>Warna</label>
    <input type="hidden" name="input[id_warna]" value="{{ $detailBeam->id_warna }}">
    <input type="text" class="form-control" value="{{ $detailBeam->relWarna()->value('name') }}" readonly>
</div>
<div class="form-group">
    <label>Motif</label>
    <input type="hidden" name="input[id_motif]" value="{{ $detailBeam->id_motif }}">
    <input type="text" class="form-control" value="{{ $detailBeam->relMotif()->value('name') }}" readonly>
</div>

<div class="form-group">
    <label>Satuan 1 (Utama)</label>
    <input type="hidden" name="input[id_satuan_1]" value="4">
    <input type="text" class="form-control" value="Pcs" readonly>
</div>
<div class="form-group">
    <label>Volume 1</label>
    <input type="text" value="{{ $data->volume_1 ?? '' }}" onkeyup="maxInputNumber($(this));"
        class="form-control number-only" name="input[volume_1]" required>
    <div id="wrapperSuggestionStok1"></div>
</div>
