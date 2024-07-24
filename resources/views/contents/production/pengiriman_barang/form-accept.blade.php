<form action="{{ route('helper.accept') }}" onsubmit="submitForm(event, $(this));" method="POST" class="formInput"
    style="height: 450px;">
    <input type="hidden" name="input[id_parent_detail]" value="{{ $id }}">
    <input type="hidden" name="input[id_pengiriman_barang]" value="{{ $idParent }}">
    <input type="hidden" name="input[code]" value="{{ $code }}">
    <input type="hidden" name="input[id_barang]">
    <input type="hidden" name="id_tipe" value="{{ $idTipe }}">
    @if ($data->validated_at != null)
        <div class="form-group">
            <div class="alert alert-icon alert-success" role="alert">
                <i class="icon md-check" aria-hidden="true"></i> Telah Divalidasi pada Tanggal
                <em>{{ App\Helpers\Date::format($data->validated_at, 98) }}</em>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    <div class="form-group">
        <label>Gudang Tujuan</label>
        <select name="input[id_gudang]" id="select_gudang" allow-clear="false" data-placeholder="-- Pilih Gudang --"
            data-route="{{ route('helper.getGudang') }}" class="form-control select2"></select>
    </div>
    <div class="form-group">
        <label>Barang</label>
        <select id="select_barang" onchange="changeBarang($(this));" allow-clear="false"
            data-route="{{ route('helper.getEmptySelect') }}" data-placeholder="-- Pilih Barang --"
            class="form-control select2">
        </select>
    </div>
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
    <div class="form-group">
        <label>Satuan 1 (Utama)</label>
        <select id="select_satuan_1" name="input[id_satuan_1]" allow-clear="false"
            data-placeholder="-- Pilih Satuan Utama --" class="form-control select2"
            data-route="{{ route('helper.getEmptySelect') }}">
        </select>
    </div>
    <div class="form-group">
        <label>Volume 1</label>
        <input type="text" value="{{ $data->volume_1 ?? '' }}" class="form-control number-only"
            name="input[volume_1]">
        <div id="wrapperSuggestionStok1"></div>
    </div>
    @if ($data->id_satuan_2 != null && $data->volume_2 != null)
        <div class="form-group">
            <label>Satuan 2 (Pilihan)</label>
            <select id="select_satuan_2" name="input[id_satuan_2]" allow-clear="false"
                data-route="{{ route('helper.getEmptySelect') }}" data-placeholder="-- Pilih Satuan Pilihan --"
                class="form-control select2">
            </select>
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
