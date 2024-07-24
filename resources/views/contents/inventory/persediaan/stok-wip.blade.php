<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="jasa_luar">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Proses</label>
                    <select id="proses" style="width: 100%;" class="form-select select2" name="proses">
                        <option value="semua">-- semua proses --</option>
                        <option value="1">Doubling</option>
                        <option value="2">Dyeing</option>
                        <option value="3">Dudulan</option>
                        <option value="4">P1</option>
                        <option value="5">Finishing Cabut</option>
                        <option value="6">P2</option>
                        <option value="7">Jahit P2</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Vendor</label>
                    <select id="supplier" style="width: 100%;" class="form-select select2" name="supplier">
                        <option value="semua">-- semua vendor --</option>
                        @foreach ($supplier as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Barang</label>
                    <select id="barang" style="width: 100%;" class="form-select select2" name="barang">
                        <option value="semua">-- semua barang --</option>
                        @foreach ($barang as $i)
                            <option value="{{ $i->id }}">{{ $i->name }} | {{ $i->relTipe->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Warna</label>
                    <select id="warna" style="width: 100%;" class="form-select select2" name="warna">
                        <option value="semua">-- semua warna --</option>
                        @foreach ($warna as $i)
                            <option value="{{ $i->id }}">{{ $i->alias }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Motif</label>
                    <select id="motif" style="width: 100%;" class="form-select select2" name="motif">
                        <option value="semua">-- semua motif --</option>
                        @foreach ($motif as $i)
                            <option value="{{ $i->id }}">{{ $i->alias }}</option>
                        @endforeach
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
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
            <thead>
                <tr>
                    <th rowspan="2" style="text-align: center">No</th>
                    <th rowspan="2" style="text-align: center">Proses</th>
                    <th rowspan="2" style="text-align: center">No. SPK</th>
                    <th rowspan="2" style="text-align: center">Vendor</th>
                    <th rowspan="2" style="text-align: center">Barang</th>
                    <th rowspan="2" style="text-align: center">Warna</th>
                    <th rowspan="2" style="text-align: center">Motif</th>
                    <th rowspan="2" style="text-align: center">Gudang</th>
                    <th colspan="4" style="text-align: center">Kuantitas</th>
                </tr>
                <tr>
                    <th style="text-align: center">Kirim</th>
                    <th style="text-align: center">Terima</th>
                    <th style="text-align: center">Hilang</th>
                    <th style="text-align: center">Sisa</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script>
    $(function() {
        $('.select2').select2();
    });

    function lihat(this_) {
        table.ajax.reload();
    }

    function table() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            // searching: false,
            order: [],
            ajax: {
                url: 'persediaan/table',
                type: 'GET',
                data: function(d) {
                    d.mode = $('#mode').val();
                    d.proses = $('#proses').val();
                    d.barang = $('#barang').val();
                    d.warna = $('#warna').val();
                    d.motif = $('#motif').val();
                    d.gudang = $('#gudang').val();
                    d.supplier = $('#supplier').val();
                }
            },
            lengthMenu: [15, 25, 50, 100],
            processing: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'proses',
                    name: 'proses'
                },
                {
                    data: 'nomor',
                    name: 'nomor'
                },
                {
                    data: 'nama_supplier',
                    name: 'nama_supplier'
                },
                {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'nama_warna',
                    name: 'nama_warna'
                },
                {
                    data: 'nama_motif',
                    name: 'nama_motif'
                },
                {
                    data: 'nama_gudang',
                    name: 'nama_gudang'
                },
                {
                    data: 'kirim_1',
                    name: 'kirim_1',
                    searchable: false,
                },
                {
                    data: 'terima_1',
                    name: 'terima_1',
                    searchable: false,
                },
                {
                    data: 'hilang_1',
                    name: 'hilang_1',
                    searchable: false,
                },
                {
                    data: 'sisa_1',
                    name: 'sisa_1',
                    searchable: false,
                },
            ],
        });
    }
</script>
