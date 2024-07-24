<title>{{ $judul }}</title>
@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 9px; padding: 5px;  hite-space: nowrap;  font-weight: bold;';
    // $bold = ' font-weight: bold;';
    $center = ' text-align: center;';
    $left = ' text-align: left;';
    $right = ' text-align: right;';
    $blue = ' background-color: rgb(207, 229, 241);';
    $yellow = ' background-color: rgb(240, 240, 162);';
    $grey = ' background-color: rgb(204, 204, 204);';
    $green = ' background-color: rgb(196, 233, 196);';
@endphp
<style>
    table {
        width: 100%;
    }
</style>
<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style . $grey }}">
            <th style="{{ $style }}" colspan="10">{{ $judul }}</th>
        </tr>
        <tr style="{{ $style . $grey }}">
            <td style="{{ $style }}" colspan="2">No. Pengiriman</td>
            <td style="{{ $style }}" colspan="3">{{ $parent->nomor }}</td>
            <td style="{{ $style }}" colspan="2">Tgl. Pengiriman</td>
            <td style="{{ $style }}" colspan="3">{{ tglCustom($parent->tanggal) }}</td>
        </tr>
        <tr style="{{ $style . $grey . $center }}">
            <td style="{{ $style }} width:25px;" rowspan="2">No.</td>
            <td style="{{ $style }}" rowspan="2">Tgl. Potong</td>
            <td style="{{ $style }}" rowspan="2">No. Benang</td>
            <td style="{{ $style }}" rowspan="2">Motif</td>
            <td style="{{ $style }} width:70px;" rowspan="2">Warna (L)</td>
            <td style="{{ $style }} width:70px;" rowspan="2">Warna (S)</td>
            <td style="{{ $style }}" rowspan="2">No. Mesin</td>
            <td style="{{ $style }}" colspan="3">Kuantitas</td>
        </tr>
        <tr style="{{ $style . $grey . $center }}">
            <td style="{{ $style }}">Baik</td>
            <td style="{{ $style }}">Cacat</td>
            <td style="{{ $style }}">Total Kirim</td>
        </tr>
    </thead>
    <tbody>

        @php
            $totalAll = 0;
            $cacatAll = 0;
            $baikAll = 0;
            $total = 0;
            $cacat = 0;
            $baik = 0;
            $no = 1;
            $findId = null;
        @endphp
        @foreach ($detail as $i)
            @if ($findId !== $i->id_motif)
                @if ($findId !== null)
                    <!-- Menampilkan baris total -->
                    <tr style="{{ $style . $green }}">
                        <td style="{{ $style . $center }}" colspan="7">Total</td>
                        <td style="{{ $style . $right }}">{{ $baik }}</td>
                        <td style="{{ $style . $right }}">{{ $cacat }}</td>
                        <td style="{{ $style . $right }}">{{ $total }}</td>
                    </tr>
                    @php
                        $total = 0;
                        $cacat = 0;
                        $baik = 0;
                    @endphp
                @endif
                @php
                    $findId = $i->id_motif;
                    // $totalAll += $i->potong;
                @endphp
            @endif
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $no }}</td>
                <td style="{{ $style }}">{{ $i->tanggal_potong ? tglCustom($i->tanggal_potong) : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_barang }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_songket }}</td>
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style . $right }}">{{ $i->baik }}</td>
                <td style="{{ $style . $right }}">{{ $i->cacat }}</td>
                <td style="{{ $style . $right }}">{{ $i->jml }}</td>
            </tr>
            @php
                $totalAll += $i->jml;
                $cacatAll += $i->cacat;
                $baikAll += $i->baik;
                $total += $i->jml;
                $cacat += $i->cacat;
                $baik += $i->baik;
                $no++;
            @endphp
        @endforeach
        <tr style="{{ $style . $green }}">
            <td style="{{ $style . $center }}" colspan="7">Total</td>
            <td style="{{ $style . $right }}">{{ $baik }}</td>
            <td style="{{ $style . $right }}">{{ $cacat }}</td>
            <td style="{{ $style . $right }}">{{ $total }}</td>
        </tr>
        <tr style="{{ $style . $yellow }}">
            <td style="{{ $style . $center }}" colspan="7">Total</td>
            <td style="{{ $style . $right }}">{{ $baikAll }}</td>
            <td style="{{ $style . $right }}">{{ $cacatAll }}</td>
            <td style="{{ $style . $right }}">{{ $totalAll }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $grey }}" colspan="5">Serah Terima ini diketahui oleh:</td>
            <td style="{{ $style . $yellow }}" colspan="5">Serah Terima ini dilakukan oleh:</td>
        </tr>
        <tr style="{{ $style . $grey . $center }}">
            <td style="{{ $style . $grey }}" colspan="2">Kasie Finishing</td>
            <td style="{{ $style . $grey }}" colspan="3">Kasie Weaving</td>
            <td style="{{ $style . $yellow }}" colspan="3">Karu Finishing</td>
            <td style="{{ $style . $yellow }}" colspan="2">Karu Inspecting</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="2">
                <p>
                <p>
                <p>
                <p></p>
                </p>
                </p>
                </p>
            </td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tfoot>
</table>
