<form action="{{ isset($id) ? route('database.nomor_beam.update', $id) : route('database.nomor_beam.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>No. Beam</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Alias</label>
        <input type="text" value="{{ $data->alias ?? '' }}" class="form-control" name="input[alias]" required>
    </div>
    {{-- <div class="form-group">
        @php
            $jenis = $data->jenis ?? '';
        @endphp
        <label>Jenis</label>
        <select name="input[jenis]" class="form-control">
            <option value="">-- Pilih Jenis --</option>
            <option value="LUSI" {{ $jenis == 'LUSI' ? 'selected' : '' }}>LUSI</option>
            <option value="SONGKET" {{ $jenis == 'SONGKET' ? 'selected' : '' }}>SONGKET</option>
        </select>
    </div> --}}
</form>
