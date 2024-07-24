<form
    action="{{ isset($id) ? route('production.penerimaan_barang.update', $id) : route('production.penerimaan_barang.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="false">
    <div class="form-group">
        <label>Tanggal PO</label>
        <input type="date" value="{{ $data->tanggal_po ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal_po]" required />
    </div>
    <div class="form-group">
        <label>Tanggal Terima</label>
        <input type="date" value="{{ $data->tanggal_terima ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal_terima]" required />
    </div>
    <div class="form-group">
        <label>No. PO</label>
        <input type="text" value="{{ $data->no_po ?? '' }}" class="form-control" name="input[no_po]" required>
    </div>
    <div class="form-group">
        <label>Vendor</label>
        <select name="input[id_supplier]" id="select_supplier" data-placeholder="-- Pilih Vendor --"
            data-route="{{ route('helper.getSupplier') }}" class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>No. Kendaraan</label>
        <input type="text" value="{{ $data->no_kendaraan ?? '' }}" class="form-control" name="input[no_kendaraan]">
    </div>
    <div class="form-group">
        <label>Nama Supir</label>
        <input type="text" value="{{ $data->supir ?? '' }}" class="form-control" name="input[supir]">
    </div>
    <div class="form-group">
        <label>No. TTBM</label>
        <input type="text" value="{{ $data->no_ttbm ?? '' }}" class="form-control" name="input[no_ttbm]">
    </div>
</form>
