@php
    $addStyle = $cetak ? 'border: 1px solid black;' : '';
@endphp
<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead>
        <tr>
            <th rowspan="1" colspan="1" style="text-align: center;{{ $addStyle }}">NO</th>
            <th rowspan="1" colspan="1" style="text-align: center;{{ $addStyle }}">BARANG</th>
            <th rowspan="1" colspan="1" style="text-align: center;{{ $addStyle }}">MOTIF</th>
            <th rowspan="1" colspan="1" style="text-align: center;{{ $addStyle }}">BULAN</th>
            <th rowspan="1" colspan="1" style="text-align: center;{{ $addStyle }}">JUMLAH</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $barang = null;
            $bulan = null;
            $total = 0;
        @endphp
        @foreach ($data as $i)
            @if ($barang == null)
                @php
                    $barang = $i->id_barang;
                    $bulan = $i->bulan;
                    $total = $i->volume;
                @endphp
            @else
                @if ($barang != $i->id_barang || $bulan != $i->bulan)
                    <tr style="background-color: rgb(206, 240, 240)">
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <th style="{{ $addStyle }}">Total</th>
                        <th style="{{ $addStyle }}">{{ $total }}</th>
                    </tr>
                    @php
                        $total = 0;
                    @endphp
                @else
                    @php
                        $total += $i->volume;
                    @endphp
                @endif
            @endif
            <tr>
                <td style="{{ $addStyle }}">{{ $no }}</td>
                <td style="{{ $addStyle }}">{{ $i->nama_barang }}</td>
                <td style="{{ $addStyle }}">{{ $i->nama_motif }}</td>
                <td style="{{ $addStyle }}">{{ getBulan($i->bulan) }}</td>
                <td style="{{ $addStyle }}">{{ $i->volume }}</td>
            </tr>
            @php
                $no++;
            @endphp
            @if ($loop->last)
                <tr style="background-color: rgb(206, 240, 240)">
                    <td style="{{ $addStyle }}"></td>
                    <td style="{{ $addStyle }}"></td>
                    <td style="{{ $addStyle }}"></td>
                    <th style="{{ $addStyle }}">Total</th>
                    <th style="{{ $addStyle }}">{{ $total }}</th>
                </tr>
            @endif
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
