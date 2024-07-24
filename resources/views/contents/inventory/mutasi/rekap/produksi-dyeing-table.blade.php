<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead style="text-align: center;">
        <tr>
            <td>NO</td>
            <td>TGL.</td>
            <td>WARNA</td>
            <td>BARANG</td>
            <td>BERAT (KG)</td>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $total = 0;
        @endphp
        @foreach ($data as $i)
            <tr>
                <td>{{ $no }}</td>
                <td>{{ tglCustom($i->tanggal) }}</td>
                <td>{{ $i->nama_warna }}</td>
                <td>{{ $i->nama_barang }}</td>
                <td>{{ $i->kg }}</td>
            </tr>
            @php
                $no++;
                $total += $i->kg;
            @endphp
        @endforeach
        <tr>
            <td colspan="4" style="background-color: rgb(238, 218, 104)">TOTAL</td>
            <td style="background-color: rgb(238, 218, 104)">{{ $total }}</td>
        </tr>
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
