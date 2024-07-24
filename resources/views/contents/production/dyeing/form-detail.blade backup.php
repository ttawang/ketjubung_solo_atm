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

    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="input[tanggal]" class="form-control"
            value="{{ isset($data->tanggal) ? $data->tanggal : date('Y-m-d') }}">
    </div>
    @if ($attr['status'] == 'SOFTCONE')
        <input type="hidden" id="select_gudang" value="99999">
        <div class="form-group">
            <label>Gudang</label>
            <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
                data-placeholder="-- Pilih Gudang --" data-model="LogStokPenerimaan"
                data-route="{{ route('helper.getGudang') }}" class="form-control select2" required></select>
        </div>
    @endif
    <div class="form-group">
        <label>
            @if ($attr['status'] == 'DYEOVEN')
                Softcone
            @elseif($attr['status'] == 'OVERCONE')
                Dyoven
            @else
                Jenis Benang
            @endif
        </label>
        <select id="select_barang" onchange="changeBarang($(this));" data-filter="1"
            @if ($attr['status'] == 'OVERCONE') data-filter-warna="YA" data-filter-dyeing="DYEOVEN" @endif
            @if ($attr['status'] == 'SOFTCONE') data-filter-code="{{ $attr['code'] }}"
            data-filter-dyeing="SOFTCONE" data-route="{{ route('helper.getBarangWithStok') }}" @else 
            data-id-dyeing="{{ $idParent }}" data-route="{{ route('helper.getBarangDyeing') }}" @endif
            data-placeholder="-- Pilih Jenis Benang --" class="form-control select2" required>
        </select>
    </div>
    @if ($attr['status'] != 'SOFTCONE')
        <input type="hidden" name="input[id_parent]">
        <input type="hidden" name="input[id_gudang]">
        <div class="form-group">
            <label>Warna</label>
            @if ($attr['status'] == 'OVERCONE')
                <input type="hidden" name="input[id_warna]">
                <input type="text" class="form-control" id="nama_warna" readonly>
            @else
                <select name="input[id_warna]" id="select_warna" data-placeholder="-- Pilih Warna --"
                    data-route="{{ route('helper.getWarna') }}" data-filter-jenis="SINGLE" class="form-control select2"
                    required>
                </select>
            @endif
        </div>
    @endif
    <div class="form-group">
        <label>Satuan 1</label>
        <select id="select_satuan_1" name="input[id_satuan_1]" allow-clear="false" data-id-gudang-table="2" data-tipe-satuan="utama"
            data-placeholder="-- Pilih Satuan --" data-route="{{ route('helper.getSatuan') }}"
            class="form-control select2">
        </select>
    </div>
    <div class="form-group">
        {{-- <div class="col-md-8"> --}}
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]"  @if ($attr['status'] == 'DYEOVEN') readonly @endif required>
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
        <select id="select_satuan_2" name="input[id_satuan_2]" allow-clear="false" data-id-gudang-table="2" data-tipe-satuan="pilihan"
            data-placeholder="-- Pilih Satuan --" data-route="{{ route('helper.getSatuan') }}"
            class="form-control select2">
        </select>
    </div>
    <div class="form-group">
        {{-- <div class="col-md-8"> --}}
        <label>Volume 2</label>
        <input type="text" value="" class="form-control number-only" name="input[volume_2]" @if ($attr['status'] == 'DYEOVEN') readonly @endif>
        <div id="wrapperSuggestionStok2"></div>
        {{-- </div> --}}
        {{-- <div class="col-md-4">
            <label>Satuan 2 (Pilihan)</label>
            <input type="hidden" name="input[id_satuan_2]" value="{{ $data->id_satuan_2 }}">
            <input type="text" class="form-control" id="txt_satuan_2" value="{{ $attr['nama_satuan_2'] }}" readonly>
        </div> --}}
    </div>
</form>
