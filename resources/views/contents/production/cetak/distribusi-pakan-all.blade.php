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
    $black = ' background-color: black;';
@endphp
<style>
    html {
        margin: 5px
    }
</style>
<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style . $black }} color: white;">
            <th style="{{ $style }}" colspan="23">{{ $judul }}</th>
        </tr>
    </thead>
    <tbody>
        <tr style="{{ $style . $center . $grey }}">
            <td style="{{ $style }}" rowspan="2">No. </td>
            <td style="{{ $style }}" rowspan="2">No. Mesin </td>
            <td style="{{ $style }}" rowspan="2">No KIKW</td>
            <td style="{{ $style }}" colspan="2">W-1</td>
            <td style="{{ $style }}" colspan="2">W-2</td>
            <td style="{{ $style }}" colspan="2">W-3</td>
            <td style="{{ $style }}" colspan="2">W-4</td>
            <td style="{{ $style }}" colspan="2">W-5</td>
            <td style="{{ $style }}" colspan="2">W-6</td>
            <td style="{{ $style }}" colspan="2">W-7</td>
            <td style="{{ $style }}" colspan="2">W-8</td>
            <td style="{{ $style }}" colspan="2">W-9</td>
            <td style="{{ $style }}" colspan="2">W-10</td>
        </tr>
        <tr style="{{ $style . $center . $grey }}">
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
            <td style="{{ $style }}width: 22px;">Wrn</td>
            <td style="{{ $style }}width: 35px;">Qty</td>
        </tr>
        @php
            $no = 1;
            $total = [
                '1_warna_1' => 0,
                '2_warna_1' => 0,
                '1_warna_2' => 0,
                '2_warna_2' => 0,
                '1_warna_3' => 0,
                '2_warna_3' => 0,
                '1_warna_4' => 0,
                '2_warna_4' => 0,
                '1_warna_5' => 0,
                '2_warna_5' => 0,
                '1_warna_6' => 0,
                '2_warna_6' => 0,
                '1_warna_7' => 0,
                '2_warna_7' => 0,
                '1_warna_8' => 0,
                '2_warna_8' => 0,
                '1_warna_9' => 0,
                '2_warna_9' => 0,
                '1_warna_10' => 0,
                '2_warna_10' => 0,
            ];
        @endphp
        @foreach ($detail as $i)
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $no }}</td>
                <td style="{{ $style }}">{{ $i->nama_mesin }}</td>
                <td style="{{ $style }}">{{ $i->no_kikw }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_1 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_1 }}
                    {{ $i->volume_2_warna_1 ? ' / ' . $i->volume_2_warna_1 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_2 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_2 }}
                    {{ $i->volume_2_warna_2 ? ' / ' . $i->volume_2_warna_2 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_3 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_3 }}
                    {{ $i->volume_2_warna_3 ? ' / ' . $i->volume_2_warna_3 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_4 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_4 }}
                    {{ $i->volume_2_warna_4 ? ' / ' . $i->volume_2_warna_4 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_5 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_5 }}
                    {{ $i->volume_2_warna_5 ? ' / ' . $i->volume_2_warna_5 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_6 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_6 }}
                    {{ $i->volume_2_warna_6 ? ' / ' . $i->volume_2_warna_6 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_7 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_7 }}
                    {{ $i->volume_2_warna_7 ? ' / ' . $i->volume_2_warna_7 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_8 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_8 }}
                    {{ $i->volume_2_warna_8 ? ' / ' . $i->volume_2_warna_8 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_9 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_9 }}
                    {{ $i->volume_2_warna_9 ? ' / ' . $i->volume_2_warna_9 : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_warna_10 }}</td>
                <td style="{{ $style }}">{{ $i->volume_1_warna_10 }}
                    {{ $i->volume_2_warna_10 ? ' / ' . $i->volume_2_warna_10 : '' }}</td>
            </tr>
            @php
                $no++;
                $total['1_warna_1'] += $i->volume_1_warna_1;
                $total['2_warna_1'] += $i->volume_2_warna_1;
                $total['1_warna_2'] += $i->volume_1_warna_2;
                $total['2_warna_2'] += $i->volume_2_warna_2;
                $total['1_warna_3'] += $i->volume_1_warna_3;
                $total['2_warna_3'] += $i->volume_2_warna_3;
                $total['1_warna_4'] += $i->volume_1_warna_4;
                $total['2_warna_4'] += $i->volume_2_warna_4;
                $total['1_warna_5'] += $i->volume_1_warna_5;
                $total['2_warna_5'] += $i->volume_2_warna_5;
                $total['1_warna_6'] += $i->volume_1_warna_6;
                $total['2_warna_6'] += $i->volume_2_warna_6;
                $total['1_warna_7'] += $i->volume_1_warna_7;
                $total['2_warna_7'] += $i->volume_2_warna_7;
                $total['1_warna_8'] += $i->volume_1_warna_8;
                $total['2_warna_8'] += $i->volume_2_warna_8;
                $total['1_warna_9'] += $i->volume_1_warna_9;
                $total['2_warna_9'] += $i->volume_2_warna_9;
                $total['1_warna_10'] += $i->volume_1_warna_10;
                $total['2_warna_10'] += $i->volume_2_warna_10;
            @endphp
        @endforeach
        <tr style="{{ $style }}">
            <td style="{{ $style . $center }}" colspan="3">TOTAL</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_1'] }}
                {{ $total['2_warna_1'] != 0 ? ' / ' . $total['2_warna_1'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_2'] }}
                {{ $total['2_warna_2'] != 0 ? ' / ' . $total['2_warna_2'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_3'] }}
                {{ $total['2_warna_3'] != 0 ? ' / ' . $total['2_warna_3'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_4'] }}
                {{ $total['2_warna_4'] != 0 ? ' / ' . $total['2_warna_4'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_5'] }}
                {{ $total['2_warna_5'] != 0 ? ' / ' . $total['2_warna_5'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_6'] }}
                {{ $total['2_warna_6'] != 0 ? ' / ' . $total['2_warna_6'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_7'] }}
                {{ $total['2_warna_7'] != 0 ? ' / ' . $total['2_warna_7'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_8'] }}
                {{ $total['2_warna_8'] != 0 ? ' / ' . $total['2_warna_8'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_9'] }}
                {{ $total['2_warna_9'] != 0 ? ' / ' . $total['2_warna_9'] : '' }}</td>
            <td style="{{ $style . $black }}"></td>
            <td style="{{ $style }}">{{ $total['1_warna_10'] }}
                {{ $total['2_warna_10'] != 0 ? ' / ' . $total['2_warna_10'] : '' }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="{{ $style . $center . $grey }}">
            <td style="{{ $style }}" colspan="4" rowspan="2">Note:</td>
            <td style="{{ $style }}" colspan="4">Yang Menerima</td>
            <td style="{{ $style }}" colspan="8">Yang Menyerahkan</td>
            <td style="{{ $style }}" colspan="7">Mengetahui</td>
        </tr>
        <tr style="{{ $style . $center . $grey }}">
            <td style="{{ $style }}" colspan="4">Karu Weaving</td>
            <td style="{{ $style }}" colspan="8">Karu Preparatory</td>
            <td style="{{ $style }}" colspan="7">Kasi Weaving</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="4"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="4"></td>
            <td style="{{ $style }}" colspan="8"></td>
            <td style="{{ $style }}" colspan="7"></td>
        </tr>
    </tfoot>
</table>
