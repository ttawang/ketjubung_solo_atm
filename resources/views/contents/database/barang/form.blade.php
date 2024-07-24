<form action="{{ isset($id) ? route('database.barang.update', $id) : route('database.barang.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]">
    </div>
    {{-- <div class="form-group">
        <label>Kode</label>
        <input type="text" value="{{ $data->kode ?? '' }}" class="form-control" name="input[kode]">
    </div> --}}
    <div class="form-group">
        <label>Alias</label>
        <input type="text" value="{{ $data->alias ?? '' }}" class="form-control" name="input[alias]">
    </div>
    <div class="form-group">
        <label>Tipe</label>
        <select name="input[id_tipe]" id="select_tipe" class="form-control select2" data-placeholder="-- Pilih Tipe --"
            data-route="{{ route('helper.getTipe') }}"></select>
    </div>
</form>
