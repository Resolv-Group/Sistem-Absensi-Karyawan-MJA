<table style="border-collapse: collapse; font-family: Calibri; font-size:11px; width:100%;">

    {{-- ================================================= --}}
    {{-- BAGIAN KOP / JUDUL --}}
    {{-- ================================================= --}}
    <tr>
        <td></td>
        <td></td>
        <td colspan="18" style="font-size:18px; font-weight:bold;">
            REKAP GAJI KARYAWAN
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="18">PT. Mitra Jua Abadi</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="18">Periode: {{ $periode }}</td>
    </tr>

    <tr><td colspan="20"></td></tr>

    {{-- ================================================= --}}
    {{-- HEADER TABEL --}}
    {{-- ================================================= --}}
    <tr>
        <td style="font-weight: bold;">{{ $unit_name }}</td>
    </tr>
    <tr style="background:#CCFF33; font-weight:bold; text-align:center;">
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">NO</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">NIK</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">NAMA KARYAWAN</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">SECTION</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">JOINT DATE</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">EXIS DATE</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">STATUS</td>

        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;" colspan="4">PENDAPATAN</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">TOTAL PENDAPATAN</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">MANAGEMENT FEE (5%)</td>

        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;" colspan="2">POTONGAN</td>

        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">TOTAL GAJI</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;" colspan="2">DPP (dari MAN FEE)</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">PPH PASAL 23 (2%)</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center; vertical-align: middle;" rowspan="2">TOTAL INVOICE</td>
    </tr>

    <tr style="background:#CCFF33; font-weight:bold; text-align:center;">
        {{-- Anak PENDAPATAN --}}
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">GAJI POKOK</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">UPAH LEMBUR</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">KOREKSI</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">LAINNYA</td>

        {{-- Anak POTONGAN --}}
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">BPJS-TK</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">BPJS-KES</td>

        {{-- Anak DPP --}}
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">11/12</td>
        <td style="background:#CCFF33; border:1px solid #000; font-weight:bold; text-align:center;">12%</td>
    </tr>

    {{-- ================================================= --}}
    {{-- ISI DATA --}}
    {{-- ================================================= --}}
    @foreach($data as $d)
    <tr>
        <td style="border:1px solid #000; text-align:center;">{{ $d['no'] }}</td>
        <td style="border:1px solid #000;">{{ $d['nik'] }}</td>
        <td style="border:1px solid #000;">{{ $d['nama'] }}</td>
        <td style="border:1px solid #000;">{{ $d['section'] }}</td>
        <td style="border:1px solid #000; text-align:center;">{{ $d['join'] }}</td>
        <td style="border:1px solid #000; text-align:center;">{{ $d['exit'] }}</td>
        <td style="border:1px solid #000; text-align:center;">{{ $d['status'] }}</td>   

        <td style="border:1px solid #000; text-align:right;">{{ $d['gapok'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['lembur'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['koreksi'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['lainnya'] }}</td>

        <td style="border:1px solid #000; text-align:right; font-weight:bold;">{{ $d['total_pendapatan'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['management_fee'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $d['bpjstk'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['bpjskes'] }}</td>

        <td style="border:1px solid #000; text-align:right; font-weight:bold; background:#e6f2ff;">{{ $d['total_gaji'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $d['dpp'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $d['ppn'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $d['pph'] }}</td>
        <td style="border:1px solid #000; text-align:right; font-weight:bold; background:#ffebcc;">{{ $d['invoice'] }}</td>
    </tr>
    @endforeach

    {{-- ================================================= --}}
    {{-- GRAND TOTAL --}}
    {{-- ================================================= --}}
    <tr style="font-weight:bold; background:#f2f2f2;">
        <td colspan="7" style="border:1px solid #000; text-align:center; font-weight:bold;">TOTAL</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['gapok'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['lembur'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['koreksi'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['lainnya'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['total_pendapatan'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['management_fee'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['bpjstk'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['bpjskes'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['total_gaji'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['dpp'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['ppn'] }}</td>

        <td style="border:1px solid #000; text-align:right;">{{ $totals['pph'] }}</td>
        <td style="border:1px solid #000; text-align:right;">{{ $totals['invoice'] }}</td>
    </tr>

    <tr></tr>
    <tr>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $penanggungjawab[0] }}</td>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $penanggungjawab[1] }}</td>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $penanggungjawab[2] }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $jabatan[0] }}</td>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $jabatan[1] }}</td>
        <td></td>
        <td style="font-weight:bold; text-align:center; vertical-align: middle;">{{ $jabatan[2] }}</td>
    </tr>

</table>