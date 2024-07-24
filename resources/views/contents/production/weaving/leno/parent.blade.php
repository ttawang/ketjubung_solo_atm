<div class="form-group row">
    <div class="col-md-12">
        @if (Auth::user()->roles_name !== 'validator')
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic float-left mr-2" onclick="tambah($(this))"><i class="icon md-plus mr-2"></i> Tambah</button>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-parent">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Nomor</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade modal-fade-in-scale-up" id="modal-kelola" aria-hidden="true" role="dialog" tabindex="-1" data-backdrop="static" data-keyboard="false">
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
                    <input type="hidden" name="mode" id="mode" value="parent">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </div>
                    <div class="form-group">
                        <label>Nomor</label>
                        <input type="text" class="form-control" name="nomor" id="nomor">
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" cols="30" rows="10"></textarea>
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
    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#tanggal').val(`{{ date('Y-m-d') }}`);
        $('#nomor').val('');
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
        $.get(`leno/get-data/${id}/${mode}`, function(data) {
            $('#id').val(data.id);
            $('#tanggal').val(data.tanggal);
            $('#nomor').val(data.nomor);
            $('#catatan').val(data.catatan);
        });
    }

    function tableParent() {
        table = $('#table-parent').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `leno/table/${mode}`,
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
