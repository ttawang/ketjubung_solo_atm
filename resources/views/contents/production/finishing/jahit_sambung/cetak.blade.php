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
    $grey = ' background-color: rgb(204, 204, 204);';
    $green = ' background-color: rgb(196, 233, 196);';
@endphp
<style>
    table {
        width: 100%;
    }
</style>
<table style="{{ $style . $center }}">
    <thead style="{{ $style . $center }}">
        <tr style="{{ $style . $center }}">
            <th style="{{ $style . $center }}" colspan="11">{{ $judul }}</th>
        </tr>
    </thead>
    <tbody>
        <tr style="{{ $style . $center . $green }}">
            <td style="{{ $style . $center }}" rowspan="2">No.</td>
            <td style="{{ $style . $center }}" rowspan="2">Tgl. Jahit Sambung</td>
            <td style="{{ $style . $center }}" rowspan="2">No. Loom</td>
            <td style="{{ $style . $center }}" rowspan="2">Tgl. Potong</td>
            <td style="{{ $style . $center }}" rowspan="2">Jenis No. Benang</td>
            <td style="{{ $style . $center }}" rowspan="2">Motif</td>
            <td style="{{ $style . $center }}" rowspan="2">Warna</td>
            <td style="{{ $style . $center }}" rowspan="2">Pcs</td>
            <td style="{{ $style . $center }}" colspan="3">Kirim ke P-1</td>
        </tr>
        <tr style="{{ $style . $center . $green }}">
            <td style="{{ $style . $center }}">Supplier</td>
            <td style="{{ $style . $center }}">Tgl</td>
            <td style="{{ $style . $center }}">SPK</td>
        </tr>
        @php
            $no = 1;
            $sameId = ['id_motif' => null];
            $total = 0;
        @endphp
        @foreach ($data as $i)
            @if ($sameId['id_motif'] == null)
                @php
                    $sameId['id_motif'] = $i->id_motif;
                @endphp
                <tr style="{{ $style }}">
                    <td style="{{ $style . $center }}">{{ $no }}</td>
                    <td style="{{ $style . $center }}">
                        {{ $i->tanggal_js != '1997-10-23' && $i->tanggal_js != null ? tglCustom($i->tanggal_js) : '' }}
                    </td>
                    <td style="{{ $style . $center }}">{{ $i->nama_mesin }}</td>
                    <td style="{{ $style . $center }}">
                        {{ $i->tanggal_potong != '1997-10-23' && $i->tanggal_potong != null ? tglCustom($i->tanggal_potong) : '' }}
                    </td>
                    <td style="{{ $style . $center }}">{{ $i->nama_barang }}</td>
                    <td style="{{ $style . $center }}">{{ $i->nama_motif }}</td>
                    <td style="{{ $style . $center }}">{{ $i->nama_warna }}</td>
                    <td style="{{ $style . $center }}">{{ $i->pcs }}</td>
                    <td style="{{ $style . $center }}">{{ $i->nama_supplier }}</td>
                    <td style="{{ $style . $center }}">
                        {{ $i->tanggal_p1 != '1997-10-23' && $i->tanggal_p1 != null ? tglCustom($i->tanggal_p1) : '' }}
                    </td>
                    <td style="{{ $style . $center }}">{{ $i->nomor_p1 }}</td>
                </tr>
                @php
                    $total += $i->pcs;
                @endphp
            @else
                @if ($sameId['id_motif'] == $i->id_motif)
                    <tr style="{{ $style }}">
                        <td style="{{ $style . $center }}">{{ $no }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_js != '1997-10-23' && $i->tanggal_js != null ? tglCustom($i->tanggal_js) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nama_mesin }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_potong != '1997-10-23' && $i->tanggal_potong != null ? tglCustom($i->tanggal_potong) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nama_barang }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_motif }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_warna }}</td>
                        <td style="{{ $style . $center }}">{{ $i->pcs }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_supplier }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_p1 != '1997-10-23' && $i->tanggal_p1 != null ? tglCustom($i->tanggal_p1) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nomor_p1 }}</td>
                    </tr>
                    @php
                        $total += $i->pcs;
                    @endphp
                @else
                    <tr style="{{ $style . $yellow }}">
                        <td style="{{ $style . $center }}" colspan="7">TOTAL</td>
                        <td style="{{ $style . $center }}" colspan="">{{ $total }}</td>
                        <td style="{{ $style . $center }}" colspan="3"></td>
                    </tr>
                    @php
                        $sameId['id_motif'] = $i->id_motif;
                        $total = 0;
                        $total += $i->pcs;
                    @endphp
                    <tr style="{{ $style }}">
                        <td style="{{ $style . $center }}">{{ $no }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_js != '1997-10-23' && $i->tanggal_js != null ? tglCustom($i->tanggal_js) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nama_mesin }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_potong != '1997-10-23' && $i->tanggal_potong != null ? tglCustom($i->tanggal_potong) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nama_barang }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_motif }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_warna }}</td>
                        <td style="{{ $style . $center }}">{{ $i->pcs }}</td>
                        <td style="{{ $style . $center }}">{{ $i->nama_supplier }}</td>
                        <td style="{{ $style . $center }}">
                            {{ $i->tanggal_p1 != '1997-10-23' && $i->tanggal_p1 != null ? tglCustom($i->tanggal_p1) : '' }}
                        </td>
                        <td style="{{ $style . $center }}">{{ $i->nomor_p1 }}</td>
                    </tr>
                @endif
            @endif
            @if ($loop->last)
                <tr style="{{ $style . $yellow }}">
                    <td style="{{ $style . $center }}" colspan="7">TOTAL</td>
                    <td style="{{ $style . $center }}" colspan="">{{ $total }}</td>
                    <td style="{{ $style . $center }}" colspan="3"></td>
                </tr>
            @endif
            @php
                $no++;
            @endphp
        @endforeach

</table>
