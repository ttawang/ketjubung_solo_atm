<form
    action="{{ isset($id) ? route('production.penerimaan_sarung.update', $id) : route('production.penerimaan_sarung.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_penerimaan_sarung]" value="{{ $idParent }}">
    <input type="hidden" name="input[code]" value="{{ $attr['code'] }}">
    <input type="hidden" name="id_log_stok" value="{{ $attr['idLogStok'] }}">
    <input type="hidden" name="tanggal" id="tanggal" value="{{ $data->tanggal }}">
    <input type="hidden" name="owner" value="{{ $attr['from'] }}">
    <div class="form-group">
        <label>Gudang</label>
        <input type="hidden" name="input[id_gudang]" value="6">
        <input type="text" class="form-control" value="Gudang Finishing" disabled>
    </div>
    <div class="form-group">
        <label>Sarung</label>
        <select id="select_barang" name="input[id_barang]" data-id-selected="117,118" tags="true"
            data-placeholder="-- Pilih Sarung --" data-route="{{ route('helper.getBarang') }}" data-filter-jenis="7"
            class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Motif</label>
        <select name="input[id_motif]" id="select_motif" data-placeholder="-- Pilih Motif --" tags="true"
            data-route="{{ route('helper.getMotif') }}" class="form-control select2">
        </select>
    </div>
    <div class="form-group">
        <label>Satuan 1 (Utama)</label>
        <input type="hidden" name="input[id_satuan_1]" value="4">
        <input type="text" class="form-control" id="txt_satuan_1" value="Pcs" readonly>
    </div>
    <div class="form-group">
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]" required>
        <div id="wrapperSuggestionStok1"></div>
    </div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10"></textarea>
    </div>
</form>
