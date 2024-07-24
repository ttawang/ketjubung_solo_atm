<form
    action="{{ isset($id) ? route('database.resep_chemical_finishing.update', $id) : route('database.resep_chemical_finishing.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 400px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_resep]" value="{{ $idParent }}">
    <div class="form-group">
        <label>Chemical</label>
        <select id="select_barang" name="input[id_barang]" data-route="{{ route('helper.getBarang') }}"
            data-filter-tipe="2" data-id-resep="{{ $idParent }}" data-placeholder="-- Pilih Chemical --"
            class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Satuan</label>
        <input type="hidden" name="input[id_satuan]" value="7">
        <input type="text" class="form-control" value="cc" disabled>
    </div>
    <div class="form-group">
        <label>Volume</label>
        <input type="text" class="form-control number-only" name="input[volume]" value="{{ $data->volume ?? '' }}"
            required>
    </div>
</form>
