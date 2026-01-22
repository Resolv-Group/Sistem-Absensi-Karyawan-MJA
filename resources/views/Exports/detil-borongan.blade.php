<table border="1" cellspacing="0" cellpadding="4" width="100%">

    {{-- HEADER --}}
    <tr>
        <td colspan="13" align="center" style="font-weight:bold; font-size:16px;">
            PT. MITRA JUA ABADI
        </td>
    </tr>
    <tr>
        <td colspan="13" align="center">
            SUMMARY UPAH<br>
            PERIODE : {{ $periode }}
        </td>
    </tr>

    <tr>
        <td colspan="2"><b>NAMA</b></td>
        <td colspan="11">: {{ $nama }}</td>
    </tr>
    <tr>
        <td colspan="2"><b>BAGIAN</b></td>
        <td colspan="11">: {{ $bagian }}</td>
    </tr>

    {{-- HEADER TABLE --}}
    <tr style="background:#eaeaea; font-weight:bold; text-align:center;">
        <td rowspan="2">No</td>
        <td rowspan="2">Item Name</td>
        <td rowspan="2">Tanggal</td>
        <td rowspan="2">Qty (Pcs)</td>
        <td colspan="4">Ket. Reject (Pcs)</td>
        <td rowspan="2">Good MC (Pcs)</td>
        <td rowspan="2">Total yg dibayar (RUMUS)</td>
        <td rowspan="2">Total yg dibayar (Pcs)</td>
        <td rowspan="2">Unit Price (Rp)</td>
        <td rowspan="2">Total Dibayarkan (Rp)</td>
    </tr>

    <tr style="background:#eaeaea; font-weight:bold; text-align:center;">
        <td>FD</td>
        <td>Max Rej. Subkon</td>
        <td>Act. Rej. MC Subkon</td>
        <td>Rej. MC dibebankan</td>
    </tr>

    {{-- DATA --}}
    @php
        $totalQty = 0;
        $totalBayar = 0;

        $totalQtyBawah = 0;
        $totalFD = 0;
        $totalMaxRej = 0;
        $totalActRej = 0;
        $totalRejMC = 0;
        $totalGoodMC = 0;
        $totalDisplay = 0;
        $totalDibayar = 0;

        // ⚠️ biasanya unit price TIDAK dijumlahkan
        // $totalUnitPrice = 0;

        $totalBayarBawah = 0;

    @endphp

    @foreach ($data as $i => $row)
        @php
            $totalQty += $row->qty;
            $totalBayar += $row->total_bayar;

            $totalQtyBawah += $row->qty;
            $totalFD += $row->fd;
            $totalMaxRej += $row->max_reject_subkon;
            $totalActRej += $row->act_reject_subkon;
            $totalRejMC += $row->rej_mc;
            $totalGoodMC += $row->good_mc;
            $totalDisplay += $row->total_display;
            $totalDibayar += $row->total_dibayar_pcs;
            $totalBayarBawah += $row->total_bayar;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->tanggal }}</td>
            <td align="right">{{ number_format($row->qty) }}</td>

            <td align="right">{{ $row->fd }}</td>
            <td align="right">{{ $row->max_reject_subkon }}</td>
            <td align="right">{{ $row->act_reject_subkon }}</td>
            <td align="right">{{ $row->rej_mc }}</td>

            <td align="right">{{ $row->good_mc }}</td>
            <td align="right">{{ $row->total_display }}</td>
            <td align="right">{{ number_format($row->total_dibayar_pcs) }}</td>
            <td align="right">{{ number_format($row->unit_price) }}</td>
            <td align="right">{{ number_format($row->total_bayar) }}</td>
            
        </tr>
    @endforeach

    {{-- FOOTER --}}

    @php
        
    @endphp


    <tr style="font-weight:bold;">
        <td colspan="2" align="center">TOTAL UPAH (1)</td>
        <td></td>
        <td>{{ $totalQtyBawah }}</td>
        <td>{{ $totalFD }}</td>
        <td>{{ $totalMaxRej }}</td>
        <td>{{ $totalActRej }}</td>
        <td>{{ $totalRejMC }}</td>
        <td>{{ $totalGoodMC }}</td>
        <td>{{ $totalDisplay }}</td>
        <td>{{ $totalDibayar }}</td>
        <td>Sub Total :</td>
        <td align="right">{{ number_format($totalBayarBawah) }}</td>
    </tr>

    <tr>
        <td colspan="2" align="center">POT. BPJS TK</td>
        <td colspan="11" align="right">{{ number_format($pot_bpjs) }}</td>
    </tr>

    <tr>
        <td colspan="2" align="center">POT. BPJS KESEHATAN</td>
        <td colspan="11" align="right">{{ number_format($pot_lain) }}</td>
    </tr>

    <tr>
        <td colspan="2" align="center">POT. LAIN-LAIN</td>
        <td colspan="11" align="right">{{ number_format($pot_lain) }}</td>
    </tr>

    <tr>
        <td colspan="2" align="center">TOTAL POT.(2)</td>
        <td colspan="11" align="right">{{ number_format($pot_lain) }}</td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="2" align="center">TAKE HOME PAY(1 -2)</td>
        <td colspan="11" align="right">{{ number_format($take_home_pay) }}</td>
    </tr>

</table>
