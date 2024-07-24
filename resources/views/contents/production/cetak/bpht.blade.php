<title>{{ $judul }}</title>
@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 11px; padding: 5px;  hite-space: nowrap;  font-weight: bold;';
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
            <th style="{{ $style }}" colspan="10">Bukti Penyerahan Hasil Tenun (BPHT)</th>
        </tr>
    </thead>
    <tbody>
        <tr style="{{ $style . $center . $grey }}">
            <td style="{{ $style }}" colspan="10">Lampiran BPHT No. <u>{{ $parent->nomor }}</u> Tanggal
                {{ tglIndo($parent->tanggal) }}</td>
        </tr>
        <tr style="{{ $style . $center . $green }}">
            <td style="{{ $style }}" rowspan="2">No.</td>
            <td style="{{ $style }}" rowspan="2">No. Loom</td>
            <td style="{{ $style }}" colspan="6">Identitas Produk</td>
            <td style="{{ $style }}" rowspan="2">Potong Hari ini</td>
            <td style="{{ $style }}" rowspan="2">Sisa Potong</td>
        </tr>
        <tr style="{{ $style . $center . $green }}">
            <td style="{{ $style }}">No KIKW</td>
            <td style="{{ $style }}">No KIKS</td>
            <td style="{{ $style }}">No Lusi</td>
            <td style="{{ $style }}">Motif</td>
            <td style="{{ $style }}">Warna (L)</td>
            <td style="{{ $style }}">Warna (S)</td>
        </tr>
        @php
            $total = 0;
            $no = 1;
            $findId = null;
            $totalAll = 0;
        @endphp
        @foreach ($detail as $i)
            @if ($findId !== $i->id_motif)
                @if ($findId !== null)
                    <!-- Menampilkan baris total -->
                    <tr style="{{ $style . $green }}">
                        <td style="{{ $style . $center }}" colspan="8">TOTAL</td>
                        <td style="{{ $style . $right }}">{{ $total }}</td>
                        <td style="{{ $style }}"></td>
                    </tr>
                    @php
                        $total = 0; // Reset total untuk motif berikutnya
                    @endphp
                @endif
                @php
                    $findId = $i->id_motif;
                    // $totalAll += $i->potong;
                @endphp
            @endif
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $no }}</td>
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style }}">{{ $i->nomor_kikw }}</td>
                <td style="{{ $style }}">{{ $i->nomor_kiks }}</td>
                <td style="{{ $style }}">{{ $i->nomor_beam }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_songket }}</td>
                <td style="{{ $style . $right }}">{{ $i->potong }}</td>
                <td style="{{ $style . $right }}">{{ $i->sisa }}</td>
            </tr>
            @php
                $total += $i->potong;
                $totalAll += $i->potong;
                $no++;
            @endphp
        @endforeach
        <!-- Baris total untuk motif terakhir -->
        <tr style="{{ $style . $green }}">
            <td style="{{ $style . $center }}" colspan="8">TOTAL</td>
            <td style="{{ $style . $right }}">{{ $total }}</td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style . $yellow }}">
            <td style="{{ $style . $center }}" colspan="8">TOTAL</td>
            <td style="{{ $style . $right }}">{{ $totalAll }}</td>
            <td style="{{ $style }}"></td>
        </tr>

        {{-- <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}" colspan="5">Input Data dilakukan oleh</td>
            <td style="{{ $style }}" colspan="4">Pemotongan dilakukan oleh:</td>
        </tr>
        <tr style="{{ $style . $center . $green }}">
            <td style="{{ $style }}" colspan="2">Validator</td>
            <td style="{{ $style }}" colspan="3">Operator</td>
            <td style="{{ $style }}" colspan="2">Dihitung oleh:</td>
            <td style="{{ $style }}" colspan="2">Dihitung oleh:</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="2"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr> --}}
        <tr style="{{ $style . $center . $blue }}">
            <td style="{{ $style }}" colspan="2">Validator</td>
            <td style="{{ $style }}" colspan="2">Operator</td>
            <td style="{{ $style }}" colspan="2">Kasi</td>
            <td style="{{ $style }}" colspan="2">Penerima</td>
            <td style="{{ $style }}" colspan="2">Pengirim</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="2"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
