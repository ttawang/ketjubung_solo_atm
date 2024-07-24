<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead style="text-align: center;">
        <tr>
            <th rowspan="2">BARANG</th>
            <th rowspan="2">MOTIF</th>
            <th rowspan="2">WARNA</th>
            <th colspan="3">P1</th>
            <th colspan="3">FC</th>
            <th colspan="3">P2</th>
        </tr>
        <tr>
            <th>VENDOR</th>
            <th>PCS</th>
            <th>KUALITAS</th>
            <th>VENDOR</th>
            <th>PCS</th>
            <th>KUALITAS</th>
            <th>VENDOR</th>
            <th>PCS</th>
            <th>KUALITAS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i)
            <tr>
                <td>{{ $i->nama_barang }}</td>
                <td>{{ $i->nama_motif }}</td>
                <td>{{ $i->nama_warna }}</td>
                <td>{{ $i->p1_supplier }}</td>
                <td>{{ $i->p1_volume_1 }}</td>
                <td>{{ $i->p1_kualitas }}</td>
                <td>{{ $i->fc_supplier }}</td>
                <td>{{ $i->fc_volume_1 }}</td>
                <td>{{ $i->fc_kualitas }}</td>
                <td>{{ $i->p2_supplier }}</td>
                <td>{{ $i->p2_volume_1 }}</td>
                <td>{{ $i->p2_kualitas }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#table').DataTable({
            ordering: false,
            "pageLength": 50
        });
    });
</script>
