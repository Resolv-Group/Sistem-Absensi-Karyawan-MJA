@php
    use Carbon\Carbon;
    $tahunSekarang = Carbon::now()->year;
@endphp

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
        <tr>
            <th colspan="16" align="center" style="font-family:'Times New Roman'; font-size:44px; font-weight:bold; color:red;">
                PT. MITRA JUA ABADI
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center" style="font-family:'Times New Roman'; font-size:15px;">
                Perumahan Deltasari Indah Blok BE-15 Kureksari Waru Sidoarjo Jawa Timur
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center" style="font-family:'Times New Roman'; font-size:15px;">
                Telp : (031) 99660431 | Fax : (031) 8535871
            </th>
        </tr>

        {{-- Gunakan colspan 16 untuk baris kosong agar DOM tetap stabil --}}
        <tr><th colspan="16"></th></tr>

        <tr>
            <th colspan="16" align="center" style="font-family:'Times New Roman'; font-size:16px; font-weight:bold;">
                PENILAIAN PRESTASI KERJA (PPK) KARYAWAN
            </th>
        </tr>

        <tr>
            <th colspan="2" align="left" style="font-family:'Times New Roman'; font-weight:bold;">UNIT KERJA</th>
            <th colspan="14" align="left" style="font-family:'Times New Roman'; font-weight:bold;">: {{ $unit->nama_unit ?? '-' }}</th>
        </tr>
        <tr>
            <th colspan="2" align="left" style="font-family:'Times New Roman'; font-weight:bold;">DIVISI</th>
            <th colspan="14" align="left" style="font-family:'Times New Roman'; font-weight:bold;">: {{ $divisi ?? '-' }}</th>
        </tr>

        <tr>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">NO</th>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">NAMA</th>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">L/P</th>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">USIA</th>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">MK</th>

            <th rowspan="2" colspan="2" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">ABSENSI<br>KEHADIRAN</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">PENGETAHUAN</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">KUALITAS KERJA<br>DAN KINERJA</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">SIKAP KERJA<br>DAN LOYALITAS</th>
            <th rowspan="2" colspan="2" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">TOTAL</th>
            <th rowspan="3" align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">KETERANGAN</th>
        </tr>
        <tr>
            {{-- Baris pendukung Rowspan/Colspan --}}
            <th colspan="2"></th><th colspan="2"></th><th colspan="2"></th><th colspan="2"></th><th colspan="2"></th>
        </tr>
        <tr>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">%</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">POINT</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">RANK</th>
            <th align="center" valign="middle" style="background:#FFC000; font-family:'Times New Roman'; font-weight:bold;">POINT</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($data as $i => $row)
            @php
                $kelamin = ($row->pekerja->kelamin ?? '') == 1 ? 'L' : 'P';
                $usia = ($row->pekerja->tgl_lahir ?? false) 
                        ? \Carbon\Carbon::now()->year - \Carbon\Carbon::parse($row->pekerja->tgl_lahir)->year 
                        : '-';
                
                // Pastikan perhitungan point aman dari null
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
                <td align="center">{{ $i + 1 }}</td>
                <td>{{ $row->pekerja->nama ?? '-' }}</td>
                <td align="center">{{ $kelamin }}</td>
                <td align="center">{{ $usia }}</td>
                <td align="center">{{ $row->mk ?? '0' }}</td>

                <td align="center">25%</td><td align="center">{{ $row->absensi ?? 0 }}</td>
                <td align="center">25%</td><td align="center">{{ $row->pengetahuan ?? 0 }}</td>
                <td align="center">30%</td><td align="center">{{ $row->kualitas ?? 0 }}</td>
                <td align="center">20%</td><td align="center">{{ $row->sikap ?? 0 }}</td>

                <td align="center"><strong>{{ $rank }}</strong></td>
                <td align="center">{{ $totalPoint }}</td>
                <td>{{ $row->keterangan ?? '-' }}</td>
            </tr>
        @endforeach

        <tr> 
            <td colspan="5" style="background-color:#000000"></td> 
            <td colspan="5" align="center" valign="middle" style="font-weight: bold;">Nama</td> 
            <td colspan="3" align="center" valign="middle" style="font-weight: bold;">Tanda Tangan</td> 
            <td colspan="3" align="center" valign="middle" >Tanggal</td> 
        </tr> 
        <tr> 
            <td colspan="5">Dibuat Oleh Supervisor</td> 
            <td colspan="5" align="center" valign="middle">{{ $supervisor }}</td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> 
        </tr> 
        <tr> 
            <td colspan="5">Diketahui Oleh Staf Operasional</td> 
            <td colspan="5"></td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> 
        </tr> 
        <tr> 
            <td colspan="5">Disetujui Oleh Manager HRD</td> 
            <td colspan="5" align="center" valign="middle"></td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> 
        </tr>
    </tbody>
</table>

