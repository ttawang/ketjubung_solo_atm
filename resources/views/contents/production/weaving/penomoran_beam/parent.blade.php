<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-parent">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>No. Beam</th>
                    <th>No. KIKW</th>
                    <th>Loom</th>
                    <th>Barang</th>
                    <th>Warna</th>
                    <th>Motif</th>
                    <th>Volume (Beam)</th>
                    <th>Volume (Pcs)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label>No. KIKW</label>
                        <input type="text" class="form-control" name="no_kikw" id="no_kikw">
                    </div>
                    <div class="form-group">
                        <label>Loom</label>
                        <select class="form-control select2" name="id_mesin" id="mesin" required>
                            <option value="0">-- pilih loom --</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure" onclick="closeModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#mesin').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `penomoran_beam/get-mesin`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });

    function table() {
        table = $('#table-parent').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `penomoran_beam/table`,
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
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function edit(this_) {
        $('.modal-title').text('Penomoran');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        $('#id').val(id);
        $.get(`penomoran_beam/get-data/${id}`, function(data) {
            if (data.rel_nomor_kikw != null) {
                $('#no_kikw').val(data.rel_nomor_kikw.name);
            }
            if (data.rel_mesin_history_latest) {
                $(`#mesin`).select2("trigger", "select", {
                    data: {
                        id: data.rel_mesin_history_latest.id_mesin,
                        text: data.rel_mesin_history_latest.rel_mesin.name
                    }
                });
            }
        });
    }
    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#no_kikw').val('');
        $('#mesin').val(0).trigger('change');
    }
</script>
