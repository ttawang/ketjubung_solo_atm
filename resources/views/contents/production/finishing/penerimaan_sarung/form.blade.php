<form
    action="{{ isset($id) ? route('production.penerimaan_sarung.update', $id) : route('production.penerimaan_sarung.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="false">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    <div class="form-group">
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    <div class="form-group">
        <label>Dari</label>
        <select name="input[from]" id="select_from" data-placeholder="-- Pilih --" class="form-control select2"
            required>
            <option value=""></option>
            <option value="GRESIK">GRESIK</option>
            <option value="FINISHEDGOODS">FINISHEDGOODS</option>
        </select>
    </div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
