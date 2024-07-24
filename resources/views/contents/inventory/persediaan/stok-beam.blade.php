<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="beam">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Beam</label>
                    <select id="beam" style="width: 100%;" class="form-select select2" name="beam">
                        <option value="semua">-- semua beam --</option>
                        @foreach ($beam as $i)
                            <option value="{{ $i->id }}">{{ $i->relBeam->relNomorBeam->name }} | {{ $i->relBeam->relNomorKikw->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Mesin</label>
                    <select id="mesin" style="width: 100%;" class="form-select select2" name="mesin">
                        <option value="semua">-- semua mesin --</option>
                        @foreach ($mesin as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Barang</label>
                    <select id="barang" style="width: 100%;" class="form-select select2" name="barang">
                        <option value="semua">-- semua barang --</option>
                        @foreach ($barang as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
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
                    <th rowspan="2" style="text-align: center">No. Beam</th>
                    <th rowspan="2" style="text-align: center">No. KIKW</th>
                    <th rowspan="2" style="text-align: center">Loom</th>
                    <th rowspan="2" style="text-align: center">Barang</th>
                    <th rowspan="2" style="text-align: center">Warna</th>
                    <th rowspan="2" style="text-align: center">Motif</th>
                    <th colspan="3" style="text-align: center">Pcs</th>
                </tr>
                <tr>
                    <th colspan="" style="text-align: center">Stok</th>
                    <th colspan="" style="text-align: center">Potong</th>
                    <th colspan="" style="text-align: center">Sisa</th>
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
            order: [],
            ajax: {
                url: 'persediaan/table',
                type: 'GET',
                data: function(d) {
                    d.mode = $('#mode').val();
                    d.beam = $('#beam').val();
                    d.mesin = $('#mesin').val();
                    d.barang = $('#barang').val();
                    d.warna = $('#warna').val();
                    d.motif = $('#motif').val();
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
                    data: 'no_beam',
                    name: 'no_beam'
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw'
                },
                {
                    data: 'nama_mesin',
                    name: 'nama_mesin'
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
                    data: 'jml',
                    name: 'jml'
                },
                {
                    data: 'jml_potong',
                    name: 'jml_potong'
                },
                {
                    data: 'jml_sisa',
                    name: 'jml_sisa'
                },
            ],
        });
    }
</script>
