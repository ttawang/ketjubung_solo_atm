<form action="{{ isset($id) ? route('database.kualitas.update', $id) : route('database.kualitas.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_kualitas]" value="{{ $idParent }}">
    <div class="form-group">
        <label>Kode</label>
        <input type="text" value="{{ $data->kode ?? '' }}" class="form-control" name="input[kode]">
    </div>
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]">
    </div>
</form>
