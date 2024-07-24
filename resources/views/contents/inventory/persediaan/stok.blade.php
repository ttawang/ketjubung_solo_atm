<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="stok">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Jenis</label>
                    <select id="jenis" style="width: 100%;" class="form-select select2" name="jenis">
                        <option value="semua">-- semua jenis --</option>
                        @foreach ($jenis as $i)
                            <option value="{{ $i->code }}">{{ $i->alias }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Barang</label>
                    <select id="barang" style="width: 100%;" class="form-select select2" name="barang">
                        <option value="semua">-- semua barang --</option>
                        {{-- <option value="tanpa">-- tanpa barang --</option> --}}
                        @foreach ($barang as $i)
                            <option value="{{ $i->id }}">{{ $i->name }} - {{ $i->relTipe->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Warna</label>
                    <select id="warna" style="width: 100%;" class="form-select select2" name="warna">
                        <option value="semua">-- semua warna --</option>
                        {{-- {{-- <option value="tanpa">-- tanpa warna --</option> --}} --}}
                        @foreach ($warna as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Motif</label>
                    <select id="motif" style="width: 100%;" class="form-select select2" name="motif">
                        <option value="semua">-- semua motif --</option>
                        {{-- {{-- <option value="tanpa">-- tanpa motif --</option> --}} --}}
                        @foreach ($motif as $i)
                            <option value="{{ $i->id }}">{{ $i->alias }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Grade</label>
                    <select id="grade" style="width: 100%;" class="form-select select2" name="grade">
                        <option value="semua">-- semua grade --</option>
                        {{-- {{-- <option value="tanpa">-- tanpa grade --</option> --}} --}}
                        @foreach ($grade as $i)
                            <option value="{{ $i->id }}">{{ $i->grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Kualitas</label>
                    <select id="kualitas" style="width: 100%;" class="form-select select2" name="kualitas">
                        <option value="semua">-- semua kualitas --</option>
                        {{-- {{-- <option value="tanpa">-- tanpa kualitas --</option> --}} --}}
                        @foreach ($kualitas as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Gudang</label>
                    <select id="gudang" style="width: 100%;" class="form-select select2" name="gudang">
                        <option value="semua">-- semua gudang --</option>
                        {{-- {{-- <option value="tanpa">-- tanpa gudang --</option> --}} --}}
                        @foreach ($gudang as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
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
                    <th rowspan="2" style="text-align: center">Jenis</th>
                    <th rowspan="2" style="text-align: center">Barang</th>
                    <th rowspan="2" style="text-align: center">KIKW</th>
                    <th rowspan="2" style="text-align: center">KIKS</th>
                    <th rowspan="2" style="text-align: center">Tanggal Potong</th>
                    <th rowspan="2" style="text-align: center">Warna</th>
                    <th rowspan="2" style="text-align: center">Motif</th>
                    <th rowspan="2" style="text-align: center">Mesin</th>
                    <th rowspan="2" style="text-align: center">Grade</th>
                    <th rowspan="2" style="text-align: center">Kualitas</th>
                    <th rowspan="2" style="text-align: center">Gudang</th>
                    <th colspan="6" style="text-align: center">Kuantitas</th>
                </tr>
                <tr>
                    <th style="text-align: center">Cones</th>
                    <th style="text-align: center">Kg</th>
                    <th style="text-align: center">Beam</th>
                    <th style="text-align: center">Pcs</th>
                    <th style="text-align: center">Gram</th>
                    <th style="text-align: center">Meter</th>
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
            searching: false,
            order: [],
            ajax: {
                url: 'persediaan/table',
                type: 'GET',
                data: function(d) {
                    d.mode = $('#mode').val();
                    d.barang = $('#barang').val();
                    d.jenis = $('#jenis').val();
                    d.warna = $('#warna').val();
                    d.motif = $('#motif').val();
                    d.gudang = $('#gudang').val();
                    d.grade = $('#grade').val();
                    d.kualitas = $('#kualitas').val();
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
                    data: 'nama_proses',
                    name: 'nama_proses'
                },
                {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw'
                },
                {
                    data: 'no_kiks',
                    name: 'no_kiks'
                },
                {
                    data: 'tanggal_potong',
                    name: 'tanggal_potong'
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
                    data: 'nama_mesin',
                    name: 'nama_mesin'
                },
                {
                    data: 'nama_grade',
                    name: 'nama_grade'
                },
                {
                    data: 'nama_kualitas',
                    name: 'nama_kualitas'
                },
                {
                    data: 'nama_gudang',
                    name: 'nama_gudang'
                },
                {
                    data: 'stok_cones',
                    name: 'stok_cones'
                },
                {
                    data: 'stok_kg',
                    name: 'stok_kg'
                },
                {
                    data: 'stok_beam',
                    name: 'stok_beam'
                },
                {
                    data: 'stok_pcs',
                    name: 'stok_pcs'
                },
                {
                    data: 'stok_gram',
                    name: 'stok_gram'
                },
                {
                    data: 'stok_meter',
                    name: 'stok_meter'
                },
            ],
            /* createdRow: function(row, data, dataIndex) {
                var ball = data.stok_ball;
                var cones = data.stok_cones;
                var kg = data.stok_kg;
                var beam = data.stok_beam;
                var pcs = data.stok_pcs;
                var palet = data.stok_palet;
                var gram = data.stok_gram;
                var meter = data.stok_meter;

                if (ball === 0 && ball === 0 && cones === 0 && kg === 0 && beam === 0 && pcs === 0 && palet === 0 && gram === 0 && meter === 0) {
                    $(row).hide();
                }
            } */
        });
    }
</script>
