<form action="{{ isset($id) ? route('database.tipe_pengiriman.update', $id) : route('database.tipe_pengiriman.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="name" required>
    </div>
    <div class="form-group">
        <label>Title</label>
        <input type="text" value="{{ $data->title ?? '' }}" class="form-control"
            placeholder="Bukti Penyerahan Barang Baku" name="title" required>
    </div>
    <div class="form-group">
        <label>Initial</label>
        <input type="text" value="{{ $data->title ?? '' }}" class="form-control" placeholder="BPBB" name="initial"
            required>
    </div>
    {{-- <div class="form-group">
        <label>Gudang Asal</label>
        <select name="id_gudang_asal" id="select_gudang_asal" class="form-control select2" data-placeholder="-- Pilih Gudang Asal --"
            data-route="{{ route('helper.getGudang') }}"></select>
    </div>
    <div class="form-group">
        <label>Gudang Tujuan</label>
        <select name="id_gudang_tujuan" id="select_gudang_tujuan" class="form-control select2" data-placeholder="-- Pilih Gudang Tujuan --"
            data-route="{{ route('helper.getGudang') }}"></select>
    </div> --}}
</form>
