<form
    action="{{ isset($id) ? route('production.pengiriman_barang.update', $id) : route('production.pengiriman_barang.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="state" value="{{ $attr['state'] }}">
    <input type="hidden" id="idGudangTable" value="{{ $attr['idGudang'] }}">

    <input type="hidden" name="input[id_pengiriman_barang]" value="{{ $idParent }}">
    <input type="hidden" name="current_code">
    <input type="hidden" name="id_tipe" value="{{ $attr['id_tipe'] }}">
    <input type="hidden" name="id_log_stok" value="{{ $attr['idLogStok'] }}">

    <input type="hidden" name="input[id_barang]">
    <input type="hidden" id="select_gudang" value="99999">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    @if ($attr['state'] == 'input')
        <div class="form-group">
            <label>Gudang Asal</label>
            <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
                data-placeholder="-- Pilih Gudang --" data-model="LogStokPenerimaan"
                data-route="{{ route('helper.getGudang') }}" class="form-control select2"></select>
        </div>
        <div class="form-group">
            <label>Barang</label>
            <select id="select_barang" data-filter-code="{{ $attr['code_tipe'] }}" onchange="changeBarang($(this));"
                data-placeholder="-- Pilih Barang --"
                @if ($attr['id_tipe'] == 1) data-filter="1" {{-- BENANG --}}
                @elseif ($attr['id_tipe'] == 8) data-filter="7" data-filter-motif-khusus="inspekting" {{-- SARUNG --}}
                @elseif ($attr['id_tipe'] == 9) data-filter="2" {{-- PEWARNA --}}
                @elseif ($attr['id_tipe'] == 5 || $attr['id_tipe'] == 6 || $attr['id_tipe'] == 15 || $attr['id_tipe'] == 16) data-filter-is-beam="YA" @endif {{-- BEAM --}}
                data-route="{{ route('helper.getBarangWithStok') }}" class="form-control select2" required>
            </select>
        </div>
    @else
        <div class="form-group">
            <label>Gudang Tujuan</label>
            <select name="input[id_gudang]" id="select_gudang_2" onchange="changeGudang($(this))"
                data-placeholder="-- Pilih Gudang --" data-route="{{ route('helper.getGudang') }}"
                class="form-control select2"></select>
        </div>
        <div class="form-group">
            <label>Barang</label>
            <select id="select_barang" onchange="changeBarang($(this));" data-id-pengiriman="{{ $idParent }}"
                data-placeholder="-- Pilih Barang --" data-route="{{ route('helper.getBarangPengiriman') }}"
                class="form-control select2" required>
            </select>
        </div>
    @endif
    <div id="wrapperFieldBeam"></div>
    <div id="wrapperFieldSongket"></div>
    <div id="wrapperFieldTanggalPotong"></div>
    <div id="wrapperFieldMesin"></div>
    <div id="wrapperFieldTipePraTenun"></div>
    <div id="wrapperFieldSizing"></div>
    <div id="wrapperFieldWarna"></div>
    <div id="wrapperFieldMotif"></div>
    <div id="wrapperFieldGrade"></div>
    <div id="wrapperFieldKualitas"></div>
    @if ($attr['state'] == 'input')
        <div class="form-group">
            <label>Satuan 1 (Utama)</label>
            <input type="hidden" name="input[id_satuan_1]">
            <input type="text" class="form-control" id="txt_satuan_1" readonly>
        </div>
        <div class="form-group">
            <label>Volume 1</label>
            <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
                name="input[volume_1]" required>
            <div id="wrapperSuggestionStok1"></div>
        </div>
        <div id="wrapperFieldSatuan2"></div>
    @else
        <div class="form-group">
            <label>Satuan 1 (Utama)</label>
            <select id="select_satuan_1" name="input[id_satuan_1]" data-placeholder="-- Pilih Satuan --"
                data-route="{{ route('helper.getSatuan') }}"
                @if ($attr['id_tipe'] == 1) data-filter-id="2" @endif class="form-control select2" required>
            </select>
            {{-- <input type="hidden" name="input[id_satuan_1]">
            <input type="text" class="form-control" id="txt_satuan_1" readonly> --}}
        </div>
        <div class="form-group">
            <label>Volume 1</label>
            <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
                name="input[volume_1]">
            <div id="wrapperSuggestionStok1"></div>
        </div>
        <div class="form-group">
            <label>Satuan 2 (Pilihan)</label>
            <select id="select_satuan_2" name="input[id_satuan_2]" data-placeholder="-- Pilih Satuan --"
                data-route="{{ route('helper.getSatuan') }}"
                @if ($attr['id_tipe'] == 1) data-filter-id="0" @endif class="form-control select2">
            </select>
            {{-- <input type="hidden" name="input[id_satuan_2]">
            <input type="text" class="form-control" id="txt_satuan_2" readonly> --}}
        </div>
        <div class="form-group">
            <label>Volume 2</label>
            <input type="text" value="{{ $data->volume_2 ?? '' }}" class="form-control number-only"
                name="input[volume_2]">
            <div id="wrapperSuggestionStok2"></div>
        </div>
    @endif

    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
