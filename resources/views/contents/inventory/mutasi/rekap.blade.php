<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="rekap">
            <div class="form-row">
                <div class="form-group col-md-12" id="div-proses">
                    <label>Proses</label>
                    <select id="proses" style="width: 100%;" class="form-select select2" name="proses">
                        <option value="persediaan_benang_grey">Persediaan Benang Grey</option>
                        <option value="persediaan_benang_warna">Persediaan Benang Warna</option>
                        <option value="persediaan_benang_warna_per_jenis">Persediaan Benang Warna per-Jenis</option>
                        <option value="pengiriman_barang">Pengiriman Barang</option>
                        <option value="pemotongan_sarung">Pemotongan Sarung</option>
                        <option value="inspecting_grey">Inspecting Grey</option>
                        <option value="cacat_jasa_luar_finishing">Cacat Jasa Luar Finishing</option>
                        <option value="produksi_dyeing">Produksi Dyeing</option>
                    </select>
                </div>
                <div class="form-group col-md-6" id="div-tgl-awal">
                    <label>Tanggal Awal</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                        name="tgl_awal" id="tgl_awal" required />
                    </select>
                </div>
                <div class="form-group col-md-6" id="div-tgl-akhir">
                    <label>Tanggal Akhir</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                        name="tgl_akhir" id="tgl_akhir" required />
                    </select>
                </div>
                <div class="form-group col-md-6" id="div-tipe-pengiriman">
                    <label>Tipe Pengiriman</label>
                    <select id="tipe_pengiriman" style="width: 100%;" class="form-select select2"
                        name="tipe_pengiriman">
                        <option value="">-- semua pengiriman --</option>
                        @foreach ($tipe_pengiriman as $i)
                            <option value="{{ $i->id }}">{{ $i->initial }} - {{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6" id="div-barang-benang">
                    <label>Barang</label>
                    <select id="barang_benang" style="width: 100%;" class="form-select select2" name="barang_benang">
                        @foreach ($barang_benang as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6" id="div-mesin-dyeing">
                    <label>Mesin</label>
                    <select id="mesin_dyeing" style="width: 100%;" class="form-select select2" name="mesin_dyeing">
                        @foreach ($mesin_dyeing as $i)
                            <option value="{{ $i->id }}">{{ $i->name }} - {{ $i->tipe }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-5">
                    <br>
                    <div class="btn-group" aria-label="Button group with nested dropdown" role="group">
                        <button type="button" class="btn btn-sm btn-primary waves-effect waves-classic"
                            onclick="lihat($(this))">Lihat</button>
                        <button type="button" class="btn btn-sm btn-warning waves-effect waves-classic"
                            onclick="cetak($(this))">Cetak</button>
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
        $('#div-tipe-pengiriman').hide();
        $('#div-barang-benang').hide();
        $('#div-mesin-dyeing').hide();
    });

    function lihat(this_) {
        $.ajax({
            url: `mutasi/table`,
            type: "get",
            dataType: "html",
            data: {
                mode: $('#mode').val(),
                proses: $('#proses').val(),
                tgl_awal: $('#tgl_awal').val(),
                tgl_akhir: $('#tgl_akhir').val(),
                tipe_pengiriman: $('#tipe_pengiriman').val(),
                barang_benang: $('#barang_benang').val(),
                mesin_dyeing: $('#mesin_dyeing').val(),
            },
            success: function(html) {
                $('#div-table').html(html);
            },
            error: function() {
                alert("Error");
            },
        });
    }

    function cetak(this_) {
        var uri = `mutasi/table`;

        var data = {
            mode: $('#mode').val(),
            proses: $('#proses').val(),
            tgl_awal: $('#tgl_awal').val(),
            tgl_akhir: $('#tgl_akhir').val(),
            tipe_pengiriman: $('#tipe_pengiriman').val(),
            barang_benang: $('#barang_benang').val(),
            mesin_dyeing: $('#mesin_dyeing').val(),
            cetak: true
        };

        window.open(uri + '?' + new URLSearchParams(data), '_blank');
    }
    $('#proses').change(function() {
        var selectedValue = $(this).val();
        var prosesTanpaTanggal = ['pemotongan_sarung', 'cacat_jasa_luar_finishing'];
        if ($.inArray(selectedValue, prosesTanpaTanggal) !== -1) {
            $('#div-tgl-awal').hide();
            $('#div-tgl-akhir').hide();
        } else {
            $('#div-tgl-awal').show();
            $('#div-tgl-akhir').show();
        }
        if (selectedValue != 'pengiriman_barang') {
            $('#div-tipe-pengiriman').hide();
        } else {
            $('#div-tipe-pengiriman').show();
        }
        if (selectedValue != 'persediaan_benang_warna_per_jenis') {
            $('#div-barang-benang').hide();
        } else {
            $('#div-barang-benang').show();
        }
        if (selectedValue != 'produksi_dyeing') {
            $('#div-mesin-dyeing').hide();
        } else {
            $('#div-mesin-dyeing').show();
        }
    });
</script>
