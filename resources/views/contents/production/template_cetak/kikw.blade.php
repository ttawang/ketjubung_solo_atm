<title>{{ $judul }}</title>
@php
    $style = 'border: 0.5px solid black; border-collapse: collapse; font-size: 11px; padding: 5px;  hite-space: nowrap;';
    $bold = ' font-weight: bold;';
    $center = ' text-align: center;';
    $left = ' text-align: left;';
    $left = ' text-align: right;';
    $blue = ' background-color: rgb(207, 229, 241);';
    $yellow = ' background-color: rgb(245, 245, 15);';
    $grey = ' background-color: rgb(204, 204, 204);';
    $green = ' background-color: rgb(196, 233, 196);';
    $arsir = ' background-color: #504e4e;';
@endphp
<style>
    table {
        width: 100%;
    }
</style>
<table style="{{ $style }}">
    <thead>
        <tr style="{{ $style . $blue }}">
            <th style="{{ $style }}" colspan="14">KIKW (KARTU INSTRUKSI KERJA WEAVING )</th>
        </tr>
    </thead>
    <tbody>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">Identitas KIKW:</td>
            <td style="{{ $style }}" colspan="12"></td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">No. KIKW</td>
            <td style="{{ $style }}" colspan="3">{{ $no_kikw }}</td>
            <td style="{{ $style }}" colspan="3">No. RDB</td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style . $blue }}" colspan="4">Dikeluarkan, PPIC</td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">Jenis &amp; No. Lusi</td>
            <td style="{{ $style }}" colspan="3">{{ $lusi ? $lusi->relBarang->name : '' }}</td>
            <td style="{{ $style }}" colspan="3">Panjang Beam, Pcs</td>
            <td style="{{ $style }}" colspan="2">{{ $lusi ? $lusi->volume_2 : '' }}</td>
            <td style="{{ $style . $blue }}" colspan="4">Tgl _________</td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">Songket-1 No,</td>
            <td style="{{ $style }}" colspan="3">{{ $songket_1 ? $songket_1->relBarang->name : '' }}</td>
            <td style="{{ $style }}" colspan="3">Panjang Beam, Pcs</td>
            <td style="{{ $style }}" colspan="2">{{ $songket_1 ? $songket_1->volume_2 : '' }}</td>
            <td style="{{ $style . $blue }}" colspan="2">Mprod</td>
            <td style="{{ $style . $blue }}" colspan="2">Ka PPIC:</td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">Songket-2, No. </td>
            <td style="{{ $style }}" colspan="3">{{ $songket_2 ? $songket_2->relBarang->name : '' }}</td>
            <td style="{{ $style }}" colspan="3">Panjang Beam, Pcs</td>
            <td style="{{ $style }}" colspan="2">{{ $songket_2 ? $songket_2->volume_2 : '' }}</td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="2">Songket-3, No.</td>
            <td style="{{ $style }}" colspan="3">{{ $songket_3 ? $songket_3->relBarang->name : '' }}</td>
            <td style="{{ $style }}" colspan="3">Panjang Beam, Pcs</td>
            <td style="{{ $style }}" colspan="2">{{ $songket_3 ? $songket_3->volume_2 : '' }}</td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="14">Realisasi Proses Warping, sesuai KB (Kartu Beam) terkai</td>
        </tr>
        <tr style="{{ $style . $bold . $yellow }}">
            <td style="{{ $style }} width:20px;" rowspan="2">No. </td>
            <td style="{{ $style }}" rowspan="2">Item</td>
            <td style="{{ $style }}" colspan="2" rowspan="2">Deskripsi</td>
            <td style="{{ $style }}" rowspan="2">Kuant Pcs</td>
            <td style="{{ $style }}" colspan="2">Tanggal</td>
            <td style="{{ $style }}" colspan="2" rowspan="2">Total JKM</td>
            <td style="{{ $style }}" rowspan="2">Kuant Pcs/Kg</td>
            <td style="{{ $style }}" colspan="4">Tandatangan Pengendali:</td>
        </tr>
        <tr style="{{ $style . $bold . $yellow }}">
            <td style="{{ $style }}">Start</td>
            <td style="{{ $style }}">Finish</td>
            <td style="{{ $style }}" colspan="2">KASI</td>
            <td style="{{ $style }}" colspan="2">Manajer</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}">1</td>
            <td style="{{ $style }}">Lusi Dasar</td>
            <td style="{{ $style }}">Proses</td>
            <td style="{{ $style }}">{{ $lusi ? $lusi->volume_2 : '' }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}">2</td>
            <td style="{{ $style }}">Songket-1</td>
            <td style="{{ $style }}">Proses</td>
            <td style="{{ $style }}">{{ $songket_1 ? $songket_1->volume_2 : '' }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}">3</td>
            <td style="{{ $style }}">Songket-2</td>
            <td style="{{ $style }}">Proses</td>
            <td style="{{ $style }}">{{ $songket_2 ? $songket_2->volume_2 : '' }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}">4</td>
            <td style="{{ $style }}">Songket-3</td>
            <td style="{{ $style }}">Proses</td>
            <td style="{{ $style }}">{{ $songket_3 ? $songket_3->volume_2 : '' }}</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="14">Realisasi Pemakaian Benang Lusi &amp; Pakan, sesuai Kartu Beam terkait:</td>
        </tr>
        <tr style="{{ $style . $bold . $yellow }}">
            <td style="{{ $style }}">No.</td>
            <td style="{{ $style }}">Item</td>
            <td style="{{ $style }}" colspan="2">Diskripsi</td>
            <td style="{{ $style }}">Kg</td>
            <td style="{{ $style }}" colspan="2">Item</td>
            <td style="{{ $style }}" colspan="2">Diskripsi</td>
            <td style="{{ $style }}">Kg</td>
            <td style="{{ $style }}" colspan="2">Sign KASI</td>
            <td style="{{ $style }}" colspan="2">Sign Mgr</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}" rowspan="3">1</td>
            <td style="{{ $style }}" rowspan="3">Lusi Dasar</td>
            <td style="{{ $style }}" colspan="2">Ambil, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3">Songket-1</td>
            <td style="{{ $style }}" colspan="2">Ambil, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Retur, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2">Retur, Kg</td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Konsumsi</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2">Konsumsi</td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}" rowspan="3">2</td>
            <td style="{{ $style }}" rowspan="3">Songket-2</td>
            <td style="{{ $style }}" colspan="2">Ambil, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3">Songket-3</td>
            <td style="{{ $style }}" colspan="2">Ambil, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Retur, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2">Retur, Kg</td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Konsumsi</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2">Konsumsi</td>
            <td style="{{ $style }}"></td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" rowspan="4">3</td>
            <td style="{{ $style }}" colspan="13">Pemakaian Benang Pakan</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" rowspan="3">Pemakaian Bng Pakan</td>
            <td style="{{ $style }}" colspan="2">Ambil, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
            <td style="{{ $style }}" colspan="2" rowspan="3"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Retur, Kg</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}" colspan="2">Konsumsi</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
            <td style="{{ $style . $arsir }}"></td>
        </tr>
        <tr style="{{ $style . $bold }}">
            <td style="{{ $style }}" colspan="14">Realisasi Produksi Tenun sesuai dengan Kartu Beam &amp; BPHT:</td>
        </tr>
        <tr style="{{ $style . $bold . $yellow }}">
            <td style="{{ $style }}" rowspan="2">No</td>
            <td style="{{ $style }}" rowspan="2">Item</td>
            <td style="{{ $style }}" colspan="2">Tanggal</td>
            <td style="{{ $style }}" rowspan="2">Total Pcs:</td>
            <td style="{{ $style }}" colspan="3">Kualitas</td>
            <td style="{{ $style }}" colspan="2" rowspan="2">Keterangan</td>
            <td style="{{ $style }}" colspan="4">Tandatangan Pengendali:</td>
        </tr>
        <tr style="{{ $style . $bold . $yellow }}">
            <td style="{{ $style }}">Start</td>
            <td style="{{ $style }}">Finish</td>
            <td style="{{ $style }}">A</td>
            <td style="{{ $style }}">B</td>
            <td style="{{ $style }}">C</td>
            <td style="{{ $style }}" colspan="2">KASI</td>
            <td style="{{ $style }}" colspan="2">Manajer</td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style . $bold }}">1</td>
            <td style="{{ $style }}">Minggu I</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">2</td>
            <td style="{{ $style }}">Minggu II</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">3</td>
            <td style="{{ $style }}">Minggu III</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">4</td>
            <td style="{{ $style }}">Minggu IV</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">5</td>
            <td style="{{ $style }}">Minggu V</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">6</td>
            <td style="{{ $style }}">Minggu VI</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">7</td>
            <td style="{{ $style }}">Minggu VII</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
            <td style="{{ $style }}" colspan="2" rowspan="2"></td>
        </tr>
        <tr style="{{ $style }}">
            <td style="{{ $style }}">8</td>
            <td style="{{ $style }}">Minggu VIII</td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}"></td>
            <td style="{{ $style }}" colspan="2"></td>
        </tr>
    </tbody>
</table>
