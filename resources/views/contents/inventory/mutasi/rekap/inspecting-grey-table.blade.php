@php
    $addStyle = $cetak ? 'border: 1px solid black;' : '';
@endphp
<table class="table table-bordered table-responsive nowrap" cellspacing="0" id="table">
    <thead>
        <tr>
            <th style="text-align: center;{{ $addStyle }}" colspan="43">LKSG (LAPORAN KUALITAS SARUNG GREY) - Rekap
                Hasil Potong {{ tglIndo($tgl_awal) }} - {{ tglIndo($tgl_akhir) }}</th>
        </tr>
        <tr>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">No. Mesin</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Jenis</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">KIKW</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Motif</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Warna</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Group</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Potongan</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="3">Grade Kualitas</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="11">B = Cacat Ringan ( Minor Defect )</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="22">C = Cacat Berat ( Major Defect )</th>
        </tr>
        <tr>
            <th style="text-align: center;{{ $addStyle }}">A</th>
            <th style="text-align: center;{{ $addStyle }}">B</th>
            <th style="text-align: center;{{ $addStyle }}">C</th>
            <th style="text-align: center;{{ $addStyle }}">Jl</th>
            <th style="text-align: center;{{ $addStyle }}">FR</th>
            <th style="text-align: center;{{ $addStyle }}">ND</th>
            <th style="text-align: center;{{ $addStyle }}">SK</th>
            <th style="text-align: center;{{ $addStyle }}">TI</th>
            <th style="text-align: center;{{ $addStyle }}">BM</th>
            <th style="text-align: center;{{ $addStyle }}">RT</th>
            <th style="text-align: center;{{ $addStyle }}">TJ</th>
            <th style="text-align: center;{{ $addStyle }}">LP</th>
            <th style="text-align: center;{{ $addStyle }}">JP</th>
            <th style="text-align: center;{{ $addStyle }}">KC</th>

            <th style="text-align: center;{{ $addStyle }}">SB</th>
            <th style="text-align: center;{{ $addStyle }}">SS</th>
            <th style="text-align: center;{{ $addStyle }}">NP</th>
            <th style="text-align: center;{{ $addStyle }}">FB</th>
            <th style="text-align: center;{{ $addStyle }}">TTT</th>
            <th style="text-align: center;{{ $addStyle }}">KKG</th>
            <th style="text-align: center;{{ $addStyle }}">BA</th>
            <th style="text-align: center;{{ $addStyle }}">BD</th>
            <th style="text-align: center;{{ $addStyle }}">PJE</th>
            <th style="text-align: center;{{ $addStyle }}">PNE</th>
            <th style="text-align: center;{{ $addStyle }}">OLI</th>
            <th style="text-align: center;{{ $addStyle }}">SJ</th>
            <th style="text-align: center;{{ $addStyle }}">SF</th>
            <th style="text-align: center;{{ $addStyle }}">SR</th>
            <th style="text-align: center;{{ $addStyle }}">RG</th>
            <th style="text-align: center;{{ $addStyle }}">NDR</th>
            <th style="text-align: center;{{ $addStyle }}">CK</th>
            <th style="text-align: center;{{ $addStyle }}">BL</th>
            <th style="text-align: center;{{ $addStyle }}">JB</th>
            <th style="text-align: center;{{ $addStyle }}">SP</th>
            <th style="text-align: center;{{ $addStyle }}">SM</th>
            <th style="text-align: center;{{ $addStyle }}">KC</th>
        </tr>
    </thead>
    <tbody>
        @php
            $lastId = [
                'id_beam' => null,
                'id_mesin' => null,
                'id_motif' => null,
                'id_warna' => null,
                'id_barang' => null,
            ];
            $total = 0;
            $total_a = 0;
            $total_b = 0;
            $total_c = 0;
            $total_all = 0;
            $total_a_all = 0;
            $total_b_all = 0;
            $total_c_all = 0;
        @endphp
        @foreach ($data as $i)
            @php
                if (
                    $lastId['id_beam'] == null &&
                    $lastId['id_mesin'] == null &&
                    $lastId['id_motif'] == null &&
                    $lastId['id_warna'] == null &&
                    $lastId['id_barang'] == null
                ) {
                    $mesin = $i->nama_mesin;
                    $jenis_mesin = $i->nama_jenis_mesin;
                    $nomor_kikw = $i->nomor_kikw;
                    $motif = $i->nama_motif;
                    $warna = $i->nama_warna;
                    $barang = $i->nama_barang;
                    $lastId = [
                        'id_beam' => $i->id_beam,
                        'id_mesin' => $i->id_mesin,
                        'id_motif' => $i->id_motif,
                        'id_warna' => $i->id_warna,
                        'id_barang' => $i->id_barang,
                    ];
                    $total += $i->jumlah;
                    $total_a += $i->grade_a;
                    $total_b += $i->grade_b;
                    $total_c += $i->grade_c;
                } else {
                    if (
                        $lastId['id_beam'] == $i->id_beam &&
                        $lastId['id_mesin'] == $i->id_mesin &&
                        $lastId['id_motif'] == $i->id_motif &&
                        $lastId['id_warna'] == $i->id_warna &&
                        $lastId['id_barang'] == $i->id_barang
                    ) {
                        $mesin = '';
                        $nomor_kikw = '';
                        $motif = '';
                        $warna = '';
                        $barang = '';
                        $total += $i->jumlah;
                        $total_a += $i->grade_a;
                        $total_b += $i->grade_b;
                        $total_c += $i->grade_c;
                    } else {
                        echo '
                            <tr style="background-color: rgb(206, 240, 240)">
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;">TOTAL</td>
                                <td style="text-align: center;">' .
                            $total .
                            '</td>
                                <td style="text-align: center;">' .
                            $total_a .
                            '</td>
                            <td style="text-align: center;">' .
                            $total_b .
                            '</td>
                            <td style="text-align: center;">' .
                            $total_c .
                            '</td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                            </tr>

                        ';

                        $mesin = $i->nama_mesin;
                        $jenis_mesin = $i->nama_jenis_mesin;
                        $nomor_kikw = $i->nomor_kikw;
                        $motif = $i->nama_motif;
                        $warna = $i->nama_warna;
                        $barang = $i->nama_barang;
                        $lastId = [
                            'id_beam' => $i->id_beam,
                            'id_mesin' => $i->id_mesin,
                            'id_motif' => $i->id_motif,
                            'id_warna' => $i->id_warna,
                            'id_barang' => $i->id_barang,
                        ];
                        $total = $i->jumlah;
                        $total_a = $i->grade_a;
                        $total_b = $i->grade_b;
                        $total_c = $i->grade_c;
                    }
                }
            @endphp
            <tr>
                <td style="text-align: center;{{ $addStyle }}">{{ $mesin }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $jenis_mesin }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $nomor_kikw }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $motif }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $warna }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->nama_group }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->jumlah }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->grade_a }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->grade_b }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->grade_c }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_1 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_2 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_3 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_4 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_5 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_6 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_7 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_8 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_9 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_10 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_11 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_12 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_13 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_14 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_15 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_16 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_17 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_18 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_19 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_20 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_21 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_22 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_23 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_24 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_25 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_26 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_27 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_28 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_29 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_30 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_31 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_32 }}</td>
                <td style="text-align: center;{{ $addStyle }}">{{ $i->kualitas_33 }}</td>
            </tr>
            @if ($loop->last)
                <tr style="background-color: rgb(206, 240, 240)">
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;">TOTAL</td>
                    <td style="text-align: center;">{{ $total }}</td>
                    <td style="text-align: center;">{{ $total_a }}</td>
                    <td style="text-align: center;">{{ $total_b }}</td>
                    <td style="text-align: center;">{{ $total_c }}</td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                </tr>
            @endif
            @php
                $total_all += $i->jumlah;
                $total_a_all += $i->grade_a;
                $total_b_all += $i->grade_b;
                $total_c_all += $i->grade_c;
            @endphp
        @endforeach
        <tr style="background-color: rgb(243, 239, 24)">
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">TOTAL SEMUA</td>
            <td style="text-align: center;">{{ $total_all }}</td>
            <td style="text-align: center;">{{ $total_a_all }}</td>
            <td style="text-align: center;">{{ $total_b_all }}</td>
            <td style="text-align: center;">{{ $total_c_all }}</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
        </tr>
    </tbody>
</table>

<script>
    $(function() {
        $('#table').DataTable({
            ordering: false,
            "pageLength": 50
        });
    });
</script>
