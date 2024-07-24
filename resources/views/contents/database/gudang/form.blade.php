<form action="{{ isset($id) ? route('database.gudang.update', $id) : route('database.gudang.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Kode</label>
        <input type="text" value="{{ $data->kode ?? '' }}" class="form-control" name="input[kode]" required>
    </div>
</form>
