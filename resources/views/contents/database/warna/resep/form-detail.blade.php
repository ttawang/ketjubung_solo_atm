<form action="{{ isset($id) ? route('database.resep.update', $id) : route('database.resep.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 400px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_resep]" value="{{ $idParent }}">
    <div class="form-group">
        <label>Chemical</label>
        <select id="select_barang" name="input[id_barang]" data-route="{{ route('helper.getBarang') }}"
            data-filter-tipe="2" data-id-resep="{{ $idParent }}" data-placeholder="-- Pilih Chemical --" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Satuan</label>
        <select class="form-control" name="input[id_satuan]" required>
            <option value="2" {{ $data->id_satuan == '2' ? 'selected' : '' }}>Kg</option>
            <option value="5" {{ $data->id_satuan == '5' ? 'selected' : '' }}>Gram</option>
        </select>
    </div>
    <div class="form-group">
        <label>Volume</label>
        <input type="text" class="form-control number-only" name="input[volume]" value="{{ $data->volume ?? '' }}"
            required>
    </div>
</form>
