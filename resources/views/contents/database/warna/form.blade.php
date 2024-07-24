<form action="{{ isset($id) ? route('database.warna.update', $id) : route('database.warna.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Alias</label>
        <input type="text" value="{{ $data->alias ?? '' }}" class="form-control" name="input[alias]" required>
    </div>
    <div class="form-group">
        <label>Jenis</label>
        <select name="input[jenis]" id="select_jenis" class="form-control">
            @php $jenis = $data->jenis ?? ''; @endphp
            <option value="SINGLE" {{ $jenis == 'SINGLE' ? 'selected' : '' }}>Single</option>
            <option value="KOMBINASI" {{ $jenis == 'KOMBINASI' ? 'selected' : '' }}>Kombinasi</option>
        </select>
    </div>
</form>
