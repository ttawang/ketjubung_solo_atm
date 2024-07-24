<title>{{ $title }}</title>
<link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
<link rel="shortcut icon" href="{{ asset('img/favicon-white.ico') }}">
<style>
    table,
    th,
    td {
        border: 1px solid black;
        padding: 5px;
    }
</style>

<table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDyeingDetail" style="width:100%;">
    <thead>
        <tr>
            <th colspan="9" style="background-color: black; color: white; padding: 5px;">KARTU INSTRUKSI KERJA DYEING
                PROCESS (KIKD)</th>
        </tr>
        <tr>
            <th colspan="9" align="center" style="padding: 5px;">KIKD No.
                <u>{{ $data->count() > 0 ? $data[0]->relDyeing()->value('no_kikd') : '' }}</u>
            </th>
        </tr>
        <tr>
            <th rowspan="3" width="30px">No</th>
            <th rowspan="3">Jenis Benang</th>
            <th rowspan="3">Warna</th>
            <th colspan="6">Proses Produksi</th>
        </tr>
        <tr>
            <th colspan="2">Softcone</th>
            <th colspan="2">Dye & Dry</th>
            <th colspan="2">Over Kelos</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Pcs/Kg</th>
            <th>Tanggal</th>
            <th>Pcs/Kg</th>
            <th>Tanggal</th>
            <th>Pcs/Kg</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->relBarang()->value('name') }}</td>
                <td>{{ $item->relWarna()->value('name') }}</td>
                <td>{{ App\Helpers\Date::format($item->tanggal_softcone, 105) }}</td>
                <td>{{ $item->volume_softcone_1 . '/' . $item->volume_softcone_2 }}</td>
                <td>{{ App\Helpers\Date::format($item->tanggal_dyeoven, 105) }}</td>
                <td>{{ $item->volume_dyeoven_1 . '/' . $item->volume_dyeoven_2 }}</td>
                <td>{{ App\Helpers\Date::format($item->tanggal_overcone, 105) }}</td>
                <td>{{ $item->volume_overcone_1 . '/' . $item->volume_overcone_2 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9"></td>
            </tr>
        @endforelse
    </tbody>
</table>
