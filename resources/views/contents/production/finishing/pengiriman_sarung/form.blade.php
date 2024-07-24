<form
    action="{{ isset($id) ? route('production.pengiriman_sarung.update', $id) : route('production.pengiriman_sarung.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" name="isDetail" value="false">
    <input type="hidden" name="input[tipe]" value="{{ $tipe }}">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control" onchange=""
            name="input[tanggal]" required />
    </div>
    <div class="form-group">
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    @if ($tipe == 'LUAR')
        <div class="form-group">
            @php
                $tipeSelected = $data->tipe_selected ?? '';
            @endphp
            <label>Dikirim Ke: </label>
            <select name="input[tipe_selected]" id="select_tipe_selected" data-placeholder="-- Pilih --"
                class="form-control select2" required>
                <option value=""></option>
                <option value="GRESIK" {{ $tipeSelected == 'GRESIK' ? 'selected' : '' }}>GRESIK</option>
                <option value="FINISHEDGOODS" {{ $tipeSelected == 'FINISHEDGOODS' ? 'selected' : '' }}>FINISHED GOODS
                </option>
            </select>
        </div>
    @endif
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
