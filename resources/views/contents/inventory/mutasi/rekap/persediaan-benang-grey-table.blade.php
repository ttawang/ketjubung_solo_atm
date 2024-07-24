@php
    $addStyle = $cetak ? 'border: 1px solid black;' : '';
@endphp
<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center'{{ $addStyle }}">NO.</th>
            <th rowspan="2" style="text-align: center'{{ $addStyle }}">BARANG</th>
            <th rowspan="1" colspan="3" style="text-align: center'{{ $addStyle }}">SALDO AWAL</th>
            <th rowspan="1" colspan="3" style="text-align: center'{{ $addStyle }}">MASUK</th>
            <th rowspan="1" colspan="3" style="text-align: center'{{ $addStyle }}">KELUAR</th>
            <th rowspan="1" colspan="3" style="text-align: center'{{ $addStyle }}">SALDO AKHIR</th>
        </tr>
        <tr>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">GUDANG</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 1</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 2</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">GUDANG</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 1</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 2</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">GUDANG</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 1</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 2</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 1</th>
            <th rowspan="1" style="text-align: center'{{ $addStyle }}">VOLUME 2</th>
        </tr>
    </thead>
    <tbody>
        @php
            function hitung($arr, $satuan, $nilai)
            {
                if ($satuan == 1) {
                    $arr['cones'] += $nilai;
                }
                if ($satuan == 2) {
                    $arr['kg'] += $nilai;
                }
                if ($satuan == 3) {
                    $arr['beam'] += $nilai;
                }
                if ($satuan == 4) {
                    $arr['pcs'] += $nilai;
                }
                if ($satuan == 5) {
                    $arr['gram'] += $nilai;
                }
                if ($satuan == 6) {
                    $arr['meter'] += $nilai;
                }

                return $arr;
            }
            function get_last_total($satuan, $arr)
            {
                $nilai = 0;
                if ($satuan == 1) {
                    $nilai = $arr['cones'];
                }
                if ($satuan == 2) {
                    $nilai = $arr['kg'];
                }
                if ($satuan == 3) {
                    $nilai = $arr['beam'];
                }
                if ($satuan == 4) {
                    $nilai = $arr['pcs'];
                }
                if ($satuan == 5) {
                    $nilai = $arr['gram'];
                }
                if ($satuan == 6) {
                    $nilai = $arr['meter'];
                }
                return $nilai;
            }
            function texttotal(array $data)
            {
                $result = [];

                foreach ($data as $key => $value) {
                    if ($value !== 0) {
                        $result[] = "$value $key";
                    }
                }

                return implode(' | ', $result);
            }
            $no = 1;
            $current_barang = null;
            $volume_1 = [
                'cones' => 0,
                'kg' => 0,
                'beam' => 0,
                'pcs' => 0,
                'gram' => 0,
                'meter' => 0,
            ];
            $volume_2 = [
                'cones' => 0,
                'kg' => 0,
                'beam' => 0,
                'pcs' => 0,
                'gram' => 0,
                'meter' => 0,
            ];
        @endphp
        @foreach ($data as $i)
            @if ($current_barang == null)
                @php
                    $current_barang = $i->id_barang;
                    $volume_1 = hitung($volume_1, $i->saldo_awal_id_satuan_1, $i->saldo_awal_volume_1 + $i->masuk_volume_1 - $i->keluar_volume_1);
                    $volume_2 = hitung($volume_2, $i->saldo_awal_id_satuan_2, $i->saldo_awal_volume_2 + $i->masuk_volume_2 - $i->keluar_volume_2);
                @endphp
                <tr>
                    <td style="{{ $addStyle }}">{{ $no }}</td>
                    <td style="{{ $addStyle }}">{{ $i->nama_barang }}</td>
                    <td style="{{ $addStyle }}">{{ $i->saldo_awal_nama_gudang }}</td>
                    <td style="{{ $addStyle }}">{{ $i->saldo_awal_volume_1 ? $i->saldo_awal_volume_1 . ' ' . $i->saldo_awal_nama_satuan_1 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ $i->saldo_awal_volume_2 ? $i->saldo_awal_volume_2 . ' ' . $i->saldo_awal_nama_satuan_2 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ $i->masuk_nama_gudang }}</td>
                    <td style="{{ $addStyle }}">{{ $i->masuk_volume_1 ? $i->masuk_volume_1 . ' ' . $i->masuk_nama_satuan_1 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ $i->masuk_volume_2 ? $i->masuk_volume_2 . ' ' . $i->masuk_nama_satuan_2 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ $i->keluar_nama_gudang }}</td>
                    <td style="{{ $addStyle }}">{{ $i->keluar_volume_1 ? $i->keluar_volume_1 . ' ' . $i->keluar_nama_satuan_1 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ $i->keluar_volume_2 ? $i->keluar_volume_2 . ' ' . $i->keluar_nama_satuan_2 : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ texttotal($volume_1) != '' ? texttotal($volume_1) : 0 }}</td>
                    <td style="{{ $addStyle }}">{{ texttotal($volume_2) != '' ? texttotal($volume_2) : 0 }}</td>
                </tr>
                @php
                    $no++;
                @endphp
            @else
                @if ($current_barang == $i->id_barang)
                    @php
                        $volume_1 = hitung($volume_1, $i->saldo_awal_id_satuan_1, get_last_total($i->saldo_awal_id_satuan_1, $volume_1) - $i->keluar_volume_1);
                        $volume_1 = hitung($volume_2, $i->saldo_awal_id_satuan_2, get_last_total($i->saldo_awal_id_satuan_2, $volume_2) - $i->keluar_volume_2);
                    @endphp
                    <tr>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}">{{ $i->keluar_volume_1 ? $i->keluar_volume_1 . ' ' . $i->keluar_nama_satuan_1 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->keluar_volume_2 ? $i->keluar_volume_2 . ' ' . $i->keluar_nama_satuan_2 : 0 }}</td>
                        <td style="{{ $addStyle }}"></td>
                        <td style="{{ $addStyle }}"></td>
                    </tr>
                @else
                    @php
                        $current_barang = $i->id_barang;
                        $volume_1 = [
                            'cones' => 0,
                            'kg' => 0,
                            'beam' => 0,
                            'pcs' => 0,
                            'gram' => 0,
                            'meter' => 0,
                        ];
                        $volume_2 = [
                            'cones' => 0,
                            'kg' => 0,
                            'beam' => 0,
                            'pcs' => 0,
                            'gram' => 0,
                            'meter' => 0,
                        ];
                        $volume_1 = hitung($volume_1, $i->saldo_awal_id_satuan_1, $i->saldo_awal_volume_1 + $i->masuk_volume_1 - $i->keluar_volume_1);
                        $volume_2 = hitung($volume_2, $i->saldo_awal_id_satuan_2, $i->saldo_awal_volume_2 + $i->masuk_volume_2 - $i->keluar_volume_2);
                    @endphp
                    <tr>
                        <td style="{{ $addStyle }}">{{ $no }}</td>
                        <td style="{{ $addStyle }}">{{ $i->nama_barang }}</td>
                        <td style="{{ $addStyle }}">{{ $i->saldo_awal_nama_gudang }}</td>
                        <td style="{{ $addStyle }}">{{ $i->saldo_awal_volume_1 ? $i->saldo_awal_volume_1 . ' ' . $i->saldo_awal_nama_satuan_1 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->saldo_awal_volume_2 ? $i->saldo_awal_volume_2 . ' ' . $i->saldo_awal_nama_satuan_2 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->masuk_nama_gudang }}</td>
                        <td style="{{ $addStyle }}">{{ $i->masuk_volume_1 ? $i->masuk_volume_1 . ' ' . $i->masuk_nama_satuan_1 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->masuk_volume_2 ? $i->masuk_volume_2 . ' ' . $i->masuk_nama_satuan_2 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->keluar_nama_gudang }}</td>
                        <td style="{{ $addStyle }}">{{ $i->keluar_volume_1 ? $i->keluar_volume_1 . ' ' . $i->keluar_nama_satuan_1 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ $i->keluar_volume_2 ? $i->keluar_volume_2 . ' ' . $i->keluar_nama_satuan_2 : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ texttotal($volume_1) != '' ? texttotal($volume_1) : 0 }}</td>
                        <td style="{{ $addStyle }}">{{ texttotal($volume_2) != '' ? texttotal($volume_2) : 0 }}</td>
                    </tr>
                    @php
                        $no++;
                    @endphp
                @endif
            @endif
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#table').DataTable({
            ordering: false
        });
    });
</script>
