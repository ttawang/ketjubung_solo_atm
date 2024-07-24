<form
    action="{{ isset($id) ? route('production.chemical.update', $id) : route('production.chemical.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_chemical]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok" value="{{ $data->id_log_stok ?? '' }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="curr_volume" value="{{ $data->volume ?? 0 }}">
    <input type="hidden" name="input[code]" value="{{ $attr['code'] }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="input[tanggal]" class="form-control"
            value="{{ isset($data->tanggal) ? $data->tanggal : date('Y-m-d') }}">
    </div>
    <div class="form-group">
        <label>Gudang</label>
        <input type="hidden" id="select_gudang" name="input[id_gudang]" value="{{ $attr['idGudang'] }}">
        <input type="text" class="form-control" value="{{ $attr['namaGudang'] }}" readonly>
    </div>
    <div class="form-group">
        <label>Chemical</label>
        <select id="select_barang" onchange="changeBarang($(this));" data-filter-code="{{ $attr['code'] }}"
            data-route="{{ route('helper.getBarangWithStok') }}" data-placeholder="-- Pilih Chemical --"
            class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Satuan</label>
        <input type="hidden" name="input[id_satuan]" value="2">
        <input type="text" id="txt_satuan_1" value="Kg" class="form-control" disabled>
    </div>
    <div class="form-group">
        <label>Volume</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only" name="input[volume]"
            required>
        <div id="wrapperSuggestionStok1"></div>
    </div>
</form>
