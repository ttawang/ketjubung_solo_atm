<form
    action="{{ isset($id) ? route('database.resep_chemical_finishing.update', $id) : route('database.resep_chemical_finishing.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Jenis Benang</label>
        <select name="input[id_barang]" id="select_barang" data-placeholder="-- Pilih Barang --"
            data-route="{{ route('helper.getBarang') }}" data-filter-tipe="7" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Motif</label>
        <select name="input[id_motif][]" id="select_motif" multiple data-placeholder="-- Pilih Motif --"
            data-route="{{ route('helper.getMotif') }}" class="form-control select2" required>
        </select>
    </div>
</form>
