<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2"
            onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-parent">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>No. SPK</th>
                    <th>Vendor</th>
                    <th width="100px">Total Pcs</th>
                    {{-- <th width="50px">Hilang</th> --}}
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-simple modal-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Form</h4>
            </div>
            <div class="modal-body" style="padding-bottom: 20px;">
                <form class="form-horizontal" id="form" action="" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="mode" id="mode" value="parent">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange=""
                            name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>No. SPK</label>
                        <input type="text" class="form-control" name="nomor" id="nomor">
                    </div>
                    <div class="form-group">
                        <label>Vendor</label>
                        <select class="form-control select2" name="id_supplier" id="supplier" required>
                            <option value="0">-- pilih vendor --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" cols="30" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close" class="btn btn-default btn-pure"
                    onclick="closeModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan($(this))">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
        $('#supplier').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%',
            ajax: {
                url: `jahit_p2/get-supplier`,
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
    /* $('#modal-kelola').on('hide.bs.modal', function(e) {
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#nomor').val('');
        $('#supplier').val(0).trigger('change');
        $('#catatan').val('');
    }); */
    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#nomor').val('');
        $('#supplier').val(0).trigger('change');
        $('#catatan').val('');
    }

    function tambah(this_) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function edit(this_) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = this_.data('id');
        var data = {
            mode: mode,
            id: id
        };
        $.ajax({
            url: `jahit_p2/get-data`,
            data: data,
            type: 'GET',
            success: function(data) {
                $('#id').val(data.id);
                $('#tanggal').val(data.tanggal);
                $(`#supplier`).select2("trigger", "select", {
                    data: {
                        id: data.id_supplier,
                        text: data.nama_supplier
                    }
                });
                $('#nomor').val(data.nomor);
                $('#catatan').val(data.catatan);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    function tableParent() {
        table = $('#table-parent').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `jahit_p2/table/${mode}`,
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
                    data: 'nomor',
                    name: 'nomor'
                },
                {
                    data: 'vendor',
                    name: 'vendor'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'catatan',
                    name: 'catatan'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }
</script>
