<form action="{{ isset($id) ? route('database.kualitas.update', $id) : route('database.kualitas.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="false">
    <div class="form-group">
        <label>Grade</label>
        <input type="text" value="{{ $data->grade ?? '' }}" class="form-control" name="input[grade]">
    </div>
    <div class="form-group">
        <label>Alias</label>
        <input type="text" value="{{ $data->alias ?? '' }}" class="form-control" name="input[alias]">
    </div>
</form>
