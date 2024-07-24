@php
    $addStyle = $cetak ? 'border: 1px solid black;' : '';
@endphp
<table class="table table-bordered table-striped" cellspacing="0" id="table">
    <thead>
        <tr style="{{ $addStyle }} text-align:center;">
            <th style="{{ $addStyle }}" colspan="12">Persediaan Benang Warna ({{ tglIndo($tgl_awal) }} -
                {{ tglIndo($tgl_akhir) }})
            </th>
        </tr>
        <tr style="{{ $addStyle }} text-align:center;">
            <th style="{{ $addStyle }}" rowspan="3">NO</th>
            <th style="{{ $addStyle }}" rowspan="3">NAMA BARANG</th>
            <th style="{{ $addStyle }}" colspan="2">SALDO AWAL</th>
            <th style="{{ $addStyle }}" colspan="8">PENERIMAAN</th>
            <th style="{{ $addStyle }}" colspan="4">PENGELUARAN</th>
            <th style="{{ $addStyle }}" colspan="2">SISA</th>
        </tr>
        <tr style="{{ $addStyle }} text-align:center;">
            <th style="{{ $addStyle }}" colspan="2">NETTO</th>
            <th style="{{ $addStyle }}" colspan="2">DYEING</th>
            <th style="{{ $addStyle }}" colspan="2">JSL</th>
            <th style="{{ $addStyle }}" colspan="2">WIREHOUSE</th>
            <th style="{{ $addStyle }}" colspan="2">RETUR</th>
            <th style="{{ $addStyle }}" colspan="2">LUSI/SONGKET</th>
            <th style="{{ $addStyle }}" colspan="2">PAKAN</th>
            <th style="{{ $addStyle }}" colspan="2">NETTO</th>

        <tr style="{{ $addStyle }} text-align:center;">
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
            <th style="{{ $addStyle }}">CONES</th>
            <th style="{{ $addStyle }}">KG</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $i)
            <tr style="{{ $addStyle }}">
                <td style="{{ $addStyle }} text-align:center;">{{ $no }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->nama_barang }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->sa_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->sa_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->dyeing_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->dyeing_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->jsl_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->jsl_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->wh_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->wh_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->rt_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->rt_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->warping_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->warping_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->pakan_cones }}</td>
                <td style="{{ $addStyle }} text-align:right;">{{ $i->pakan_kg }}</td>
                <td style="{{ $addStyle }} text-align:right;">
                    {{ $i->sa_cones + $i->dyeing_cones + $i->jsl_cones + $i->wh_cones + $i->rt_cones - $i->warping_cones - $i->pakan_cones }}
                </td>
                <td style="{{ $addStyle }} text-align:right;">
                    {{ $i->sa_kg + $i->dyeing_kg + $i->jsl_kg + $i->wh_kg + $i->rt_kg - $i->warping_kg - $i->pakan_kg }}
                </td>
            </tr>
            @php
                $no++;
            @endphp
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
