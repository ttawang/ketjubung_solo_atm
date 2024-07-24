<div class="col-md-12">
    @if ($data->validated_at)
        <h5 class="text-right">Tanggal Validasi :
            &nbsp;<em>{{ tglIndoFull($data->validated_at) }}</em><i class="icon md-check-circle ml-2 text-success"></i>
        </h5>
    @endif
    <div class="row text-center">
        <div class="col-md-3">
            <h3><span class="badge badge-outline badge-primary">No. Beam : {{ $no_beam }}</span></h3>
        </div>
        <div class="col-md-3">
            <h3><span class="badge badge-outline badge-primary">No. KIKW : {{ $no_kikw }}</span></h3>
        </div>
        <div class="col-md-3">
            <h3><span class="badge badge-outline badge-primary">No. Loom : {{ $no_loom }}</span></h3>
        </div>
        <div class="col-md-3">
            <h3><span class="badge badge-outline badge-primary">Potongan : {{ $potongan }}</span></h3>
        </div>
    </div>
</div>
<br>
<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-default btn-sm waves-effect waves-classic" onclick="parent($(this));">
            <i class="icon md-arrow-left mr-2"></i> Kembali
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" cellspacing="0" id="table-detail">
            <thead>
                <tr>
                    <th width="30px">No.</th>
                    <th>Tanggal</th>
                    <th>Group</th>
                    <th>Barang</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Potongan</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<br>
<form id="form" action="" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="mode" id="mode" value="detail">
    <input type="hidden" name="id_inspecting_grey" id="id_inspecting_grey" value="{{ $id_inspecting_grey }}">

    @for ($group = 1; $group <= 3; $group++)
        @php
            if ($group == 1) {
                $data_kualitas = $group_1;
            } elseif ($group == 2) {
                $data_kualitas = $group_2;
            } else {
                $data_kualitas = $group_3;
            }
        @endphp
        <h5 class="text-center">Group {{ $group }}</h5>
        <hr>
        <div class="row">
            <div class="col-md-1">
                <h5>Grade B :</h5>
            </div>
            <div class="col-md-11">
                <div class="form-row">
                    @php
                        $b = 1;
                    @endphp
                    @foreach ($kualitas_b as $i)
                        @php
                            $obj = 'jml_kualitas_' . $b;
                        @endphp
                        <div class="input-group input-group-sm col-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $i->kode }}</span>
                            </div>
                            <input type="number" class="form-control"
                                id="group_{{ $group }}_kualitas_{{ $b }}"
                                name="group_{{ $group }}_kualitas_{{ $b }}"
                                placeholder="{{ $i->kode }}" value={{ $data_kualitas->$obj }}>
                        </div>
                        @php
                            $b++;
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-1">
                <h5>Grade C :</h5>
            </div>
            <div class="col-md-11">
                <div class="form-row">
                    @php
                        $c = $b;
                    @endphp
                    @foreach ($kualitas_c as $i)
                        @php
                            $obj = 'jml_kualitas_' . $c;
                            if ($c == 33) {
                                $c = $c + 2;
                            }
                        @endphp
                        <div class="input-group input-group-sm col-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $i->kode }}</span>
                            </div>
                            <input type="number" class="form-control"
                                id="group_{{ $group }}_kualitas_{{ $c }}"
                                name="group_{{ $group }}_kualitas_{{ $c }}"
                                placeholder="{{ $i->kode }}" value={{ $data_kualitas->$obj }}>
                        </div>
                        @php
                            $c++;
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>
        <br>
    @endfor
</form>
@if (Auth::user()->roles_name !== 'validator')
    @if (!$data->validated_at)
        <button type="button" class="btn btn-primary" onclick="simpanKualitas($(this))"
            data-id_inspecting_grey="{{ $data->id }}">Simpan</button>
    @endif
@endif

<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            dropdownParent: $('#modal-kelola'),
            width: '100%'
        });
    });

    function table(id) {
        table = $('#table-detail').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [],
            ajax: `{{ url('production/inspect_grey_2/table/${id}') }}`,
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
                    data: 'group',
                    name: 'group'
                },
                {
                    data: 'barang',
                    name: 'barang'
                },
                {
                    data: 'motif',
                    name: 'motif'
                },
                {
                    data: 'warna',
                    name: 'warna'
                },
                {
                    data: 'potongan',
                    name: 'potongan'
                },
                {
                    data: 'a',
                    name: 'a'
                },
                {
                    data: 'b',
                    name: 'b'
                },
                {
                    data: 'c',
                    name: 'c'
                }
            ]
        });
    }
</script>
