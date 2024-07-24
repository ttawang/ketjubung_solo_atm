<form
    action="{{ isset($id) ? route('production.dyeing_jasa_luar.update', $id) : route('production.dyeing_jasa_luar.store') }}"
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
        <label>No. SPK</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    <div class="form-group">
        <label>Vendor</label>
        <select name="input[id_supplier]" id="select_supplier" data-placeholder="-- Pilih Vendor --"
            data-route="{{ route('helper.getSupplier') }}" class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
