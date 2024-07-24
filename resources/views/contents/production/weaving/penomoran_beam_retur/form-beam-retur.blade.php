<div class="example-wrap">
    <h4 class="example-title"><i class="icon md-assignment-o"></i> Beam (Nomor Lama / Retur)</h4>
    <div class="example">
        <div class="form-group form-material row">
            <input type="hidden" id="select_gudang" value="99999">
            <label class="col-md-3 col-form-label">Gudang: </label>
            <div class="col-md-9">
                <select name="input[id_gudang]" id="select_gudang" data-dropdown-parent="false"
                    onchange="changeGudang($(this))" data-placeholder="-- Pilih Gudang --"
                    data-model="LogStokPenerimaan" data-route="{{ route('helper.getGudang') }}"
                    class="form-control select2"></select>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Beam Retur Songket/Lusi: </label>
            <div class="col-md-9">
                <input type="hidden" name="input[id_barang]">
                <input type="hidden" name="input[id_satuan_1]">
                <input type="hidden" name="input[id_satuan_2]">
                <select id="select_barang" data-dropdown-parent="false" data-filter-code="BBTLR,BBTSR"
                    onchange="changeBeam($(this))" data-placeholder="-- Pilih Beam --"
                    data-route="{{ route('helper.getBarangWithStok') }}" class="form-control select2"></select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Nomor Beam: </label>
                    <div class="col-md-9">
                        <input type="hidden" name="input[id_beam]">
                        <input type="text" class="form-control" id="no_beam" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Nomor Kikw / Kiks: </label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="no_kikw" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Warna: </label>
                    <div class="col-md-9">
                        <input type="hidden" name="input[id_warna]">
                        <input type="text" class="form-control" id="nama_warna" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Motif: </label>
                    <div class="col-md-9">
                        <input type="hidden" name="input[id_motif]">
                        <input type="text" class="form-control" id="nama_motif" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Mesin: </label>
            <div class="col-md-9">
                <input type="hidden" name="input[id_mesin]">
                <input type="text" class="form-control" id="nama_mesin" readonly>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Tipe Pra Tenun: </label>
            <div class="col-md-9">
                <input type="text" name="input[tipe_pra_tenun]" class="form-control" id="tipe_pra_tenun_txt"
                    readonly>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Sizing: </label>
            <div class="col-md-9">
                <input type="text" name="input[is_sizing]" class="form-control" id="sizing_txt" readonly>
            </div>
        </div>
        <div class="form-group form-material row">
            <label class="col-md-3 col-form-label">Volume (pcs): </label>
            <div class="col-md-9">
                <input type="hidden" name="input[volume_1]" class="volume_1">
                <input type="text" name="input[volume_2]" class="form-control volume_2" readonly>
            </div>
        </div>
    </div>
</div>
