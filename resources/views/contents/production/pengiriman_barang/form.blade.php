<form
    action="{{ isset($id) ? route('production.pengiriman_barang.update', $id) : route('production.pengiriman_barang.store') }}"
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
        <label>Tipe Pengiriman</label>
        <div id="wrapperTipePengiriman">
            @if (isset($data))
                @if ($data->id_tipe_pengiriman != null)
                    <select name="input[id_tipe_pengiriman]" id="select_tipe_pengiriman"
                        data-placeholder="-- Pilih Tipe Pengiriman --"
                        data-route="{{ route('helper.getTipePengiriman') }}" class="form-control select2"
                        required></select>
                @else
                    <input type="text" value="{{ $data->txt_tipe_pengiriman }}" id="select_tipe_pengiriman"
                        class="form-control" name="input[txt_tipe_pengiriman]" required>
                @endif
            @else
                <select name="input[id_tipe_pengiriman]" id="select_tipe_pengiriman"
                    data-placeholder="-- Pilih Tipe Pengiriman --" data-route="{{ route('helper.getTipePengiriman') }}"
                    class="form-control select2" required></select>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    {{-- <div class="form-group">
        <div class="checkbox-custom checkbox-primary">
            @if (isset($data))
                <input type="checkbox" id="checkNonTipe"
                    data-tipe-pengiriman="{{ $data->relTipePengiriman()->value('name') }}"
                    data-id-tipe-pengiriman="{{ $data->id_tipe_pengiriman ?? $data->txt_tipe_pengiriman }}"
                    onchange="checkingTipeLainnya($(this));" {{ $data->id_tipe_pengiriman != null ? '' : 'checked' }}>
            @else
                <input type="checkbox" id="checkNonTipe" data-tipe-pengiriman="" data-id-tipe-pengiriman=""
                    onchange="checkingTipeLainnya($(this));">
            @endif
            <label for="checkNonTipe">Tipe Pengiriman Lainnya?</label>
        </div>
    </div> --}}
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
