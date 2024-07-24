<div class="form-group">
    <label>Jenis Benang</label>
    <select id="select_barang" onchange="changeBarang($(this));" data-filter="1" data-filter-code="PB"
        data-route="{{ route('helper.getBarangWithStok') }}" data-placeholder="-- Pilih Jenis Benang --"
        class="form-control select2" required>
    </select>
</div>
<div class="form-group">
    <label>Satuan 1</label>
    <input type="hidden" name="input[id_satuan_1]" value="2">
    <input type="text" id="txt_satuan_1" value="Kg" class="form-control" disabled>
</div>
<div class="form-group">
    <label>Volume 1</label>
    <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only" name="input[volume_1]"
        required>
    <div id="wrapperSuggestionStok1"></div>
</div>
