@php
    $addStyle = $cetak ? 'border: 1px solid black;' : '';
@endphp
<table class="table table-bordered table-striped" cellspacing="0" id="table">
    <thead>
        <tr style="{{ $addStyle }}">
            <td style="{{ $addStyle }} text-align:center;" rowspan="2">NO</td>
            <td style="{{ $addStyle }} text-align:center;" rowspan="2">TIPE PENGIRIMAN</td>
            <td style="{{ $addStyle }} text-align:center;" rowspan="2">JENIS BARANG</td>
            <td style="{{ $addStyle }} text-align:center;" rowspan="2">MOTIF</td>
            <td style="{{ $addStyle }} text-align:center;" colspan="2">ASAL</td>
            <td style="{{ $addStyle }} text-align:center;" colspan="2">TUJUAN</td>
        </tr>
        <tr style="{{ $addStyle }}">
            <td style="{{ $addStyle }} text-align:center;">GUDANG</td>
            <td style="{{ $addStyle }} text-align:center;">VOLUME</td>
            <td style="{{ $addStyle }} text-align:center;">GUDANG</td>
            <td style="{{ $addStyle }} text-align:center;">VOLUME</td>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $i)
            <tr style="{{ $addStyle }}">
                <td style="{{ $addStyle }} text-align:center;">{{ $no }}</td>
                <td style="{{ $addStyle }}">{{ $i->initial }} - {{ $i->nama_pengiriman }}</td>
                <td style="{{ $addStyle }}">{{ $i->nama_barang }}</td>
                <td style="{{ $addStyle }}">{{ $i->nama_motif }}</td>
                <td style="{{ $addStyle }}">{{ $i->asal_nama_gudang }}</td>
                <td style="{{ $addStyle }}">
                    {{ $i->asal_volume_1 ? $i->asal_volume_1 . ' ' . $i->asal_nama_satuan_1 : '' }}
                    {{ $i->asal_volume_2 ? ' / ' . $i->asal_volume_2 . ' ' . $i->asal_nama_satuan_2 : '' }}
                </td>
                <td style="{{ $addStyle }}">{{ $i->tujuan_nama_gudang }}</td>
                <td style="{{ $addStyle }}">
                    {{ $i->tujuan_volume_1 ? $i->tujuan_volume_1 . ' ' . $i->tujuan_nama_satuan_1 : '' }}
                    {{ $i->tujuan_volume_2 ? ' / ' . $i->tujuan_volume_2 . ' ' . $i->tujuan_nama_satuan_2 : '' }}
                </td>
            </tr>
            @php
                $no++;
            @endphp
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#table').DataTable({
            ordering: false
        });
    });
</script>
