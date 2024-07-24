<div class="form-group">
    <label>Jenis Benang</label>
    <select id="select_barang" name="input[id_parent_detail]" data-id-dyeing-grey="{{ $idParent }}"
        onchange="changeBarangTerima($(this));" data-route="{{ route('helper.getDyeingGrey') }}"
        data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
    </select>
</div>
<div class="form-group">
    <label>Warna</label>
    <input type="hidden" name="input[id_warna]" value="33">
    <input type="text" id="txt_warna" value="PT" class="form-control" disabled>
</div>
<div class="form-group">
    <label>Satuan 1</label>
    <input type="hidden" name="input[id_satuan_1]" value="1">
    <input type="text" id="txt_satuan_1" value="Cones" class="form-control" disabled>
</div>
<div class="form-group">
    <label>Volume 1</label>
    <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only" name="input[volume_1]"
        required>
</div>
<div class="form-group">
    <label>Satuan 2</label>
    <input type="hidden" name="input[id_satuan_2]" value="2">
    <input type="text" id="txt_satuan_2" value="Kg" class="form-control" disabled>
</div>
<div class="form-group">
    <label>Volume 2</label>
    <input type="text" value="{{ $data->volume_2 ?? '' }}" class="form-control number-only" name="input[volume_2]"
        required>
</div>
