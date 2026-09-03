<table>
    <tr>
        <td style="font-weight: bold;">Tanggal Setelah :</td>
        <td style="font-weight: bold;">{{ $tanggalSetelah }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Unit :</td>
        <td style="font-weight: bold;">{{ $unit ? $unit->nama_unit : '-' }}</td> <!-- Ubah nama_unit sesuai kolom DB Anda -->
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="5" style="font-weight: bold; text-align: center; font-size: 14px;">Laporan Monitoring Masa Berlaku PKWT</td>
    </tr>
    <tr></tr> <!-- Baris kosong (Row 4) -->

    <!-- ============================================== -->
    <!-- BARIS HEADER                                   -->
    <!-- ============================================== -->
    <tr>
        <td rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid black;">No</td>
        <td rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid black;">Nama</td>
        <td rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid black;">Divisi</td>
        <td rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid black;">Jabatan</td>
        
        <!-- Lebar "Masa Berlaku" akan membentang sepanjang durasi yang dipilih -->
        <td colspan="{{ $durasi }}" style="font-weight: bold; text-align: center; border: 1px solid black;">Masa Berlaku</td>
        
        <td rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid black;">Keterangan</td>
    </tr>

    <!-- BARIS SUB-HEADER (Nama Bulan) -->
    <tr>
        @foreach($targetMonths as $month)
            <td style="font-weight: bold; text-align: center; border: 1px solid black;">{{ $month['label'] }}</td>
        @endforeach
    </tr>

    <!-- ============================================== -->
    <!-- BARIS DATA PEKERJA                             -->
    <!-- ============================================== -->
    @php
        $totals = array_fill(0, $durasi, 0);
        $totalKeterangan = 0;
    @endphp

    @foreach($dataPkwt as $index => $pkwt)
        @php
            $tglAkhir = \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt);
            $formatYm = $tglAkhir->format('Y-m'); // Format Tahun-Bulan untuk validasi
            $tanggalFormatted = $tglAkhir->translatedFormat('d F Y'); // Output: 17 Agustus 2026
            
            $isInTarget = false;
        @endphp
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black;">{{ $pkwt->pekerja->nama ?? '-' }}</td>
            <td style="border: 1px solid black;">{{ $pkwt->divisi->nama ?? '-' }}</td>   <!-- Ubah dari nama_divisi ke nama -->
            <td style="border: 1px solid black;">{{ $pkwt->jabatan->nama ?? '-' }}</td>  <!-- Ubah dari nama_jabatan ke nama -->
                        
            <!-- LOOPING KOLOM BULAN -->
            @foreach($targetMonths as $idx => $month)
                @if($formatYm === $month['format'])
                    <td style="border: 1px solid black; text-align: center;">{{ $tanggalFormatted }}</td>
                    @php 
                        $isInTarget = true; 
                        $totals[$idx]++;
                    @endphp
                @else
                    <td style="border: 1px solid black;"></td>
                @endif
            @endforeach
            
            <!-- KOLOM KETERANGAN -->
            <!-- Jika tanggalnya TIDAK ADA dalam durasi bulan (misal Agt atau Des), masuk ke Keterangan -->
            @if(!$isInTarget)
                <td style="border: 1px solid black; text-align: center;">{{ $tanggalFormatted }}</td>
                @php $totalKeterangan++; @endphp
            @else
                <td style="border: 1px solid black;"></td>
            @endif
        </tr>
    @endforeach

    <!-- ============================================== -->
    <!-- BARIS TOTAL BAWAH                              -->
    <!-- ============================================== -->
    <tr>
        <td colspan="4" style="text-align: right; font-weight: bold; border: 1px solid black;">Total</td>
        @foreach($totals as $total)
            <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $total }}</td>
        @endforeach
        <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totalKeterangan }}</td>
    </tr>
</table>