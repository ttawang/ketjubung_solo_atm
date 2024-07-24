<form action="{{ route('production.absensi_pekerja.update', $id) }}" onsubmit="submitForm(event, $(this));" method="POST"
    class="formInput" style="height: 450px;">
    @method('PATCH')
    <input type="hidden" name="arr_id" value="{{ $data->arr_id ?? '' }}">
    <input type="hidden" name="input[lembur]" value="{{ $data->lembur ?? '' }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" readonly />
    </div>
    <div class="form-group">
        <label>Pekerja</label>
        <select name="input[id_pekerja]" id="select_pekerja" data-placeholder="-- Pilih Pekerja --"
            onchange="changePekerja($(this));" data-route="{{ route('helper.getPekerja') }}"
            class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>Group</label>
        <input type="hidden" name="input[id_group]">
        <input type="text" id="txt_group" class="form-control" disabled>
        {{-- <select name="input[id_group]" id="select_group" data-placeholder="-- Pilih Group --"
            data-route="{{ route('helper.getGroup') }}" class="form-control select2" required></select> --}}
    </div>
    <div class="form-group">
        <label>Mesin</label>
        <select name="input[id_mesin][]" id="select_mesin" multiple data-placeholder="-- Pilih Mesin --"
            data-route="{{ route('helper.getMesin') }}" data-jenis="LOOM" class="form-control select2"
            required></select>
    </div>
    <div class="form-group">
        <label>Shift</label>
        <input type="text" name="input[shift]" value="{{ $data->shift ?? '' }}" class="form-control" readonly>
    </div>
</form>
