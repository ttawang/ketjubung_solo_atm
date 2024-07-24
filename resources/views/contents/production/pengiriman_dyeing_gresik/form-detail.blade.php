<form
    action="{{ isset($id) ? route('production.pengiriman_dyeing_gresik.update', $id) : route('production.pengiriman_dyeing_gresik.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_pengiriman_dyeing_gresik]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok" value="{{ $data->id_log_stok ?? '' }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="curr_volume_1" value="{{ $currVolume1 }}">
    <input type="hidden" name="curr_volume_2" value="{{ $currVolume2 }}">
    <input type="hidden" name="code" value="{{ $data->tipe }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="input[tanggal]" class="form-control"
            value="{{ isset($data->tanggal) ? $data->tanggal : date('Y-m-d') }}">
    </div>
    <input type="hidden" id="select_gudang" value="99999">
    <div class="form-group">
        <label>Gudang</label>
        <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
            data-id-gudang-selected="2" data-placeholder="-- Pilih Gudang --" data-model="LogStokPenerimaan"
            data-route="{{ route('helper.getGudang') }}" class="form-control select2" required></select>
    </div>
    <div class="form-group">
        <label>Jenis Benang</label>
        <select id="select_barang" onchange="changeBarang($(this));" data-filter="1"
            data-filter-code="{{ $data->tipe }}" data-route="{{ route('helper.getBarangWithStok') }}"
            data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
        </select>
    </div>
    <div id="wrapperFieldWarna"></div>
    @if ($data->tipe == 'BDG')
        <div class="form-group">
            <label>Satuan 1</label>
            <input type="hidden" name="input[id_satuan_1]" value="1">
            <input type="text" id="txt_satuan_1" value="Cones" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Volume 1</label>
            <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
                name="input[volume_1]">
            <div id="wrapperSuggestionStok1"></div>
        </div>
        <div class="form-group">
            <label>Satuan 2</label>
            <input type="hidden" name="input[id_satuan_2]" value="2">
            <input type="text" id="txt_satuan_2" value="Kg" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Volume 2</label>
            <input type="text" value="{{ $data->volume_2 ?? '' }}" class="form-control number-only"
                name="input[volume_2]">
            <div id="wrapperSuggestionStok2"></div>
        </div>
    @else
        <div class="form-group">
            <label>Satuan 1</label>
            <input type="hidden" name="input[id_satuan_1]" value="2">
            <input type="text" id="txt_satuan_1" value="Kg" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Volume 1</label>
            <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
                name="input[volume_1]">
            <div id="wrapperSuggestionStok1"></div>
        </div>
    @endif
</form>
