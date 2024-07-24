<title>{{ $judul }}</title>
@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 8px; padding: 5px;  hite-space: nowrap;  font-weight: bold;';
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
    <tbody>
        <tr style="{{ $style . $center . $yellow }}">
            <td style="{{ $style }}" colspan="8">SURAT PERINTAH KERJA DUDULAN (SPK-Du)</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">No. SPK-Du:</td>
            <td style="{{ $style }}">{{ $parent->nomor }}</td>
            <td style="{{ $style }}">Kepada, yth:</td>
            <td style="{{ $style }}" colspan="4"> {{ $parent->relSupplier->name }}</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Tanggal:</td>
            <td style="{{ $style }}">{{ tglIndo($parent->tanggal) }}</td>
            <td style="{{ $style }}" colspan="5"></td>
        </tr>
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}" rowspan="2">Jenis &amp; No. Bn</td>
            <td style="{{ $style }}" rowspan="2">Motif</td>
            <td style="{{ $style }}" rowspan="2">Warna</td>
            <td style="{{ $style }}" rowspan="2">Tgl. Potong</td>
            <td style="{{ $style }}" rowspan="2">No. Loom</td>
            <td style="{{ $style }}" colspan="3">Kuanitas. Pcs</td>
        </tr>
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}">Baik</td>
            <td style="{{ $style }}">Cacat</td>
            <td style="{{ $style }}">Total</td>
        </tr>
        @php
            $total = 0;
            $totalBaik = 0;
            $totalCacat = 0;
        @endphp
        @foreach ($detail as $i)
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $i->nama_barang }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna }}</td>
                <td style="{{ $style }}">{{ $i->tanggal_potong }}</td>
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style }}">{{ $i->baik }}</td>
                <td style="{{ $style }}">{{ $i->cacat }}</td>
                <td style="{{ $style }}">{{ $i->total }}</td>
            </tr>
            @php
                $total += $i->total;
                $totalBaik += $i->baik;
                $totalCacat += $i->cacat;
            @endphp
        @endforeach
        <tr style="{{ $style }}">
            <td style="{{ $style . $center }}" colspan="5">T O T A L</td>
            <td style="{{ $style }}"> {{ $totalBaik }} </td>
            <td style="{{ $style }}"> {{ $totalCacat }} </td>
            <td style="{{ $style }}"> {{ $total }} </td>
        </tr>
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}" colspan="8">Diserahkan oleh Inspekting, tgl
                {{ tglIndo($parent->tanggal) }}</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $blue }}" colspan="3">Penyerahan oleh Inspekting:</td>
            <td style="{{ $style . $yellow }}" colspan="5">Penenrima, Jasa Luar Duduan</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $blue }}" colspan="2">KASI Weaving</td>
            <td style="{{ $style . $blue }}">KARU Inspekting</td>
            <td style="{{ $style . $yellow }}" colspan="3">Mengetahui</td>
            <td style="{{ $style . $yellow }}" colspan="2">Penerima</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2"><br><br><br><br><br><br></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
