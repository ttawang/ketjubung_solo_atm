<form action="{{ isset($id) ? route('production.dyeing.update', $id) : route('production.dyeing.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[status]" value="{{ $attr['status'] }}">
    <input type="hidden" name="input[id_dyeing]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok_masuk" value="{{ $attr['idLogStokMasuk'] }}">
    <input type="hidden" name="id_log_stok_keluar" value="{{ $attr['idLogStokKeluar'] }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="input[key]" value="{{ $attr['key'] }}">
    <input type="hidden" name="volume_kirim">
    <input type="hidden" name="satuan_kirim">
    <input type="hidden" name="volume_2_kirim">
    <input type="hidden" name="satuan_2_kirim">
    <input type="hidden" name="curr_volume_2" value="{{ $attr['curr_volume_2'] }}">
    <input type="hidden" name="code_retur" value="{{ $attr['code'] }}">
    @include('contents.production.dyeing.form-detail-' . strtolower($attr['status']))
</form>
