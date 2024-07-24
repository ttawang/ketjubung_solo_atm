<form action="{{ route('helper.storeLembur') }}" onsubmit="submitForm(event, $(this));" method="POST" class="formInput"
    style="height: 450px;">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="input[tanggal]" />
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
            data-route="{{ route('helper.getMesin') }}" data-jenis="LOOM" class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>Shift</label>
        <select name="input[shift]" id="select_shift" data-placeholder="-- Pilih Shift --" class="form-control select2"
            required>
            <option value=""></option>
            <option value="PAGI">PAGI</option>
            <option value="SIANG">SIANG</option>
            <option value="MALAM">MALAM</option>
        </select>
    </div>
</form>
