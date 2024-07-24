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
    $pink = ' background-color: rgb(247, 212, 218);';
@endphp
<style>
    table {
        width: 100%;
    }
</style>
<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style . $pink }} text-transform: uppercase;">
            <th colspan="33">{{ $judul }}</th>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $green }}" rowspan="2">Jenis &amp; No. Bn</td>
            <td style="{{ $style . $green }}" rowspan="2">No. Loom</td>
            <td style="{{ $style . $green }}" rowspan="2">Motif</td>
            <td style="{{ $style . $green }}" rowspan="2">Warna</td>
            <td style="{{ $style . $green }}" colspan="3">Total Potong</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-1</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-2</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-3</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-4</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-5</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-6</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-7</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-8</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-9</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-10</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-11</td>
            <td style="{{ $style . $pink }}" colspan="2">JL-12</td>
            <td style="{{ $style . $pink }}" colspan="2">TOTAL</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $green }}">B</td>
            <td style="{{ $style . $green }}">C</td>
            <td style="{{ $style . $green }}">Total</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
            <td style="{{ $style . $pink }}">B</td>
            <td style="{{ $style . $pink }}">C</td>
        </tr>
    </thead>
    <tbody>

        @php
            $total = 0;
        @endphp
        @foreach ($data as $i)
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $i->nama_barang }}</td>
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna }}</td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style . $right }}">{{ $i->volume_1 }}</td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
            </tr>
            @php
                $total += $i->volume_1;
            @endphp
        @endforeach
        <tr style="{{ $style . $yellow }}">
            <td style="{{ $style . $center }}" colspan="4">Total</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style . $right }}">{{ $total }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="5">Catatan</td>
            <td style="{{ $style }}" colspan="14">Mengetahui</td>
            <td style="{{ $style }}" colspan="14">Dibuat Oleh</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="5"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="14"></td>
            <td style="{{ $style }}" colspan="14"></td>
        </tr>
    </tfoot>
</table>
