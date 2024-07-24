<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead>
        <tr>
            <th rowspan="1" style="text-align: center">NO.</th>
            <th rowspan="1" style="text-align: center">NO. BEAM</th>
            <th rowspan="1" style="text-align: center">NO. KIKW</th>
            <th rowspan="1" style="text-align: center">TANGGAL</th>
            <th rowspan="1" style="text-align: center">NO. MC</th>
            <th rowspan="1" style="text-align: center">MOTIF</th>
            <th rowspan="1" style="text-align: center">WARNA LUSI</th>
            <th rowspan="1" style="text-align: center">GROUP</th>
            <th rowspan="1" style="text-align: center">JML. POT</th>
            <th rowspan="1" style="text-align: center">SISA BEAM</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $total_potong = 0;
            $total_sisa = 0;
        @endphp
        @foreach ($data as $i)
            <tr>
                <td>{{ $no }}</td>
                <td>{{ $i->relBeam->relNomorBeam->name }}</td>
                <td>{{ $i->relBeam->no_kikw }}</td>
                <td>{{ $i->tanggal }}</td>
                <td>{{ $i->nama_mesin }}</td>
                <td>{{ $i->relMotif->name }}</td>
                <td>{{ $i->relWarna->alias }}</td>
                <td>{{ $i->relGroup->name }}</td>
                <td>{{ $i->volume_potong }}</td>
                <td>{{ $i->volume_sisa }}</td>
            </tr>
            @php
                $no++;
                $total_potong += $i->volume_potong;
                $total_sisa += $i->volume_sisa;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="8" style="text-align:right">Total:</th>
            <th>{{ $total_potong }}</th>
            <th>{{ $total_sisa }}</th>
        </tr>
    </tfoot>
</table>
<script>
    $(function() {
        $('#table').DataTable({
            ordering: false
        });
    });
</script>
