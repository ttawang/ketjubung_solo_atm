<form action="{{ isset($id) ? route('production.tyeing.update', $id) : route('production.tyeing.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 300px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_tyeing]" value="{{ $idParent }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" name="input[tanggal]"
            required />
    </div>
    <div class="form-group">
        <label>Pekerja</label>
        <select name="input[id_pekerja]" id="select_pekerja" data-placeholder="-- Pilih Pekerja --"
            data-route="{{ route('helper.getPekerja') }}" data-flag="tyeing" class="form-control select2"
            required></select>
    </div>
</form>
