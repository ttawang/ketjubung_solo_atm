<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="produksi">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tanggal Awal</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tgl_awal" id="tgl_awal" required />
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Tanggal Akhir</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tgl_akhir" id="tgl_akhir" required />
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Produksi</label>
                    <select id="produksi" style="width: 100%;" class="form-select select2" name="produksi">
                        <option value="semua">-- semua produksi --</option>
                        <option value="1">Pengiriman Barang</option>
                        <option value="2">Softcone</option>
                        <option value="3">Dye & Oven</option>
                        <option value="4">Overcone</option>
                        <option value="5">Warping</option>
                        <option value="6">Pakan</option>
                        <option value="7">Leno</option>
                        <option value="8">Sizing</option>
                        <option value="9">Cucuk</option>
                        <option value="10">Tyeing</option>
                        <option value="11">Tenun</option>
                        <option value="12">Inspecting Grey</option>
                        <option value="13">Dudulan</option>
                        <option value="14">Inspect Dudulan</option>
                        <option value="15">Jahit Sambung</option>
                        <option value="16">P1</option>
                        <option value="17">Fininshing Cabut</option>
                        <option value="18">Jigger & Cuci Sarung</option>
                        <option value="19">Drying</option>
                        <option value="20">P2</option>
                        <option value="21">Jahit P2</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-5">
                    <br>
                    <div class="btn-group" aria-label="Button group with nested dropdown" role="group">
                        <button type="button" class="btn btn-sm btn-primary waves-effect waves-classic" onclick="lihat($(this))">Lihat</button>
                        {{-- <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle waves-effect waves-classic" id="lihat_dropdown" data-toggle="dropdown" aria-expanded="false">
                                <i class="icon md-print mr-2"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="lihat_dropdown" role="menu">
                                <a class="dropdown-item" id="btn_lihat_pdf" role="menuitem">PDF</a>
                                <a class="dropdown-item" id="btn_lihat_excel" role="menuitem">Excel</a>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </form>
        <br>
        <div id="div-table">

        </div>
    </div>
</div>
<script>
    $(function() {
        $('.select2').select2();
        lihat();
    });

    function lihat(this_) {
        $.ajax({
            url: `mutasi/table`,
            type: "get",
            dataType: "html",
            data: {
                mode: $('#mode').val(),
                tgl_awal: $('#tgl_awal').val(),
                tgl_akhir: $('#tgl_akhir').val(),
                proses: $('#produksi').val(),
            },
            success: function(html) {
                $('#div-table').html(html);
            },
            error: function() {
                alert("Error");
            },
        });
    }
</script>
