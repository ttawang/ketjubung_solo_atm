<form action="{{ isset($id) ? route('production.cucuk.update', $id) : route('production.cucuk.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="false">
    <input type="hidden" name="tipe_pra_tenun" value="CUCUK">
    <input type="hidden" name="old_id_beam" value="{{ $data->id_beam ?? '' }}">
    <input type="hidden" name="id_log_stok_masuk" value="{{ $data->id_log_stok_masuk ?? '' }}">
    <input type="hidden" name="id_log_stok_keluar" value="{{ $data->id_log_stok_keluar ?? '' }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    <div class="form-group">
        <label>No. KIKW</label>
        <select id="select_beam" name="input[id_beam]" data-placeholder="-- Pilih No KIKW --"
            data-id-parent="{{ isset($id) ? $id : '' }}" data-flag="cucuk" onchange="changeNoBeam($(this))"
            data-route="{{ route('helper.getBeam') }}" class="form-control select2" required></select>
    </div>
    <div id="wrapperFieldNoBeam"></div>
    <div id="wrapperFieldMesin"></div>
    <div id="wrapperFieldSizing"></div>
</form>
