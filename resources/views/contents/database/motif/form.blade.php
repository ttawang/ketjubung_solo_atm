<form action="{{ isset($id) ? route('database.motif.update', $id) : route('database.motif.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="name" required>
    </div>
    <div class="form-group">
        <label>Alias</label>
        <input type="text" value="{{ $data->alias ?? '' }}" class="form-control" name="alias">
    </div>
</form>
