<form action="{{ isset($id) ? route('database.resep.update', $id) : route('database.resep.store') }}"
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
            data-route="{{ route('helper.getBarang') }}" data-filter-tipe="1" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Warna</label>
        <select name="input[id_warna]" id="select_warna" data-placeholder="-- Pilih Warna --"
            data-route="{{ route('helper.getWarna') }}" class="form-control select2" required>
        </select>
    </div>
</form>
