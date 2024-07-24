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
@php
    $withWarna = [2, 3, 4, 5, 6, 11, 12, 13, 14, 15, 16, 20, 21];
    $withKikw = [5, 6, 11, 12, 15, 16]; /* no_kikw */
    $withMesin = [5, 6, 11, 12, 13, 14, 15, 16]; /* nomor mesin */
    $withBeam = [5, 11, 15]; /* no beam */
    $withCatatan = [2]; /* no beam */
    $warna = false;
    $kikw = false;
    $mesin = false;
    $beam = false;
    $catatan = false;
    $addCol = 0;
    if (in_array($data->id_tipe_pengiriman, $withWarna)) {
        $warna = true;
        $addCol += 1;
    }
    if (in_array($data->id_tipe_pengiriman, $withKikw)) {
        $kikw = true;
        $addCol += 1;
    }
    if (in_array($data->id_tipe_pengiriman, $withMesin)) {
        $mesin = true;
        $addCol += 1;
    }
    if (in_array($data->id_tipe_pengiriman, $withBeam)) {
        $beam = true;
        $addCol += 1;
    }
    if (in_array($data->id_tipe_pengiriman, $withCatatan)) {
        $catatan = true;
        $addCol += 1;
    }
@endphp

@if ($checkNullVolume2)
    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tablePengirimanBarang"
        style="width:100%;">
        <thead>
            <tr>
                <th colspan="{{ $addCol != 0 ? 5 + $addCol : 5 }}"
                    style="background-color: black; color: white; padding: 5px;">
                    {{ strtoupper($data->nama_tipe_pengiriman) }}</th>
            </tr>
            <tr>
                <td colspan="{{ $addCol != 0 ? 3 + $addCol : 3 }}" align="left">Nomor : {{ $data->nomor }}</td>
                <td align="left" colspan="2">Tanggal : {{ App\Helpers\Date::format($data->tanggal, 105) }}</td>
            </tr>
            <tr>
                <th width="50px">No.</th>
                @if ($catatan)
                    <th>Catatan</th>
                @endif
                <th colspan="2">Jenis & Nomor Benang</th>
                @if ($mesin)
                    <th>No. MC</th>
                @endif
                @if ($warna)
                    <th>Warna</th>
                @endif
                @if ($kikw)
                    <th>KIKW/KIKS</th>
                @endif
                @if ($beam)
                    <th>No. Beam</th>
                @endif
                <th>Satuan</th>
                <th>Volume</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data->relPengirimanDetail as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    @if ($catatan)
                        <td>{{ $item->catatan }}</td>
                    @endif
                    <td colspan="2">{{ $item->relBarang()->value('name') }}</td>
                    @if ($mesin)
                        <td>{{ $item->nama_mesin }}</td>
                    @endif
                    @if ($warna)
                        <td>{{ $item->relWarna()->value('alias') }}</td>
                    @endif
                    @if ($kikw)
                        <td>{{ $item->no_kikw }}</td>
                    @endif
                    @if ($beam)
                        <td>{{ $item->no_beam }}</td>
                    @endif
                    <td>{{ $item->relSatuan1()->value('name') }}</td>
                    <td align="right">{{ $item->volume_1 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $addCol != 0 ? 5 + $addCol : 5 }}"></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th align="center" colspan="{{ $addCol != 0 ? 4 + $addCol : 4 }}">T O T A L</th>
                <th align="right">{{ $data->relPengirimanDetail->sum('volume_1') }}</th>
            </tr>
            <tr>
                <td rowspan="2" colspan="{{ $addCol != 0 ? 2 + $addCol : 2 }}"
                    style="vertical-align: text-top; width: 200px;">NOTE
                    :
                    {{ $data->catatan }}</th>
                <th align="center">Penerima : </th>
                <th align="center">Penyerah : </th>
                <th align="center">Mengetahui : </th>
            </tr>
            <tr>
                <td><br><br><br><br></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@else
    <table class="table table-bordered table-hover table-striped" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th colspan="{{ $addCol != 0 ? 8 + $addCol : 8 }}"
                    style="background-color: black; color: white; padding: 5px;">
                    {{ strtoupper($data->nama_tipe_pengiriman) }}</th>
            </tr>
            <tr>
                <td colspan="3" align="left">Nomor : {{ $data->nomor }}</td>
                <th colspan="3"></th>
                <td align="left" colspan="{{ $addCol != 0 ? 2 + $addCol : 2 }}">Tanggal :
                    {{ App\Helpers\Date::format($data->tanggal, 105) }}</td>
            </tr>
            <tr>
                <th width="50px">No.</th>
                @if ($catatan)
                    <th>Catatan</th>
                @endif
                <th colspan="3">Jenis & Nomor Benang</th>
                @if ($mesin)
                    <th>No. MC</th>
                @endif
                @if ($warna)
                    <th>Warna</th>
                @endif
                @if ($kikw)
                    <th>KIKW/KIKS</th>
                @endif
                @if ($beam)
                    <th>No. Beam</th>
                @endif
                <th>Satuan 1</th>
                <th>Volume 1</th>
                <th>Satuan 2</th>
                <th>Volume 2</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data->relPengirimanDetail as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    @if ($catatan)
                        <td>{{ $item->catatan }}</td>
                    @endif
                    <td colspan="3">{{ $item->relBarang()->value('name') }}</td>
                    @if ($mesin)
                        <td>{{ $item->nama_mesin }}</td>
                    @endif
                    @if ($warna)
                        <td>{{ $item->relWarna()->value('alias') }}</td>
                    @endif
                    @if ($kikw)
                        <td>{{ $item->no_kikw }}</td>
                    @endif
                    @if ($beam)
                        <td>{{ $item->no_beam }}</td>
                    @endif
                    <td>{{ $item->relSatuan1()->value('name') }}</td>
                    <td align="right">{{ $item->volume_1 }}</td>
                    <td>{{ $item->relSatuan2()->value('name') }}</td>
                    <td align="right">{{ $item->volume_2 }}</td>\
                </tr>
            @empty
                <tr>
                    <td colspan="8"></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th align="center" colspan="{{ $addCol != 0 ? 5 + $addCol : 5 }}">T O T A L</th>
                <th align="right">{{ $data->relPengirimanDetail->sum('volume_1') }}</th>
                <td></td>
                <th align="right">{{ $data->relPengirimanDetail->sum('volume_2') }}</th>
            </tr>
            <tr>
                <td rowspan="2" colspan="{{ $addCol != 0 ? 2 + $addCol : 2 }}"
                    style="vertical-align: text-top; width: 200px;">NOTE
                    :
                    {{ $data->catatan }}</th>
                <th align="center" colspan="2">Penerima : </th>
                <th align="center" colspan="2">Penyerah : </th>
                <th align="center" colspan="2">Mengetahui : </th>
            </tr>
            <tr>
                <td colspan="2"><br><br><br><br></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endif
