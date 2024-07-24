<div class="example-wrap">
    <h4 class="example-title"><i class="icon md-assignment-o"></i> Beam (Nomor Baru)</h4>
    <div class="example">
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Beam Baru Songket/Lusi: </label>
            <div class="col-md-9">
                <input type="hidden" name="inputBaru[id_barang]">
                <select id="select_barang_baru" name="inputBaru[id_barang]" data-dropdown-parent="false"
                    data-placeholder="-- Pilih Beam --" data-route="{{ route('helper.getBarang') }}"
                    data-filter-tipe="3,4" class="form-control select2" disabled></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Warna: </label>
            <div class="col-md-9">
                <select id="select_warna" name="inputBaru[id_warna]" data-dropdown-parent="false"
                    data-placeholder="-- Pilih Warna --" data-route="{{ route('helper.getWarna') }}"
                    class="form-control select2" disabled></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Motif: </label>
            <div class="col-md-9">
                <select id="select_motif" name="inputBaru[id_motif]" data-dropdown-parent="false"
                    data-placeholder="-- Pilih Motif --" data-route="{{ route('helper.getMotif') }}"
                    class="form-control select2"></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Nomor Beam: </label>
            <div class="col-md-9">
                <input type="hidden" name="tipe_beam">
                <select id="select_nomor_beam" name="id_nomor_beam" data-dropdown-parent="false"
                    data-placeholder="-- Pilih Nomor Beam --" data-route="{{ route('helper.getNomorBeam') }}"
                    class="form-control select2" disabled></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Nomor Kikw / Kiks: </label>
            <div class="col-md-9">
                <input type="text" class="form-control"
                    value="{{ !empty($data) ? $data->throughNomorKikw()->value('name') : '' }}" name="nomor_kikw"
                    id="no_kikw_baru" data-no-kikw="{{ !empty($data) ? $data->throughNomorKikw()->value('name') : '' }}"
                    onkeyup="onCheckNoKikw($(this));" autocomplete="false">
                <span id="txtAlertNoKikw"></span>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Mesin: </label>
            <div class="col-md-9">
                <select id="select_mesin" name="inputBaru[id_mesin]" data-dropdown-parent="false" data-jenis="LOOM"
                    data-placeholder="-- Pilih Mesin --" data-route="{{ route('helper.getMesin') }}"
                    class="form-control select2"></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Tipe Pra Tenun: </label>
            <div class="col-md-9">
                <select id="select_tipe_pra_tenun" name="inputBaru[tipe_pra_tenun]"
                    data-dropdown-parent="false" data-placeholder="-- Pilih Tipe Pra Tenun --"
                    class="form-control select2">
                    <option value=""></option>
                    <option value="CUCUK">CUCUK</option>
                    <option value="TYEING">TYEING</option>
                </select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Volume (pcs): </label>
            <div class="col-md-9">
                <input type="hidden" name="inputBaru[volume_1]" class="volume_1">
                <input type="text" name="inputBaru[volume_2]" class="form-control volume_2" readonly>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Catatan</label>
            <div class="col-md-9">
                <textarea name="catatan" id="catatan" class="form-control" cols="30" rows="10">{{ $data->catatan ?? '' }}</textarea>
            </div>
        </div>
    </div>
</div>
