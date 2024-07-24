<form action="{{ isset($id) ? route('database.pekerja.update', $id) : route('database.pekerja.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Group</label>
        <select name="input[id_group]" id="select_group" data-placeholder="-- Pilih Group --"
            data-route="{{ route('helper.getGroup') }}" class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>No. Register</label>
        <input type="text" value="{{ $data->no_register ?? '' }}" class="form-control" name="input[no_register]"
            required>
    </div>
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>No Hp</label>
        <input type="text" value="{{ $data->no_hp ?? '' }}" class="form-control number-only" name="input[no_hp]"
            required>
    </div>
</form>
