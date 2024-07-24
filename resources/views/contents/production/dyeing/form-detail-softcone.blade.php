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
<div class="form-group">
    <label>Mesin</label>
    <select name="input[id_mesin]" id="select_mesin" data-placeholder="-- Pilih Mesin --"
        data-route="{{ route('helper.getMesin') }}" data-jenis="DYEING" class="form-control select2" required></select>
</div>
<div class="form-group">
    <label>
        Jenis Benang
    </label>
    <select id="select_barang" onchange="changeBarang($(this));" data-filter="1" data-filter-code="{{ $attr['code'] }}"
        data-filter-dyeing="SOFTCONE" data-route="{{ route('helper.getBarangWithStok') }}"
        data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
    </select>
    <div id="wrapperSuggestionStok2"></div>
</div>
<div class="form-group">
    <label>Satuan 1</label>
    <select id="select_satuan_1" name="input[id_satuan_1]" allow-clear="false" data-id-gudang-table="2"
        data-tipe-satuan="utama" data-placeholder="-- Pilih Satuan --" data-route="{{ route('helper.getSatuan') }}"
        class="form-control select2">
    </select>
</div>
<div class="form-group">
    {{-- <div class="col-md-8"> --}}
    <label>Volume 1</label>
    <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only" name="input[volume_1]"
        required>
    <div id="wrapperSuggestionStok1"></div>
    {{-- </div> --}}
    {{-- <div class="col-md-4">
        <label>Satuan 1 (Utama)</label>
        <input type="hidden" name="input[id_satuan_1]" value="{{ $data->id_satuan_1 }}">
        <input type="text" class="form-control" id="txt_satuan_1" value="{{ $attr['nama_satuan_1'] }}" readonly>
    </div> --}}
</div>
<div class="form-group">
    <label>Satuan 2</label>
    <select id="select_satuan_2" name="input[id_satuan_2]" allow-clear="false" data-id-gudang-table="2"
        data-tipe-satuan="pilihan" data-placeholder="-- Pilih Satuan --" data-route="{{ route('helper.getSatuan') }}"
        class="form-control select2">
    </select>
</div>
<div class="form-group">
    {{-- <div class="col-md-8"> --}}
    <label>Volume 2</label>
    <input type="text" value="" class="form-control number-only" name="input[volume_2]">
    {{-- </div> --}}
    {{-- <div class="col-md-4">
        <label>Satuan 2 (Pilihan)</label>
        <input type="hidden" name="input[id_satuan_2]" value="{{ $data->id_satuan_2 }}">
        <input type="text" class="form-control" id="txt_satuan_2" value="{{ $attr['nama_satuan_2'] }}" readonly>
    </div> --}}
</div>
