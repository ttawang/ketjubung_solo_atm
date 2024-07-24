<form
    action="{{ isset($id) ? route('production.penerimaan_chemical_dyeing.update', $id) : route('production.penerimaan_chemical_dyeing.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_penerimaan_chemical]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok" value="{{ $attr['idLogStok'] }}">
    <input type="hidden" name="tanggal" id="tanggal" value="{{ $data->tanggal }}">
    <input type="hidden" name="input[code]" value="DW">
    {{-- <div class="form-group">
        <label>Proses</label>
        <select name="input[code]" id="select_proses" data-placeholder="-- Pilih Proses --"
            data-id-penerimaan="{{ $idParent }}" class="form-control select2" required>
            <option value=""></option>
            <option value="CJ">Jigger & Cuci Sarung</option>
            <option value="CD">Drying (Pengeringan)</option>
        </select>
    </div> --}}
    <div class="form-group">
        <label>Gudang</label>
        <select name="input[id_gudang]" id="select_gudang" data-placeholder="-- Pilih Gudang --"
            data-route="{{ route('helper.getEmptySelect') }}" allow-clear="false" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Barang</label>
        <select name="input[id_barang]" id="select_barang" data-placeholder="-- Pilih Barang --"
            data-route="{{ route('helper.getBarang') }}" data-filter-tipe="2"
            data-id-penerimaan-chemical="{{ $idParent }}" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Satuan 1 (Utama)</label>
        <select name="input[id_satuan_1]" id="select_satuan_1" data-placeholder="-- Pilih Satuan Utama --"
            data-route="{{ route('helper.getEmptySelect') }}" allow-clear="false" class="form-control select2" required>
        </select>
    </div>
    <div class="form-group">
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]" required>
    </div>
    {{-- <div class="form-group">
        <label>Satuan 2 (Pilihan)</label>
        <select name="input[id_satuan_2]" id="select_satuan_2" data-placeholder="-- Pilih Satuan Pilihan --"
            data-route="{{ route('helper.getSatuan') }}" class="form-control select2">
        </select>
    </div>
    <div class="form-group">
        <label>Volume 2</label>
        <input type="text" value="{{ $data->volume_2 ?? '' }}" class="form-control number-only"
            name="input[volume_2]">
    </div> --}}
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
