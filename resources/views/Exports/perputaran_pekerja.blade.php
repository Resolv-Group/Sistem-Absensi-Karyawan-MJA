<table>
    <tr>
        <td style="font-weight: bold;">Periode :</td>
        <td style="font-weight: bold;">{{ $periodeLabel }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Unit :</td>
        <td style="font-weight: bold;">{{ $unit->nama_unit ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5" style="font-weight: bold; text-align: center; font-size: 14px;">Laporan Perputaran Karyawan Unit</td>
    </tr>
    <tr></tr>

    <tr>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">No</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">Divisi</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">Awal</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">Pembaruan</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">Pengurangan</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid black;">Akhir</td>
    </tr>

    @php
        $totAwal = 0; $totPembaruan = 0; $totPengurangan = 0; $totAkhir = 0;
    @endphp

    @foreach($rekapData as $index => $row)
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black;">{{ $row['divisi'] }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $row['awal'] }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $row['pembaruan'] }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $row['pengurangan'] }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $row['akhir'] }}</td>
        </tr>
        @php
            $totAwal += $row['awal'];
            $totPembaruan += $row['pembaruan'];
            $totPengurangan += $row['pengurangan'];
            $totAkhir += $row['akhir'];
        @endphp
    @endforeach

    <!-- Baris Total -->
    <tr>
        <td colspan="2" style="text-align: right; font-weight: bold; border: 1px solid black;">Total</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totAwal }}</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totPembaruan }}</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totPengurangan }}</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totAkhir }}</td>
    </tr>
</table>