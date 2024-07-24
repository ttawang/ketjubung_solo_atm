<form
    action="{{ isset($id) ? route('production.distribusi_pakan.update', $id) : route('production.distribusi_pakan.store') }}"
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
        <label>Tipe</label>
        @if (isset($id))
            @if ($data->count_detail > 0)
                <input type="hidden" name="input[tipe]" value="{{ $data->tipe }}">
                <input type="text" class="form-control" value="{{ strtoupper($data->tipe) }}" readonly>
            @else
                <select name="input[tipe]" id="select_tipe" data-placeholder="-- Pilih Tipe --"
                    class="form-control select2" required>
                    <option value=""></option>
                    <option value="shuttle">SHUTTLE</option>
                    <option value="rappier">RAPPIER</option>
                    {{-- <option value="warna">BENANG WARNA</option> --}}
                </select>
            @endif
        @else
            <select name="input[tipe]" id="select_tipe" data-placeholder="-- Pilih Tipe --" class="form-control select2"
                required>
                <option value=""></option>
                <option value="shuttle">SHUTTLE</option>
                <option value="rappier">RAPPIER</option>
                {{-- <option value="warna">BENANG WARNA</option> --}}
            </select>
        @endif
    </div>
    <div class="form-group">
        <label>Nomor</label>
        <input type="text" value="{{ $data->nomor ?? '' }}" class="form-control" name="input[nomor]" required>
    </div>
    <div class="form-group">
        <label>Catatan</label>
        <textarea name="input[catatan]" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
    </div>
</form>

@if (isset($id))
    @if ($data->count_detail == 0)
        <script>
            $(document).ready(function() {
                $('#select_tipe').val("{{ $data->tipe }}").change();
            })
        </script>
    @endif
@endif
