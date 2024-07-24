<div class="form-group">
    <label>Tanggal</label>
    <input type="date" name="input[tanggal]" class="form-control"
        value="{{ isset($data->tanggal) ? $data->tanggal : date('Y-m-d') }}">
</div>
<div class="form-group">
    <label>Softcone</label>
    <select id="select_barang" onchange="changeBarang($(this));" data-filter="1" data-id-dyeing="{{ $idParent }}"
        data-route="{{ route('helper.getBarangDyeing') }}" data-placeholder="-- Pilih Jenis Benang --"
        class="form-control select2" required>
    </select>
</div>
<input type="hidden" name="input[id_parent]">
<input type="hidden" name="input[id_gudang]">
<input type="hidden" name="current_id_mesin">
<div class="form-group">
    <label>Mesin</label>
    <select name="input[id_mesin]" id="select_mesin" data-placeholder="-- Pilih Mesin --"
        data-route="{{ route('helper.getMesin') }}" data-jenis="DYEING" class="form-control select2" required></select>
</div>
<div class="form-group">
    <label>Warna</label>
    <select name="input[id_warna]" id="select_warna" data-placeholder="-- Pilih Warna --"
        data-route="{{ route('helper.getWarna') }}" data-filter-jenis="SINGLE" class="form-control select2" required>
    </select>
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
        readonly required>
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
    <input type="text" value="" class="form-control number-only" name="input[volume_2]" readonly>
    <div id="wrapperSuggestionStok2"></div>
    {{-- </div> --}}
    {{-- <div class="col-md-4">
        <label>Satuan 2 (Pilihan)</label>
        <input type="hidden" name="input[id_satuan_2]" value="{{ $data->id_satuan_2 }}">
        <input type="text" class="form-control" id="txt_satuan_2" value="{{ $attr['nama_satuan_2'] }}" readonly>
    </div> --}}
</div>
