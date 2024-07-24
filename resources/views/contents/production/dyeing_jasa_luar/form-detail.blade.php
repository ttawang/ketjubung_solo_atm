<form
    action="{{ isset($id) ? route('production.dyeing_jasa_luar.update', $id) : route('production.dyeing_jasa_luar.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_dyeing_jasa_luar]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok" value="{{ $data->id_log_stok ?? '' }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="input[status]" value="{{ $status }}">
    <input type="hidden" name="curr_volume_1" value="{{ $currVolume1 }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="input[tanggal]" class="form-control"
            value="{{ isset($data->tanggal) ? $data->tanggal : date('Y-m-d') }}">
    </div>
    <input type="hidden" id="select_gudang" value="99999">
    <div class="form-group">
        <label>Gudang</label>
        <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
            data-placeholder="-- Pilih Gudang --" data-model="LogStokPenerimaan"
            data-route="{{ route('helper.getGudang') }}" class="form-control select2" required></select>
    </div>
    @include('contents.production.dyeing_jasa_luar.form-detail-' . strtolower($status))
</form>
