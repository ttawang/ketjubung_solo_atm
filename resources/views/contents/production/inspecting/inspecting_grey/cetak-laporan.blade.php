@php
    $addStyle = 'border: 1px solid black;';
    $yellow = 'background-color: yellow';
@endphp
<table class="table table-bordered table-responsive nowrap" cellspacing="0" id="table">
    <thead>
        <tr>
            <th style="text-align: center;{{ $addStyle }}" colspan="45">LKSG (LAPORAN KUALITAS SARUNG GREY) - Rekap
                Hasil Potong {{ tglIndo($tgl) }}</th>
        </tr>
        <tr>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">No. Mesin</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Jenis Mesin</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">KIKW</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Motif</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Warna</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Group</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Potongan</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="3">Grade Kualitas</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="11">B = Cacat Ringan ( Minor Defect )</th>
            <th style="text-align: center;{{ $addStyle }}" colspan="22">C = Cacat Berat ( Major Defect )</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Panjang Sarung</th>
            <th style="text-align: center;{{ $addStyle }}" rowspan="2">Keterangan</th>
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
                'id_inspecting_grey' => null,
                'id_beam' => null,
                'id_mesin' => null,
                'id_motif' => null,
                'id_warna' => null,
                'id_barang' => null,
            ];
            $total = [
                'total_jumlah' => 0,
                'total_grade_a' => 0,
                'total_grade_b' => 0,
                'total_grade_c' => 0,
                'total_kualitas_1' => 0,
                'total_kualitas_2' => 0,
                'total_kualitas_3' => 0,
                'total_kualitas_4' => 0,
                'total_kualitas_5' => 0,
                'total_kualitas_6' => 0,
                'total_kualitas_7' => 0,
                'total_kualitas_8' => 0,
                'total_kualitas_9' => 0,
                'total_kualitas_10' => 0,
                'total_kualitas_11' => 0,
                'total_kualitas_12' => 0,
                'total_kualitas_13' => 0,
                'total_kualitas_14' => 0,
                'total_kualitas_15' => 0,
                'total_kualitas_16' => 0,
                'total_kualitas_17' => 0,
                'total_kualitas_18' => 0,
                'total_kualitas_19' => 0,
                'total_kualitas_20' => 0,
                'total_kualitas_21' => 0,
                'total_kualitas_22' => 0,
                'total_kualitas_23' => 0,
                'total_kualitas_24' => 0,
                'total_kualitas_25' => 0,
                'total_kualitas_26' => 0,
                'total_kualitas_27' => 0,
                'total_kualitas_28' => 0,
                'total_kualitas_29' => 0,
                'total_kualitas_30' => 0,
                'total_kualitas_31' => 0,
                'total_kualitas_32' => 0,
                'total_kualitas_33' => 0,
            ];
        @endphp
        @foreach ($data as $i)
            @if (
                $lastId['id_inspecting_grey'] == null &&
                    $lastId['id_beam'] == null &&
                    $lastId['id_mesin'] == null &&
                    $lastId['id_motif'] == null &&
                    $lastId['id_warna'] == null &&
                    $lastId['id_barang'] == null)
                @php
                    $mesin = $i->nama_mesin;
                    $jenis_mesin = $i->nama_tipe_mesin;
                    $nomor_kikw = $i->nomor_kikw;
                    $motif = $i->nama_motif;
                    $warna = $i->nama_warna;
                    $barang = $i->nama_barang;
                @endphp
                <tr>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $mesin }}</td>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $jenis_mesin }}</td>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $nomor_kikw }}</td>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $motif }}</td>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $warna }}</td>
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
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $i->panjang_sarung }}
                    </td>
                    <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $i->keterangan }}</td>
                </tr>
                @php
                    $lastId = [
                        'id_inspecting_grey' => $i->id_inspecting_grey,
                        'id_beam' => $i->id_beam,
                        'id_mesin' => $i->id_mesin,
                        'id_motif' => $i->id_motif,
                        'id_warna' => $i->id_warna,
                        'id_barang' => $i->id_barang,
                    ];
                    $total['total_jumlah'] += $i->jumlah;
                    $total['total_grade_a'] += $i->grade_a;
                    $total['total_grade_b'] += $i->grade_b;
                    $total['total_grade_c'] += $i->grade_c;
                    $total['total_kualitas_1'] += $i->kualitas_1;
                    $total['total_kualitas_2'] += $i->kualitas_2;
                    $total['total_kualitas_3'] += $i->kualitas_3;
                    $total['total_kualitas_4'] += $i->kualitas_4;
                    $total['total_kualitas_5'] += $i->kualitas_5;
                    $total['total_kualitas_6'] += $i->kualitas_6;
                    $total['total_kualitas_7'] += $i->kualitas_7;
                    $total['total_kualitas_8'] += $i->kualitas_8;
                    $total['total_kualitas_9'] += $i->kualitas_9;
                    $total['total_kualitas_10'] += $i->kualitas_10;
                    $total['total_kualitas_11'] += $i->kualitas_11;
                    $total['total_kualitas_12'] += $i->kualitas_12;
                    $total['total_kualitas_13'] += $i->kualitas_13;
                    $total['total_kualitas_14'] += $i->kualitas_14;
                    $total['total_kualitas_15'] += $i->kualitas_15;
                    $total['total_kualitas_16'] += $i->kualitas_16;
                    $total['total_kualitas_17'] += $i->kualitas_17;
                    $total['total_kualitas_18'] += $i->kualitas_18;
                    $total['total_kualitas_19'] += $i->kualitas_19;
                    $total['total_kualitas_20'] += $i->kualitas_20;
                    $total['total_kualitas_21'] += $i->kualitas_21;
                    $total['total_kualitas_22'] += $i->kualitas_22;
                    $total['total_kualitas_23'] += $i->kualitas_23;
                    $total['total_kualitas_24'] += $i->kualitas_24;
                    $total['total_kualitas_25'] += $i->kualitas_25;
                    $total['total_kualitas_26'] += $i->kualitas_26;
                    $total['total_kualitas_27'] += $i->kualitas_27;
                    $total['total_kualitas_28'] += $i->kualitas_28;
                    $total['total_kualitas_29'] += $i->kualitas_29;
                    $total['total_kualitas_30'] += $i->kualitas_30;
                    $total['total_kualitas_31'] += $i->kualitas_31;
                    $total['total_kualitas_32'] += $i->kualitas_32;
                    $total['total_kualitas_33'] += $i->kualitas_33;
                @endphp
            @else
                @if (
                    $lastId['id_inspecting_grey'] == $i->id_inspecting_grey &&
                        $lastId['id_beam'] == $i->id_beam &&
                        $lastId['id_mesin'] == $i->id_mesin &&
                        $lastId['id_motif'] == $i->id_motif &&
                        $lastId['id_warna'] == $i->id_warna &&
                        $lastId['id_barang'] == $i->id_barang)
                    @php
                        $mesin = '';
                        $nomor_kikw = '';
                        $motif = '';
                        $warna = '';
                        $barang = '';
                    @endphp
                    <tr>
                        {{-- <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $mesin }}</td>
                        {{-- <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $jenis_mesin }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $nomor_kikw }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $motif }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $warna }}</td> --}}
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
                        {{-- <td style="text-align: center;{{ $addStyle }}"></td>
                        <td style="text-align: center;{{ $addStyle }}"></td> --}}
                    </tr>
                    @php
                        $total['total_jumlah'] += $i->jumlah;
                        $total['total_grade_a'] += $i->grade_a;
                        $total['total_grade_b'] += $i->grade_b;
                        $total['total_grade_c'] += $i->grade_c;
                        $total['total_kualitas_1'] += $i->kualitas_1;
                        $total['total_kualitas_2'] += $i->kualitas_2;
                        $total['total_kualitas_3'] += $i->kualitas_3;
                        $total['total_kualitas_4'] += $i->kualitas_4;
                        $total['total_kualitas_5'] += $i->kualitas_5;
                        $total['total_kualitas_6'] += $i->kualitas_6;
                        $total['total_kualitas_7'] += $i->kualitas_7;
                        $total['total_kualitas_8'] += $i->kualitas_8;
                        $total['total_kualitas_9'] += $i->kualitas_9;
                        $total['total_kualitas_10'] += $i->kualitas_10;
                        $total['total_kualitas_11'] += $i->kualitas_11;
                        $total['total_kualitas_12'] += $i->kualitas_12;
                        $total['total_kualitas_13'] += $i->kualitas_13;
                        $total['total_kualitas_14'] += $i->kualitas_14;
                        $total['total_kualitas_15'] += $i->kualitas_15;
                        $total['total_kualitas_16'] += $i->kualitas_16;
                        $total['total_kualitas_17'] += $i->kualitas_17;
                        $total['total_kualitas_18'] += $i->kualitas_18;
                        $total['total_kualitas_19'] += $i->kualitas_19;
                        $total['total_kualitas_20'] += $i->kualitas_20;
                        $total['total_kualitas_21'] += $i->kualitas_21;
                        $total['total_kualitas_22'] += $i->kualitas_22;
                        $total['total_kualitas_23'] += $i->kualitas_23;
                        $total['total_kualitas_24'] += $i->kualitas_24;
                        $total['total_kualitas_25'] += $i->kualitas_25;
                        $total['total_kualitas_26'] += $i->kualitas_26;
                        $total['total_kualitas_27'] += $i->kualitas_27;
                        $total['total_kualitas_28'] += $i->kualitas_28;
                        $total['total_kualitas_29'] += $i->kualitas_29;
                        $total['total_kualitas_30'] += $i->kualitas_30;
                        $total['total_kualitas_31'] += $i->kualitas_31;
                        $total['total_kualitas_32'] += $i->kualitas_32;
                        $total['total_kualitas_33'] += $i->kualitas_33;
                    @endphp
                @else
                    @php
                        $mesin = $i->nama_mesin;
                        $jenis_mesin = $i->nama_tipe_mesin;
                        $nomor_kikw = $i->nomor_kikw;
                        $motif = $i->nama_motif;
                        $warna = $i->nama_warna;
                        $barang = $i->nama_barang;
                    @endphp
                    <tr style="{{ $yellow }}">
                        <td style="text-align: center;{{ $addStyle . $yellow }}" colspan="6">TOTAL</td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_jumlah'] }}</td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_a'] }}</td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_b'] }}</td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_c'] }}</td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_1'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_2'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_3'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_4'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_5'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_6'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_7'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_8'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_9'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_10'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_11'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_12'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_13'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_14'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_15'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_16'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_17'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_18'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_19'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_20'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_21'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_22'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_23'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_24'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_25'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_26'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_27'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_28'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_29'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_30'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_31'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_32'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_33'] }}
                        </td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}"></td>
                        <td style="text-align: center;{{ $addStyle . $yellow }}"></td>
                    </tr>
                    @php
                        $lastId = [
                            'id_inspecting_grey' => $i->id_inspecting_grey,
                            'id_beam' => $i->id_beam,
                            'id_mesin' => $i->id_mesin,
                            'id_motif' => $i->id_motif,
                            'id_warna' => $i->id_warna,
                            'id_barang' => $i->id_barang,
                        ];
                        $total = [
                            'total_jumlah' => 0,
                            'total_grade_a' => 0,
                            'total_grade_b' => 0,
                            'total_grade_c' => 0,
                            'total_kualitas_1' => 0,
                            'total_kualitas_2' => 0,
                            'total_kualitas_3' => 0,
                            'total_kualitas_4' => 0,
                            'total_kualitas_5' => 0,
                            'total_kualitas_6' => 0,
                            'total_kualitas_7' => 0,
                            'total_kualitas_8' => 0,
                            'total_kualitas_9' => 0,
                            'total_kualitas_10' => 0,
                            'total_kualitas_11' => 0,
                            'total_kualitas_12' => 0,
                            'total_kualitas_13' => 0,
                            'total_kualitas_14' => 0,
                            'total_kualitas_15' => 0,
                            'total_kualitas_16' => 0,
                            'total_kualitas_17' => 0,
                            'total_kualitas_18' => 0,
                            'total_kualitas_19' => 0,
                            'total_kualitas_20' => 0,
                            'total_kualitas_21' => 0,
                            'total_kualitas_22' => 0,
                            'total_kualitas_23' => 0,
                            'total_kualitas_24' => 0,
                            'total_kualitas_25' => 0,
                            'total_kualitas_26' => 0,
                            'total_kualitas_27' => 0,
                            'total_kualitas_28' => 0,
                            'total_kualitas_29' => 0,
                            'total_kualitas_30' => 0,
                            'total_kualitas_31' => 0,
                            'total_kualitas_32' => 0,
                            'total_kualitas_33' => 0,
                        ];
                    @endphp
                    <tr>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $mesin }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $jenis_mesin }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $nomor_kikw }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $motif }}</td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $warna }}</td>
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
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $i->panjang_sarung }}
                        </td>
                        <td style="text-align: center;{{ $addStyle }}" rowspan="3">{{ $i->keterangan }}
                        </td>
                    </tr>
                    @php
                        $total['total_jumlah'] += $i->jumlah;
                        $total['total_grade_a'] += $i->grade_a;
                        $total['total_grade_b'] += $i->grade_b;
                        $total['total_grade_c'] += $i->grade_c;
                        $total['total_kualitas_1'] += $i->kualitas_1;
                        $total['total_kualitas_2'] += $i->kualitas_2;
                        $total['total_kualitas_3'] += $i->kualitas_3;
                        $total['total_kualitas_4'] += $i->kualitas_4;
                        $total['total_kualitas_5'] += $i->kualitas_5;
                        $total['total_kualitas_6'] += $i->kualitas_6;
                        $total['total_kualitas_7'] += $i->kualitas_7;
                        $total['total_kualitas_8'] += $i->kualitas_8;
                        $total['total_kualitas_9'] += $i->kualitas_9;
                        $total['total_kualitas_10'] += $i->kualitas_10;
                        $total['total_kualitas_11'] += $i->kualitas_11;
                        $total['total_kualitas_12'] += $i->kualitas_12;
                        $total['total_kualitas_13'] += $i->kualitas_13;
                        $total['total_kualitas_14'] += $i->kualitas_14;
                        $total['total_kualitas_15'] += $i->kualitas_15;
                        $total['total_kualitas_16'] += $i->kualitas_16;
                        $total['total_kualitas_17'] += $i->kualitas_17;
                        $total['total_kualitas_18'] += $i->kualitas_18;
                        $total['total_kualitas_19'] += $i->kualitas_19;
                        $total['total_kualitas_20'] += $i->kualitas_20;
                        $total['total_kualitas_21'] += $i->kualitas_21;
                        $total['total_kualitas_22'] += $i->kualitas_22;
                        $total['total_kualitas_23'] += $i->kualitas_23;
                        $total['total_kualitas_24'] += $i->kualitas_24;
                        $total['total_kualitas_25'] += $i->kualitas_25;
                        $total['total_kualitas_26'] += $i->kualitas_26;
                        $total['total_kualitas_27'] += $i->kualitas_27;
                        $total['total_kualitas_28'] += $i->kualitas_28;
                        $total['total_kualitas_29'] += $i->kualitas_29;
                        $total['total_kualitas_30'] += $i->kualitas_30;
                        $total['total_kualitas_31'] += $i->kualitas_31;
                        $total['total_kualitas_32'] += $i->kualitas_32;
                        $total['total_kualitas_33'] += $i->kualitas_33;
                    @endphp
                @endif
            @endif
            @if ($loop->last)
                <tr style="{{ $yellow }}">
                    <td style="text-align: center;{{ $addStyle . $yellow }}" colspan="6">TOTAL</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_jumlah'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_a'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_b'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_grade_c'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_1'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_2'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_3'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_4'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_5'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_6'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_7'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_8'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_9'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_10'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_11'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_12'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_13'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_14'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_15'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_16'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_17'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_18'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_19'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_20'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_21'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_22'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_23'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_24'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_25'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_26'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_27'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_28'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_29'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_30'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_31'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_32'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}">{{ $total['total_kualitas_33'] }}</td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}"></td>
                    <td style="text-align: center;{{ $addStyle . $yellow }}"></td>
                </tr>
            @endif
        @endforeach
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
