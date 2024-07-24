<table class="table table-bordered table-hover table-striped" cellspacing="0" id="table">
    <thead>
        <tr>
            <th style="text-align: center" rowspan="2">NO</th>
            <th style="text-align: center" rowspan="2">PRODUKSI</th>
            <th style="text-align: center" rowspan="2">NOMOR</th>
            <th style="text-align: center" colspan="2">KELUAR</th>
            <th style="text-align: center" colspan="2">MASUK</th>
        </tr>
        <tr>
            <th style="text-align: center">TANGGAL</th>
            <th style="text-align: center">BARANG</th>
            <th style="text-align: center">TANGGAL</th>
            <th style="text-align: center">BARANG</th>
        </tr>
    </thead>
    <tbody>
        @php
            $arr = [];
            $jmlkeluar = 0;
            $jmlmasuk = 0;
            $spk = null;

            foreach ($data as $i) {
                if ($spk == null) {
                    $spk = $i->id_spk;
                    if ($i->status === 'keluar') {
                        $jmlkeluar += 1;
                        $arr[$i->sort . ',' . $i->id_spk]['keluar'][] = $i;
                    } else {
                        $jmlmasuk += 1;
                        $arr[$i->sort . ',' . $i->id_spk]['masuk'][] = $i;
                    }
                    $arr[$i->sort . ',' . $i->id_spk]['jmlkeluar'] = $jmlkeluar;
                    $arr[$i->sort . ',' . $i->id_spk]['jmlmasuk'] = $jmlmasuk;
                } else {
                    if ($spk == $i->id_spk) {
                        if ($i->status === 'keluar') {
                            $jmlkeluar += 1;
                            $arr[$i->sort . ',' . $i->id_spk]['keluar'][] = $i;
                        } else {
                            $jmlmasuk += 1;
                            $arr[$i->sort . ',' . $i->id_spk]['masuk'][] = $i;
                        }
                        $arr[$i->sort . ',' . $i->id_spk]['jmlkeluar'] = $jmlkeluar;
                        $arr[$i->sort . ',' . $i->id_spk]['jmlmasuk'] = $jmlmasuk;
                    } else {
                        $spk = $i->id_spk;
                        $jmlkeluar = 0;
                        $jmlmasuk = 0;
                        if ($i->status === 'keluar') {
                            $jmlkeluar = 1;
                            $arr[$i->sort . ',' . $i->id_spk]['keluar'][] = $i;
                        } else {
                            $jmlmasuk = 1;
                            $arr[$i->sort . ',' . $i->id_spk]['masuk'][] = $i;
                        }
                        $arr[$i->sort . ',' . $i->id_spk]['jmlkeluar'] = $jmlkeluar;
                        $arr[$i->sort . ',' . $i->id_spk]['jmlmasuk'] = $jmlmasuk;
                    }
                }
            }

            function gettextcode($code, $text)
            {
                $filtercode = ['BHDR', 'BBWS', 'BBPS', 'BGDH', 'P1H', 'FCH', 'P2H', 'BBTLT', 'BBTST', 'DPRT', 'DPST', 'BOT', 'BBTLR', 'BBTSR', 'DPRR', 'DPSR', 'BOR'];
                $textcode = '';
                if (in_array($code, $filtercode)) {
                    $endcode = substr($code, -1);
                    if ($endcode == 'S') {
                        $textcode = 'SISA';
                    }
                    if ($endcode == 'R') {
                        $textcode = 'RETURN';
                    }
                    if ($endcode == 'T') {
                        $textcode = 'TURUN';
                    }
                    if ($endcode == 'H') {
                        $textcode = 'HILANG';
                    }
                    $text = '<span class="badge badge-outline badge-warning">' . $textcode . '</span> ' . $text;
                } else {
                    $text = $text;
                }
                return $text;
            }

            function total($arr, $satuan_1, $satuan_2, $nilai_1, $nilai_2)
            {
                if ($satuan_1 == 1) {
                    $arr['cones'] += $nilai_1;
                }
                if ($satuan_2 == 1) {
                    $arr['cones'] += $nilai_2;
                }
                if ($satuan_1 == 2) {
                    $arr['kg'] += $nilai_1;
                }
                if ($satuan_2 == 2) {
                    $arr['kg'] += $nilai_2;
                }
                if ($satuan_1 == 3) {
                    $arr['beam'] += $nilai_1;
                }
                if ($satuan_2 == 3) {
                    $arr['beam'] += $nilai_2;
                }
                if ($satuan_1 == 4) {
                    $arr['pcs'] += $nilai_1;
                }
                if ($satuan_2 == 4) {
                    $arr['pcs'] += $nilai_2;
                }
                if ($satuan_1 == 5) {
                    $arr['gram'] += $nilai_1;
                }
                if ($satuan_2 == 5) {
                    $arr['gram'] += $nilai_2;
                }
                if ($satuan_1 == 6) {
                    $arr['meter'] += $nilai_1;
                }
                if ($satuan_2 == 6) {
                    $arr['meter'] += $nilai_2;
                }

                return $arr;
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
        @endphp

        @foreach ($arr as $i)
            @php
                $detailkeluar = null;
                $detailmasuk = null;

                $totalkeluar = [
                    'cones' => 0,
                    'kg' => 0,
                    'beam' => 0,
                    'pcs' => 0,
                    'gram' => 0,
                    'meter' => 0,
                ];

                $totalmasuk = [
                    'cones' => 0,
                    'kg' => 0,
                    'beam' => 0,
                    'pcs' => 0,
                    'gram' => 0,
                    'meter' => 0,
                ];
            @endphp
            @if ($i['jmlkeluar'] > $i['jmlmasuk'])
                @for ($j = 0; $j < $i['jmlkeluar']; $j++)
                    @if (isset($i['keluar'][$j]))
                        @php
                            $detailkeluar =
                                ($i['keluar'][$j]->nama_gudang ? $i['keluar'][$j]->nama_gudang : '') .
                                ($i['keluar'][$j]->nomor_kikw ? ' | ' . $i['keluar'][$j]->nomor_kikw : '') .
                                ($i['keluar'][$j]->nomor_beam ? ' | ' . $i['keluar'][$j]->nomor_beam : '') .
                                ($i['keluar'][$j]->nama_barang ? ' | ' . $i['keluar'][$j]->nama_barang : '') .
                                ($i['keluar'][$j]->nama_warna ? ' | ' . $i['keluar'][$j]->nama_warna : '') .
                                ($i['keluar'][$j]->nama_motif ? ' | ' . $i['keluar'][$j]->nama_motif : '') .
                                ($i['keluar'][$j]->nama_mesin ? ' | ' . $i['keluar'][$j]->nama_mesin : '') .
                                ($i['keluar'][$j]->nama_grade ? ' | ' . $i['keluar'][$j]->nama_grade : '') .
                                ($i['keluar'][$j]->nama_kualitas ? ' | ' . $i['keluar'][$j]->nama_kualitas : '') .
                                ($i['keluar'][$j]->volume_1 ? ' | ' . $i['keluar'][$j]->volume_1 . ' ' . $i['keluar'][$j]->nama_satuan_1 : '') .
                                ($i['keluar'][$j]->volume_2 ? ' | ' . $i['keluar'][$j]->volume_2 . ' ' . $i['keluar'][$j]->nama_satuan_2 : '');
                            $totalkeluar = total($totalkeluar, $i['keluar'][$j]->id_satuan_1, $i['keluar'][$j]->id_satuan_2, $i['keluar'][$j]->volume_1, $i['keluar'][$j]->volume_2);
                        @endphp
                        <tr>
                            <td>{{ $no }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $i['keluar'][$j]->proses)) }}</td>
                            <td>{{ /* $i['keluar'][$j]->id_spk . ' | ' .  */ $i['keluar'][$j]->nomor }}</td>
                            <td>{{ tglCustom($i['keluar'][$j]->tanggal) }}</td>
                            @php
                                $detailkeluar = gettextcode($i['keluar'][$j]->code, $detailkeluar);
                            @endphp
                            <td>{!! $detailkeluar !!}</td>
                            @if (isset($i['masuk'][$j]))
                                @php
                                    $detailmasuk =
                                        ($i['masuk'][$j]->nama_gudang ? $i['masuk'][$j]->nama_gudang : '') .
                                        ($i['masuk'][$j]->nomor_kikw ? ' | ' . $i['masuk'][$j]->nomor_kikw : '') .
                                        ($i['masuk'][$j]->nomor_beam ? ' | ' . $i['masuk'][$j]->nomor_beam : '') .
                                        ($i['masuk'][$j]->nama_barang ? ' | ' . $i['masuk'][$j]->nama_barang : '') .
                                        ($i['masuk'][$j]->nama_warna ? ' | ' . $i['masuk'][$j]->nama_warna : '') .
                                        ($i['masuk'][$j]->nama_motif ? ' | ' . $i['masuk'][$j]->nama_motif : '') .
                                        ($i['masuk'][$j]->nama_mesin ? ' | ' . $i['masuk'][$j]->nama_mesin : '') .
                                        ($i['masuk'][$j]->nama_grade ? ' | ' . $i['masuk'][$j]->nama_grade : '') .
                                        ($i['masuk'][$j]->nama_kualitas ? ' | ' . $i['masuk'][$j]->nama_kualitas : '') .
                                        ($i['masuk'][$j]->volume_1 ? ' | ' . $i['masuk'][$j]->volume_1 . ' ' . $i['masuk'][$j]->nama_satuan_1 : '') .
                                        ($i['masuk'][$j]->volume_2 ? ' | ' . $i['masuk'][$j]->volume_2 . ' ' . $i['masuk'][$j]->nama_satuan_2 : '');
                                    $totalmasuk = total($totalmasuk, $i['masuk'][$j]->id_satuan_1, $i['masuk'][$j]->id_satuan_2, $i['masuk'][$j]->volume_1, $i['masuk'][$j]->volume_2);
                                @endphp
                                <td>{{ tglCustom($i['masuk'][$j]->tanggal) }}</td>
                                @php
                                    $detailmasuk = gettextcode($i['masuk'][$j]->code, $detailmasuk);
                                @endphp
                                <td>{!! $detailmasuk !!}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                        @php
                            $no++;
                        @endphp
                    @endif
                @endfor
                @php
                    $detailtotalkeluar =
                        ($totalkeluar['pcs'] != 0 ? $totalkeluar['pcs'] . ' pcs ' : '') .
                        ($totalkeluar['kg'] != 0 ? $totalkeluar['kg'] . ' kg ' : '') .
                        ($totalkeluar['beam'] != 0 ? $totalkeluar['beam'] . ' beam ' : '') .
                        ($totalkeluar['pcs'] != 0 ? $totalkeluar['pcs'] . ' pcs ' : '') .
                        ($totalkeluar['gram'] != 0 ? $totalkeluar['gram'] . ' gram ' : '') .
                        ($totalkeluar['meter'] != 0 ? $totalkeluar['meter'] . ' meter ' : '');
                    $detailtotalmasuk =
                        ($totalmasuk['pcs'] != 0 ? $totalmasuk['pcs'] . ' pcs ' : '') .
                        ($totalmasuk['kg'] != 0 ? $totalmasuk['kg'] . ' kg ' : '') .
                        ($totalmasuk['beam'] != 0 ? $totalmasuk['beam'] . ' beam ' : '') .
                        ($totalmasuk['pcs'] != 0 ? $totalmasuk['pcs'] . ' pcs ' : '') .
                        ($totalmasuk['gram'] != 0 ? $totalmasuk['gram'] . ' gram ' : '') .
                        ($totalmasuk['meter'] != 0 ? $totalmasuk['meter'] . ' meter ' : '');
                @endphp
                <tr style="background-color: rgb(206, 240, 240)">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b>{{ texttotal($totalkeluar) }}</b></td>
                    <td><b>Total</b></td>
                    <td><b>{{ texttotal($totalmasuk) }}</b></td>
                </tr>
            @else
                @for ($j = 0; $j < $i['jmlmasuk']; $j++)
                    @if (isset($i['masuk'][$j]))
                        @php
                            $detailmasuk =
                                ($i['masuk'][$j]->nama_gudang ? $i['masuk'][$j]->nama_gudang : '') .
                                ($i['masuk'][$j]->nomor_kikw ? ' | ' . $i['masuk'][$j]->nomor_kikw : '') .
                                ($i['masuk'][$j]->nomor_beam ? ' | ' . $i['masuk'][$j]->nomor_beam : '') .
                                ($i['masuk'][$j]->nama_barang ? ' | ' . $i['masuk'][$j]->nama_barang : '') .
                                ($i['masuk'][$j]->nama_warna ? ' | ' . $i['masuk'][$j]->nama_warna : '') .
                                ($i['masuk'][$j]->nama_motif ? ' | ' . $i['masuk'][$j]->nama_motif : '') .
                                ($i['masuk'][$j]->nama_mesin ? ' | ' . $i['masuk'][$j]->nama_mesin : '') .
                                ($i['masuk'][$j]->nama_grade ? ' | ' . $i['masuk'][$j]->nama_grade : '') .
                                ($i['masuk'][$j]->nama_kualitas ? ' | ' . $i['masuk'][$j]->nama_kualitas : '') .
                                ($i['masuk'][$j]->volume_1 ? ' | ' . $i['masuk'][$j]->volume_1 . ' ' . $i['masuk'][$j]->nama_satuan_1 : '') .
                                ($i['masuk'][$j]->volume_2 ? ' | ' . $i['masuk'][$j]->volume_2 . ' ' . $i['masuk'][$j]->nama_satuan_2 : '');
                            $totalmasuk = total($totalmasuk, $i['masuk'][$j]->id_satuan_1, $i['masuk'][$j]->id_satuan_2, $i['masuk'][$j]->volume_1, $i['masuk'][$j]->volume_2);
                        @endphp
                        <tr>
                            <td>{{ $no }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $i['masuk'][$j]->proses)) }}</td>
                            <td>{{ /* $i['masuk'][$j]->id_spk . ' | ' .  */ $i['masuk'][$j]->nomor }}</td>
                            @if (isset($i['keluar'][$j]))
                                @php
                                    $detailkeluar =
                                        ($i['keluar'][$j]->nama_gudang ? $i['keluar'][$j]->nama_gudang : '') .
                                        ($i['keluar'][$j]->nomor_kikw ? ' | ' . $i['keluar'][$j]->nomor_kikw : '') .
                                        ($i['keluar'][$j]->nomor_beam ? ' | ' . $i['keluar'][$j]->nomor_beam : '') .
                                        ($i['keluar'][$j]->nama_barang ? ' | ' . $i['keluar'][$j]->nama_barang : '') .
                                        ($i['keluar'][$j]->nama_warna ? ' | ' . $i['keluar'][$j]->nama_warna : '') .
                                        ($i['keluar'][$j]->nama_motif ? ' | ' . $i['keluar'][$j]->nama_motif : '') .
                                        ($i['keluar'][$j]->nama_mesin ? ' | ' . $i['keluar'][$j]->nama_mesin : '') .
                                        ($i['keluar'][$j]->nama_grade ? ' | ' . $i['keluar'][$j]->nama_grade : '') .
                                        ($i['keluar'][$j]->nama_kualitas ? ' | ' . $i['keluar'][$j]->nama_kualitas : '') .
                                        ($i['keluar'][$j]->volume_1 ? ' | ' . $i['keluar'][$j]->volume_1 . ' ' . $i['keluar'][$j]->nama_satuan_1 : '') .
                                        ($i['keluar'][$j]->volume_2 ? ' | ' . $i['keluar'][$j]->volume_2 . ' ' . $i['keluar'][$j]->nama_satuan_2 : '');
                                    $totalkeluar = total($totalkeluar, $i['keluar'][$j]->id_satuan_1, $i['keluar'][$j]->id_satuan_2, $i['keluar'][$j]->volume_1, $i['keluar'][$j]->volume_2);
                                @endphp
                                <td>{{ tglCustom($i['keluar'][$j]->tanggal) }}</td>
                                @php
                                    $detailkeluar = gettextcode($i['keluar'][$j]->code, $detailkeluar);
                                @endphp
                                <td>{!! $detailkeluar !!}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                            <td>{{ tglCustom($i['masuk'][$j]->tanggal) }}</td>
                            @php
                                $detailmasuk = gettextcode($i['masuk'][$j]->code, $detailmasuk);
                            @endphp
                            <td>{!! $detailmasuk !!}</td>
                        </tr>
                    @endif
                    @php
                        $no++;
                    @endphp
                @endfor
                @php
                    $detailtotalkeluar =
                        ($totalkeluar['cones'] != 0 ? $totalkeluar['cones'] . ' cones ' : '') .
                        ($totalkeluar['kg'] != 0 ? $totalkeluar['kg'] . ' kg ' : '') .
                        ($totalkeluar['beam'] != 0 ? $totalkeluar['beam'] . ' beam ' : '') .
                        ($totalkeluar['pcs'] != 0 ? $totalkeluar['pcs'] . ' pcs ' : '') .
                        ($totalkeluar['gram'] != 0 ? $totalkeluar['gram'] . ' gram ' : '') .
                        ($totalkeluar['meter'] != 0 ? $totalkeluar['meter'] . ' meter ' : '');
                    $detailtotalmasuk =
                        ($totalmasuk['cones'] != 0 ? $totalmasuk['cones'] . ' cones ' : '') .
                        ($totalmasuk['kg'] != 0 ? $totalmasuk['kg'] . ' kg ' : '') .
                        ($totalmasuk['beam'] != 0 ? $totalmasuk['beam'] . ' beam ' : '') .
                        ($totalmasuk['pcs'] != 0 ? $totalmasuk['pcs'] . ' pcs ' : '') .
                        ($totalmasuk['gram'] != 0 ? $totalmasuk['gram'] . ' gram ' : '') .
                        ($totalmasuk['meter'] != 0 ? $totalmasuk['meter'] . ' meter ' : '');
                @endphp
                <tr style="background-color: rgb(206, 240, 240)">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b>{{ texttotal($totalkeluar) }}</b></td>
                    <td><b>Total</b></td>
                    <td><b>{{ texttotal($totalmasuk) }}</b></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#table').DataTable({
            searching: false,
            ordering: false,
            "pageLength": 50
        });
    });
</script>
