<form action="{{ isset($id) ? route('database.mesin.update', $id) : route('database.mesin.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 350px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>No Mesin</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        @php $tipe = $data->tipe ?? ''; @endphp
        <label>Jenis</label>
        <select name="input[tipe]" class="form-control">
            <option value="">-- Pilih Jenis --</option>
            <option value="A" {{ $tipe == 'A' ? 'selected' : '' }}>A</option>
            <option value="B" {{ $tipe == 'B' ? 'selected' : '' }}>B</option>
            <option value="C" {{ $tipe == 'C' ? 'selected' : '' }}>C</option>
            <option value="D" {{ $tipe == 'D' ? 'selected' : '' }}>D</option>

            <option value="DIJUAL" {{ $tipe == 'DIJUAL' ? 'selected' : 'DIJUAL' }}>DIJUAL</option>
            <option value="DRA" {{ $tipe == 'DRA' ? 'selected' : 'DRA' }}>DRA</option>
            <option value="DRB-1" {{ $tipe == 'DRB-1' ? 'selected' : 'DRB-1' }}>DRB-1</option>
            <option value="DRB-2" {{ $tipe == 'DRB-2' ? 'selected' : 'DRB-2' }}>DRB-2</option>
            <option value="DRL" {{ $tipe == 'DRL' ? 'selected' : 'DRL' }}>DRL</option>
            <option value="DS" {{ $tipe == 'DS' ? 'selected' : 'DS' }}>DS</option>
            <option value="JKT" {{ $tipe == 'JKT' ? 'selected' : 'JKT' }}>JKT</option>
            <option value="JRB-1" {{ $tipe == 'JRB-1' ? 'selected' : 'JRB-1' }}>JRB-1</option>
            <option value="JRB-2" {{ $tipe == 'JRB-2' ? 'selected' : 'JRB-2' }}>JRB-2</option>
            <option value="JRB-3" {{ $tipe == 'JRB-3' ? 'selected' : 'JRB-3' }}>JRB-3</option>
            <option value="JRB-4" {{ $tipe == 'JRB-4' ? 'selected' : 'JRB-4' }}>JRB-4</option>
            <option value="JRNS" {{ $tipe == 'JRNS' ? 'selected' : 'JRNS' }}>JRNS</option>
            <option value="JRS" {{ $tipe == 'JRS' ? 'selected' : 'JRS' }}>JRS</option>
            <option value="STOCK" {{ $tipe == 'STOCK' ? 'selected' : 'STOCK' }}>STOCK</option>

            <option value="BESAR" {{ $tipe == 'BESAR' ? 'selected' : '' }}>Besar</option>
            <option value="FONG" {{ $tipe == 'FONG' ? 'selected' : '' }}>Fong</option>
            <option value="KECIL" {{ $tipe == 'KECIL' ? 'selected' : '' }}>Kecil</option>
        </select>
    </div>
    <div class="form-group">
        @php $jenis = $data->jenis ?? ''; @endphp
        <label>Proses</label>
        <select name="input[jenis]" class="form-control" required>
            <option value="">-- Pilih Proses --</option>
            <option value="DYEING" {{ $jenis == 'DYEING' ? 'selected' : '' }}>DYEING</option>
            <option value="WARPING" {{ $jenis == 'WARPING' ? 'selected' : '' }}>WARPING</option>
            <option value="LOOM" {{ $jenis == 'LOOM' ? 'selected' : '' }}>LOOM</option>
        </select>
    </div>
</form>
