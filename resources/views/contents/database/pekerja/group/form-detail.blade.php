<form action="{{ isset($id) ? route('database.group.update', $id) : route('database.group.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 200px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_group]" value="{{ $idParent }}">
    <div class="form-group">
        <label>Pekerja</label>
        @if (isset($id))
            <select id="select_pekerja" name="input[id_pekerja]" data-route="{{ route('helper.getPekerja') }}"
                data-flag="group" data-placeholder="-- Pilih Pekerja --" class="form-control select2" required>
            </select>
        @else
            <select id="select_pekerja" multiple name="input[id_pekerja][]"
                data-route="{{ route('helper.getPekerja') }}" data-flag="group" data-placeholder="-- Pilih Pekerja --"
                class="form-control select2" required>
            </select>
        @endif
    </div>
</form>
