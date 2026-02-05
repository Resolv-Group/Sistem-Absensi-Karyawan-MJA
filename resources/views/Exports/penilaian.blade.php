@php
    use Carbon\Carbon;
    $tahunSekarang = Carbon::now()->year;
@endphp

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
<tr>
            <th colspan="16" align="center" style="border-left: 1px solid #000; border-top: 1px solid #000; border-right: 1px solid #000; font-family:'Times New Roman'; font-size:44px; font-weight:bold; color:red;">
                PT. MITRA JUA ABADI
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center" style="border-left: 1px solid #000; border-right: 1px solid #000; font-family:'Times New Roman'; font-size:15px;">
                Perumahan Deltasari Indah Blok BE-15 Kureksari Waru Sidoarjo Jawa Timur
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center" style="border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; font-family:'Times New Roman'; font-size:15px;">
                Telp : (031) 99660431 | Fax : (031) 8535871
            </th>
        </tr>

        {{-- Gunakan colspan 16 untuk baris kosong agar DOM tetap stabil --}}
        <tr><th colspan="16" style="border-left: 1px solid #000; border-right: 1px solid #000;"></th></tr>

        <tr>
            <th colspan="16" align="center" style="border-left: 1px solid #000; border-right: 1px solid #000; font-family:'Times New Roman'; font-size:16px; font-weight:bold;">
                PENILAIAN PRESTASI KERJA (PPK) KARYAWAN
            </th>
        </tr>

        <tr>
            <th colspan="2" align="left" style="border-left: 1px solid #000; font-family:'Times New Roman'; font-weight:bold;">UNIT KERJA</th>
            <th colspan="14" align="left" style="font-family:'Times New Roman'; font-weight:bold; border-right: 1px solid #000;">: {{ $unit->nama_unit ?? '-' }}</th>
        </tr>
        <tr>
            <th colspan="2" align="left" style="border-left: 1px solid #000; font-family:'Times New Roman'; font-weight:bold;">DIVISI</th>
            <th colspan="14" align="left" style="font-family:'Times New Roman'; font-weight:bold; border-right: 1px solid #000;">: {{ $divisi ?? '-' }}</th>
        </tr>

        <tr>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">NO</th>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">NAMA</th>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">L/P</th>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">USIA</th>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">DIVISI</th>

            <th rowspan="2" colspan="2" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">ABSENSI<br>KEHADIRAN</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">PENGETAHUAN</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">KUALITAS KERJA<br>DAN KINERJA</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">SIKAP KERJA<br>DAN LOYALITAS</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">TOTAL</th>
            <th rowspan="3" align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">KETERANGAN</th>
        </tr>
        <tr>
            
        </tr>
        <tr>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">RANK</th>
            <th align="center" valign="middle" style="border:1px solid #000; background:#FFC000; font-weight:bold;">POINT</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($data as $i => $row)
            @php
                $kelamin = ($row->pekerja->kelamin ?? '') == 1 ? 'L' : 'P';
                $usia = ($row->pekerja->tgl_lahir ?? false) 
                        ? \Carbon\Carbon::now()->year - \Carbon\Carbon::parse($row->pekerja->tgl_lahir)->year 
                        : '-';
                
                $absensiPoint     = ($row->absensi ?? 0) * 0.25;
                $pengetahuanPoint = ($row->pengetahuan ?? 0) * 0.25;
                $kualitasPoint    = ($row->kualitas ?? 0) * 0.30;
                $sikapPoint       = ($row->sikap ?? 0) * 0.20;

                $totalPoint = $absensiPoint + $pengetahuanPoint + $kualitasPoint + $sikapPoint;

                if ($totalPoint >= 50) $rank = 'A';
                elseif ($totalPoint >= 41) $rank = 'B';
                elseif ($totalPoint >= 29) $rank = 'C';
                else $rank = 'D';
            @endphp
            <tr>
                <td align="center" style="border:1px solid #000;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000;">{{ $row->pekerja->nama ?? '-' }}</td>
                <td align="center" style="border:1px solid #000;">{{ $kelamin }}</td>
                <td align="center" style="border:1px solid #000;">{{ $usia }}</td>
                <td align="center" style="border:1px solid #000;">{{ $divisi }}</td>

                <td align="center" style="border:1px solid #000;">25%</td><td align="center" style="border:1px solid #000;">{{ $row->absensi ?? 0 }}</td>
                <td align="center" style="border:1px solid #000;">25%</td><td align="center" style="border:1px solid #000;">{{ $row->pengetahuan ?? 0 }}</td>
                <td align="center" style="border:1px solid #000;">30%</td><td align="center" style="border:1px solid #000;">{{ $row->kualitas ?? 0 }}</td>
                <td align="center" style="border:1px solid #000;">20%</td><td align="center" style="border:1px solid #000;">{{ $row->sikap ?? 0 }}</td>

                <td align="center" style="border:1px solid #000;"><strong>{{ $rank }}</strong></td>
                <td align="center" style="border:1px solid #000;">{{ $totalPoint }}</td>
                <td style="border:1px solid #000;">{{ $row->keterangan ?? '-' }}</td>
            </tr>
        @endforeach

        {{-- Footer dengan Full Border --}}
        <tr> 
            <td colspan="5" style="border:1px solid #000; background-color:#000000"></td> 
            <td colspan="5" align="center" valign="middle" style="border:1px solid #000; font-weight: bold;">Nama</td> 
            <td colspan="3" align="center" valign="middle" style="border:1px solid #000; font-weight: bold;">Tanda Tangan</td> 
            <td colspan="3" align="center" valign="middle" style="border:1px solid #000;">Tanggal</td> 
        </tr> 
        <tr> 
            <td colspan="5" style="border:1px solid #000;">Dibuat Oleh Supervisor</td> 
            <td colspan="5" align="center" valign="middle" style="border:1px solid #000;">{{ $pic }}</td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
        </tr> 
        <tr> 
            <td colspan="5" style="border:1px solid #000;">Diketahui Oleh Staf Operasional</td> 
            <td colspan="5" align="center" valign="middle" style="border:1px solid #000;">{{ $supervisor }}</td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
        </tr> 
        <tr> 
            <td colspan="5" style="border:1px solid #000;">Disetujui Oleh Manager HRD</td> 
            <td colspan="5" align="center" valign="middle" style="border:1px solid #000;">{{ $hrd }}</td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
            <td colspan="3" style="border:1px solid #000;"></td> 
        </tr>
    </tbody>
</table>

