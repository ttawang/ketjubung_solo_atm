<title>{{ $judul }}</title>
@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 11px; padding: 5px;  hite-space: nowrap;  font-weight: bold;';
    // $bold = ' font-weight: bold;';
    $center = ' text-align: center;';
    $left = ' text-align: left;';
    $left = ' text-align: right;';
    $blue = ' background-color: rgb(207, 229, 241);';
    $yellow = ' background-color: rgb(240, 240, 162);';
@endphp
<style>
    table {
        width: 100%;
    }

    html {
        margin: 5px
    }
</style>
<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style . $center . $yellow }}">
            <th style="{{ $style }}" colspan="8">PENYELESAIAN SURAT PERINTAH KERJA (SPK) DUDULAN</th>
        </tr>
    </thead>
    <tbody>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">No. SPK-Du: </td>
            <td style="{{ $style }}" colspan="6">{{ $parent->nomor }}</td>
        </tr>
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}" rowspan="2">No. Loom</td>
            <td style="{{ $style }}" rowspan="2">Motif</td>
            <td style="{{ $style }}" colspan="3">Tahap Pengembalian</td>
            <td style="{{ $style }}" colspan="3">Kuanitas. Pcs</td>
        </tr>
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}">Tahap-1</td>
            <td style="{{ $style }}">Tahap-2</td>
            <td style="{{ $style }}">Tahap-3</td>
            <td style="{{ $style }}">Baik</td>
            <td style="{{ $style }}">Cacat</td>
            <td style="{{ $style }}">Total</td>
        </tr>
        @php
            $total = 0;
            $baik = 0;
            $cacat = 0;
        @endphp
        @foreach ($detail as $i)
            <tr style="{{ $style . $center }}">
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}">{{ $i->tahap_1 }}</td>
                <td style="{{ $style }}">{{ $i->tahap_2 }}</td>
                <td style="{{ $style }}">{{ $i->tahap_3 }}</td>
                <td style="{{ $style }}">{{ $i->baik }}</td>
                <td style="{{ $style }}">{{ $i->cacat }}</td>
                <td style="{{ $style }}">{{ $i->total }}</td>
            </tr>
            @php
                $total += $i->total;
                $baik += $i->baik;
                $cacat += $i->cacat;
            @endphp
        @endforeach
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="5">T O T A L</td>
            <td style="{{ $style }}">{{ $baik }}</td>
            <td style="{{ $style }}">{{ $cacat }}</td>
            <td style="{{ $style }}">{{ $total }}</td>
        </tr>
        <tr style="{{ $style . $center . $yellow }}">
            <td style="{{ $style }}" colspan="8">Diselesaikan oleh Pihak Jasa Dudulan, tgl ___________</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="4">Diserahkan oleh Jasa Dudulan</td>
            <td style="{{ $style . $blue }}" colspan="4">Diterima oleh Pihak Inspekting</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="2">Mengetahui</td>
            <td style="{{ $style . $yellow }}" colspan="2">Pengrim</td>
            <td style="{{ $style . $blue }}" colspan="2">KARU Inspekting</td>
            <td style="{{ $style . $blue }}" colspan="2">KASI Weaving</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="2"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
