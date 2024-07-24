<form
    action="{{ isset($id) ? route('production.operasional_dyeing.update', $id) : route('production.operasional_dyeing.store') }}"
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
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    <div class="form-group">
        <label>Proses</label>
        <select name="input[proses]" id="select_proses" data-placeholder="-- Pilih Proses --"
            class="form-control select2" required>
            @php
                $proses = $data->proses ?? '';
            @endphp
            <option value=""></option>
            <option value="LIMBAH" {{ $proses == 'LIMBAH' ? 'selected' : '' }}>LIMBAH</option>
            <option value="CUCI MESIN" {{ $proses == 'CUCI MESIN' ? 'selected' : '' }}>CUCI MESIN</option>
        </select>
    </div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>
