<form action="{{ isset($id) ? route('database.satuan.update', $id) : route('database.satuan.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
</form>