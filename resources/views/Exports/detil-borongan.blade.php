<style>
    table {
        font-family: "Times New Roman", Times, serif;
    }
    td, th {
        font-family: "Times New Roman", Times, serif;
    }
</style>

<table border="5" cellspacing="0" cellpadding="4" width="100%" style="border-collapse: collapse; border: 3pt solid black;">

    {{-- HEADER --}}
    <tr>
        <td colspan="15" align="center" style="font-weight:bold; font-size:16px;">
            SUMMARY UPAH
        </td>
    </tr>
    <tr>
        <td colspan="15" align="center" style="font-weight:bold;">
            PT MITRA JUA ABADI
        </td>   
    </tr>

        <tr>
        <td colspan="15" align="center" style="font-weight:bold;">
            PERIODE : {{ $periode }}
        </td>   
    </tr>

    <tr>
        <td colspan="2" style="font-weight:bold;"><b>NAMA</b></td>
        <td colspan="13" style="font-weight:bold;">: {{ $nama }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold;"><b>BAGIAN</b></td>
        <td colspan="13" style="font-weight:bold;">: {{ $bagian }}</td>
    </tr>

    {{-- HEADER TABLE --}}
    <tr>
        <td rowspan="3" align="center" valign="middle">No</td>
        <td rowspan="3" colspan="3" align="center" valign="middle">Item Name</td>
        <td align="center" valign="middle">Surat Jalan</td>
        <td rowspan="3" align="center" valign="middle">Qty (Pcs)</td>
        <td colspan="4" align="center" valign="middle">Ket. Reject (Pcs)</td>
        <td rowspan="3" align="center" valign="middle">Good MC<br>(Pcs)</td>
        <td rowspan="3" align="center" valign="middle">Total yg dibayar<br>(RUMUS)</td>
        <td rowspan="3" align="center" valign="middle">Total yg dibayar<br>(Pcs)</td>
        <td rowspan="3" align="center" valign="middle">Unit Price<br>(Rp)</td>
        <td rowspan="3" align="center" valign="middle">Total<br>Bayar<br>(Rp)</td>
    </tr>

    <tr>
        <td rowspan="2" align="center" valign="middle">Tanggal</td>
        <td rowspan="2" align="center" valign="middle">FD</td>
        <td align="center" valign="middle">Max Rej.</td>
        <td align="center" valign="middle">Act. Rej.</td>
        <td align="center" valign="middle">Rej. MC</td>
    </tr>

    <tr>
        <td align="center" valign="middle">Subkon</td>
        <td align="center" valign="middle">MC Subkon</td>
        <td align="center" valign="middle">dibebankan</td>
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
            <td align="center">{{ $i + 1 }}</td>
            <td colspan="3">{{ $row->item_name }}</td>
            <td>{{ $row->tanggal }}</td>
            <td align="center">{{ number_format($row->qty) }}</td>

            <td align="center">{{ $row->fd }}</td>
            <td align="center">{{ $row->max_reject_subkon }}</td>
            <td align="center">{{ $row->act_reject_subkon }}</td>
            <td align="center">{{ $row->rej_mc }}</td>

            <td align="center">{{ $row->good_mc }}</td>
            <td align="center">{{ $row->total_dibayar_pcs }}</td>
            <td align="center">{{ number_format($row->total_dibayar_pcs) }}</td>
            <td align="center">{{ number_format($row->unit_price) }}</td>
            <td align="right">{{ number_format($row->total_bayar) }}</td>
            
        </tr>
    @endforeach

    {{-- FOOTER --}}
    <tr>
        <td colspan="5" align="center"><b>TOTAL UPAH (1)</b></td>
        <td align="center"><b>{{ $totalQtyBawah }}</b></td>
        <td align="center"><b>{{ $totalFD }}</b></td>
        <td align="center"><b>{{ $totalMaxRej }}</b></td>
        <td align="center"><b>{{ $totalActRej }}</b></td>
        <td align="center"><b>{{ $totalRejMC }}</b></td>
        <td align="center"><b>{{ $totalGoodMC }}</b></td>
        <td align="center"><b>{{ $totalDisplay }}</b></td>
        <td align="center"><b>{{ $totalDibayar }}</b></td>
        <td><b>Sub Total:</b></td>
        <td align="right"><b>{{ number_format($totalBayarBawah) }}</b></td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>POT. BPJS TK</b></td>
        <td colspan="10" align="right"><b>{{ number_format($pot_bpjs) }}</b></td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>POT. BPJS KESEHATAN</b></td>
        <td colspan="10" align="right"><b>{{ number_format($pot_kesehatan) }}</b></td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>POT. LAIN-LAIN</b></td>
        <td colspan="10" align="right"><b>{{ number_format($pot_lain) }}</b></td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>TOTAL POT.(2)</b></td>
        <td colspan="10" align="right"><b>{{ number_format($pot_lain + $pot_bpjs + $pot_kesehatan) }}</b></td>
    </tr>

        <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>TUNJANGAN(3)</b></td>
        <td colspan="10" align="right"><b>{{ number_format($tunjangan) }}</b></td>
    </tr>

    <tr style="font-weight:bold;">
        <td colspan="5" align="center"><b>TAKE HOME PAY(1-2+3)</b></td>
        <td colspan="10" align="right"><b>{{ number_format($take_home_pay) }}</b></td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="13"></td>
        <td align="center"><b>SPV</b></td>
        <td align="center"><b>ADMIN</b></td>
    </tr>
    <tr>
        <td colspan="13"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="13"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="13"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="13"></td>
        <td align="center"><b>SPV</b></td>
        <td align="center"><b>ADMIN</b></td>
    </tr>


</table>
