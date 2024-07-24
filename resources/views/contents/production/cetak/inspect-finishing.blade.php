@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 11px; padding: 5px;  hite-space: nowrap;  font-weight: bold;';
    $center = ' text-align: center;';
    $left = ' text-align: left;';
    $right = ' text-align: right;';
    $blue = ' background-color: rgb(207, 229, 241);';
    $yellow = ' background-color: rgb(240, 240, 162);';
    $grey = ' background-color: rgb(204, 204, 204);';
    $green = ' background-color: rgb(196, 233, 196);';
    $pink = ' background-color: rgb(247, 212, 218);';

    $addspan = 0;
    if ($proses != 'jigger' && $proses != 'drying') {
        if (!$spk) {
            $addspan++;
        }
    } else {
        $addspan = $addspan - 1;
        if (!$tanggal) {
            $addspan++;
        }
    }
@endphp
<style>
    html {
        margin: 5px
    }
</style>

<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style }} text-transform: uppercase;">
            <th style="{{ $style . $center }}" colspan="{{ 11 + $addspan }}">{{ $judul }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="{{ $style . $center }} width:50px;">No.</td>
            @if ($proses != 'jigger' && $proses != 'drying')
                @if (!$spk)
                    <td style="{{ $style . $center }} width:200px;">SPK</td>
                @endif
            @endif
            @if (!$tanggal)
                <td style="{{ $style . $center }} width:150px;">Tanggal</td>
            @endif
            <td style="{{ $style . $center }} width:150px;">Tgl. Potong</td>
            <td style="{{ $style . $center }} width:150px;">No. KIKW</td>
            <td style="{{ $style . $center }} width:150px;">No. KIKS</td>
            <td style="{{ $style . $center }} width:50px;">Mesin</td>
            <td style="{{ $style . $center }} width:100px;">Barang</td>
            <td style="{{ $style . $center }} width:50px;">Warna</td>
            <td style="{{ $style . $center }} width:50px;">Motif</td>
            <td style="{{ $style . $center }} width:50px;">Grade</td>
            @if ($proses != 'jigger' && $proses != 'drying')
                <td style="{{ $style . $center }} width:100px;">Kualitas</td>
            @endif
            <td style="{{ $style . $center }} width:50px;">Jml</td>
        </tr>
        @php
            $no = 1;
            $total = 0;
        @endphp
        @foreach ($detail as $i)
            <tr>
                <td style="{{ $style . $center }}">{{ $no }}</td>
                @if ($proses != 'jigger' && $proses != 'drying')
                    @if (!$spk)
                        <td style="{{ $style . $center }}">{{ $i->nomor }}</td>
                    @endif
                @endif
                @if (!$tanggal)
                    <td style="{{ $style . $center }}">{{ tglCustom($i->tanggal) }}</td>
                @endif
                <td style="{{ $style . $center }}">{{ tglCustom($i->tanggal_potong) }}</td>
                <td style="{{ $style . $center }}">{{ $i->no_kikw }}</td>
                <td style="{{ $style . $center }}">{{ $i->no_kiks }}</td>
                <td style="{{ $style . $center }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style . $center }}">{{ $i->nama_barang }}</td>
                <td style="{{ $style . $center }}">{{ $i->nama_warna }}</td>
                <td style="{{ $style . $center }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style . $center }}">{{ $i->nama_grade }}</td>
                @if ($proses != 'jigger' && $proses != 'drying')
                    <td style="{{ $style . $center }}">{{ $i->nama_kualitas }}</td>
                @endif
                <td style="{{ $style . $right }}">{{ $i->jml }}</td>
                @php
                    $no++;
                    $total += $i->jml;
                @endphp
            </tr>
        @endforeach
        <tr>
            <td style="{{ $style . $center }}" colspan="{{ 11 + $addspan }}">TOTAL</td>
            <td style="{{ $style . $right }}">{{ $total }}</td>
        </tr>
    </tbody>
</table>
