<title>{{ $judul }}</title>
@php
    $style =
        'border: 0.5px solid black; border-collapse: collapse; font-size: 8px; padding: 2px;  hite-space: nowrap;  font-weight: bold;';
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
        <tr style="{{ $style . $center }}">
            <td style="{{ $style }}" colspan="10">PENYELESAIAN SURAT PERINTAH KERJA {{ $proses }} (SPK -
                {{ $proses }})</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">No. SPK-S</td>
            <td style="{{ $style }}" colspan="8"></td>
        </tr>
        <tr style="{{ $style . $center . $yellow }}">
            <td style="{{ $style }} width: 50px">No.</td>
            <td style="{{ $style }}">No. Surat Jalan</td>
            <td style="{{ $style }}">Tgl. Terima</td>
            <td style="{{ $style }}">Tgl. Potong</td>
            <td style="{{ $style }}">Motif</td>
            <td style="{{ $style }}">Roll</td>
            <td style="{{ $style }}">Pcs</td>
            <td style="{{ $style }}">Yard</td>
            <td style="{{ $style }}">Rp/Yard</td>
            <td style="{{ $style }}">Total, Rp.</td>
        </tr>
        @php
            $no = 1;
            $total = 0;
        @endphp
        @foreach ($detail as $i)
            <tr style="{{ $style }}">
                <td style="{{ $style }}">{{ $no }}</td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}">{{ $i->tanggal ? tglCustom($i->tanggal) : '' }}</td>
                <td style="{{ $style }}">{{ $i->tanggal_potong ? tglCustom($i->tanggal_potong) : '' }}</td>
                <td style="{{ $style }}">{{ $i->nama_motif }}</td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}">{{ $i->volume_1 }}</td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
                <td style="{{ $style }}"></td>
            </tr>
            @php
                $no++;
                $total += $i->volume_1;
            @endphp
        @endforeach

        <tr style="{{ $style }}">
            <td style="{{ $style . $center }}" colspan="5">Total</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}">{{ $total }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="5">Dikirim kembali ke Solo, tgl ____________________</td>
            <td style="{{ $style . $blue }}" colspan="5">Dikirim kembali di Solo, tgl ____________________</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="5">Dikirim Kembali Oleh Pihak {{ $proses }}</td>
            <td style="{{ $style . $blue }}" colspan="5">Diterima kembali di Solo, oleh:</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="3">Pengirim</td>
            <td style="{{ $style . $yellow }}" colspan="2">Mengetahui</td>
            <td style="{{ $style . $blue }}" colspan="3">Mengetahui</td>
            <td style="{{ $style . $blue }}" colspan="2">Penerima</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="3"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
