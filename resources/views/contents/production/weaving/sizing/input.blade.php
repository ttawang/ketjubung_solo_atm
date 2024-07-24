<div class="tab-pane active" id="tabInput" role="tabpanel">
    <div class="form-group row">
        <div class="col-md-12">
            @if (Auth::user()->roles_name !== 'validator')
                @if (!$data->validated_at)
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this))">
                        <i class="icon md-plus mr-2"></i> Tambah Barang</button>
                @endif
            @endif
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-detail">
                <thead>
                    <tr>
                        <th width="30px">No.</th>
                        <th>Tanggal</th>
                        <th>No. KIKW</th>
                        <th>No. Beam</th>
                        <th>Loom</th>
                        <th>Barang</th>
                        <th>Warna</th>
                        <th>Motif</th>
                        <th>Volume (Beam)</th>
                        <th>Volume (Pcs)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_sizing" id="id_sizing">
                    <input type="hidden" name="id_log_stok_penerimaan" id="id_log_stok_penerimaan">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" name="tipe" id="tipe" value="input">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Beam</label>
                        <select class="form-control select2" name="id_beam" id="id_beam" required>
                            <option value="0">-- pilih beam --</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#id_beam').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `sizing/get-barang/${tipe}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(data) {
                            return {
                                id: data.id,
                                text: `${data.rel_mesin_history_latest?.rel_mesin?.name} | ${data.rel_nomor_kikw.name} | ${data.rel_nomor_beam.name} | ${data.rel_log_stok_penerimaan_b_l?.rel_warna?.alias} | ${data.rel_log_stok_penerimaan_b_l?.rel_motif?.alias}`
                            };
                        }),
                        pagination: {
                            more: data.next_page_url ? true : false
                        }
                    };
                },
                error: () => {},
                cache: true
            }
        });
    });

    function tableDetail(id, tipe) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: false,
            order: [],
            ajax: `sizing/table/${mode}/${id}/${tipe}`,
            lengthMenu: [15, 25, 50, 100],
            processing: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'no_kikw',
                    name: 'no_kikw'
                },
                {
                    data: 'no_beam',
                    name: 'no_beam'
                },
                {
                    data: 'mesin',
                    name: 'mesin'
                },
                {
                    data: 'barang',
                    name: 'barang'
                },
                {
                    data: 'warna',
                    name: 'warna'
                },
                {
                    data: 'motif',
                    name: 'motif'
                },
                {
                    data: 'volume_1',
                    name: 'volume_1'
                },
                {
                    data: 'volume_2',
                    name: 'volume_2'
                },
                {
                    data: null,
                    name: 'volume_2',
                    render: (data) => {
                        let jumlahTerima = data.jumlah_terima || 0
                        if (jumlahTerima > 0) {
                            return `<span class="badge badge-outline badge-primary">Sudah Diterima</span>`;
                        } else {
                            return `<span class="badge badge-outline badge-warning">Belum Diterima</span>`;
                        }
                    }
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
        $('#id_sizing').val(id);
    }

    $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#id_sizing').val(id);
        $('#id_beam').val(0).trigger('change');
    });
</script>
