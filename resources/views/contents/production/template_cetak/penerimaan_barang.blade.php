<html>

<head>
    <title>{{ $title }}</title>
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon-white.ico') }}">
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        table #tablePenerimaanBarang,
        .table thead tr th,
        .table thead tr td,
        .table tbody tr td,
        tfoot tr th,
        tfoot tr td,
        {
        border: 1px solid black;
        padding: 5px;
        }

        .underline {
            text-decoration: underline;
            text-align: left
        }

        .header {
            position: absolute;
            top: -20px;
            left: 0px;
            right: 0px;
            font-size: 20px;
            font-style: bold;
        }
    </style>
</head>

<body>
    <span class="header">CV.KETJUBUNG</span>

    <center>
        <h1><b>TANDA TERIMA BARANG</b></h1>
    </center>

    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <th></th>
            <th></th>
            <th colspan="2" width="150px"></th>
            <th align="left">DITERIMA DARI</th>
            <th class="underline">: {{ $data->relSupplier()->value('name') }}</th>
        </tr>
        <tr>
            <th align="left">TANGGAL</th>
            <th align="left">: &nbsp;{{ App\Helpers\Date::format($data->tanggal_terima, 0) }}</th>
            <th colspan="2" width="150px"></th>
            <th align="left">NO. KENDARAAN</th>
            <th class="underline">: {{ $data->no_kendaraan ?? '-' }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th colspan="2" width="150px"></th>
            <th align="left">NAMA SUPIR</th>
            <th class="underline">: {{ $data->supir ?? '-' }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th colspan="2" width="150px"></th>
            <th align="left">NO. TTBM</th>
            <th class="underline" style="color: red">: {{ $data->no_ttbm ?? '-' }}</th>
        </tr>
        <tr>
            <td align="left" colspan="6">Dengan ini kami menyatakan bahwa kami telah menerima sejumlah barang dalam
                kondisi baik
                dengan jumlah dan deskripsi sebagai berikut : </th>
        </tr>
    </table>

    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tablePenerimaanBarang"
        style="width:100%;">
        <thead>
            <tr>
                <th>NO URUT</th>
                <th colspan="2">KETERANGAN</th>
                <th>SATUAN</th>
                <th>JUMLAH</th>
                <th>CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->relPenerimaanBarangDetail as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td colspan="2">{{ $item->relBarang()->value('name') }}</td>
                    <td>{{ $item->relSatuan1()->value('name') }}</td>
                    <td align="right">{{ $item->volume_1 }}</td>
                    <td></td>
                </tr>
            @endforeach
            @for ($i = 0; $i < abs(count($data->relPenerimaanBarangDetail) - 10); $i++)
                <tr style="border: 1px solid black;">
                    <td>&nbsp;</td>
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
            <tr>
                <td colspan="6" style="border: 0px;"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th align="center">Penyerah : </th>
                <th align="center">Penerima : </th>
                <th align="center">Pembelian : </th>
                <th align="center" colspan="2">Mengetahui : </th>
                <th style="border: 0px;"></th>
            </tr>
            <tr>
                <td><br><br><br><br></td>
                <td></td>
                <td></td>
                <td colspan="2"></td>
                <td style="border: 0px; padding: 10px; font-size: 12pt;">
                    <p><b>PO NO</b> <span class="underline">: {{ $data->no_po }}</span></p>
                    <p><b>TGL</b> <span class="underline">: {{ $data->tanggal_po_custom }}</span></p>
                </td>
            </tr>
        </tfoot>
    </table>
    <table>
        <tr>
            <td colspan="2">NB : Mohon di TT di sertakan Nama Terang dan Tanggal</td>
        </tr>
        <tr>
            <td>Lembar 1 : Pembelian</td>
            <td>Lembar 3 : Accounting</td>
        </tr>
        <tr>
            <td>Lembar 2 : Penerima</td>
            <td>Lembar 4 : Gudang</td>
        </tr>
    </table>
</body>

</html>
