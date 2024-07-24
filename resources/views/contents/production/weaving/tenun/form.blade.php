<form action="{{ isset($id) ? route('production.tenun.update', $id) : route('production.tenun.store') }}"
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
        <label>No. KIKW</label>
        <select id="select_beam" name="input[id_beam]" data-placeholder="-- Pilih No KIKW --" data-flag="tenun"
            data-id-parent="{{ isset($id) ? $id : '' }}" onchange="changeNoBeam($(this))"
            data-route="{{ route('helper.getBeam') }}" class="form-control select2" required></select>
    </div>
    <div id="wrapperFieldNoBeam"></div>
    <div id="wrapperFieldMesin"></div>
    <div id="wrapperFieldSizing"></div>
    <div id="wrapperFieldTipePraTenun"></div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
