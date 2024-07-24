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
        <tr style="{{ $style . $center . $yellow }}">
            <td style="{{ $style }}" colspan="9">SURAT PERINTAH KERJA {{ $proses }} (SPK -
                {{ $proses }})</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">No. SPK</td>
            <td style="{{ $style }}" colspan="2">{{ $parent->nomor }}</td>
            <td style="{{ $style }}" colspan="5">Kepada, Yth: {{ $parent->relSupplier->name }}</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Tanggal</td>
            <td style="{{ $style }}" colspan="7">{{ tglIndo($parent->tanggal) }}</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Kategori Produk</td>
            <td style="{{ $style }}" colspan="7"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Total</td>
            <td style="{{ $style }}" colspan="7">{{ $parent->total_kirim }}</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="9">Dengan hormat, kain tersebut pada rincian SPK ini kami
                kirimkan dan mohon diterima untuk diproses sesuai dengan kontrak kerja yang
                disepakati</td>
        </tr>
        <tr style="{{ $style . $blue . $center }}">
            <td style="{{ $style }} width: 50px;">No.</td>
            <td style="{{ $style }}">No. Surat Jalan</td>
            <td style="{{ $style }}">Tanggal</td>
            <td style="{{ $style }}">Tgl. Potong</td>
            <td style="{{ $style }} width: 100px;">Motif</td>
            <td style="{{ $style }} width: 65px;">Roll</td>
            <td style="{{ $style }} width: 65px;">Pcs</td>
            <td style="{{ $style }} width: 65px;">Yard</td>
            <td style="{{ $style }}">Keterangan</td>
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
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $blue }}" colspan="4">Dikirim dari Solo, {{ tglIndo($parent->tanggal) }}</td>
            <td style="{{ $style . $yellow }}" colspan="5">Diterima di ____________________ tgl ____________________
            </td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $blue }}" colspan="2" rowspan="2">Dibuat Admin Finishing</td>
            <td style="{{ $style . $blue }}" colspan="2" rowspan="2">Mengetahui, Kasubsi Finishing</td>
            <td style="{{ $style . $yellow }}" colspan="5">Diterima oleh pihak Jasa {{ $proses }}</td>
        </tr>
        <tr style="{{ $style . $center }}">
            <td style="{{ $style . $yellow }}" colspan="3">Mengetahui</td>
            <td style="{{ $style . $yellow }}" colspan="2">Penerima</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2"><br><br><br><br><br><br></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="3"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
