<form action="{{ isset($id) ? route('production.doubling.update', $id) : route('production.doubling.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="input[id_doubling]" value="{{ $idParent }}">
    <input type="hidden" name="id_log_stok" value="{{ $data->id_log_stok ?? '' }}">
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
    <div class="form-group">
        <label>Jenis Benang</label>
        @if ($status == 'KIRIM')
            <input type="hidden" name="input[id_barang]">
            <select id="select_barang" onchange="changeBarang($(this));" data-filter="1" data-filter-code="PB"
                data-route="{{ route('helper.getBarangWithStok') }}" data-placeholder="-- Pilih Jenis Benang --"
                class="form-control select2" required>
            </select>
        @else
            {{-- <select id="select_barang" name="input[id_parent_detail]" data-id-doubling="{{ $idParent }}"
                onchange="changeBarangTerima($(this));" data-route="{{ route('helper.getDoubling') }}"
                data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
            </select> --}}

            <select id="select_barang" name="input[id_barang]" data-filter-tipe="1" data-route="{{ route('helper.getBarang') }}"
                data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
            </select>
        @endif
    </div>
    <div class="form-group">
        <label>Satuan 1</label>
        <select id="select_satuan_1" name="input[id_satuan_1]" allow-clear="false"
            data-route="{{ route('helper.getEmptySelect') }}" data-placeholder="-- Pilih Satuan 1 --"
            class="form-control select2">
        </select>
    </div>
    <div class="form-group">
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]" required>
    </div>
</form>
