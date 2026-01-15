@php
    use Carbon\Carbon;
    $tahunSekarang = Carbon::now()->year;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
        <tr>
            <th colspan="16" align="center" style="font-size:18px; font-weight:bold; color:red;">
                PT. MITRA JUA ABADI
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center">
                Perumahan Deltasari Indah Blok BE-15 Kureksari Waru Sidoarjo Jawa Timur
            </th>
        </tr>
        <tr>
            <th colspan="16" align="center">
                Telp : (031) 99660431 | Fax : (031) 8535871
            </th>
        </tr>

        <tr>
            <th colspan="16" align="center" style="font-weight:bold;">
                PENILAIAN PRESTASI KERJA (PPK) KARYAWAN
            </th>
        </tr>

        <tr>
            <th colspan="4" align="left">UNIT KERJA</th>
            <th colspan="12" align="left">: {{ $unit->nama_unit ?? '-' }}</th>
        </tr>
        <tr>
            <th colspan="4" align="left">DIVISI</th>
            <th colspan="12" align="left">: {{ $divisi ?? '-' }}</th>
        </tr>

        <tr style="background:#FFD700; font-weight:bold; text-align:center;">
            <th rowspan="2">NO</th>
            <th rowspan="2">NAMA</th>
            <th rowspan="2">L/P</th>
            <th rowspan="2">USIA</th>
            <th rowspan="2">MK</th>

            <th colspan="2">ABSENSI</th>
            <th colspan="2">PENGETAHUAN</th>
            <th colspan="2">KUALITAS</th>
            <th colspan="2">SIKAP</th>
            <th colspan="2">TOTAL</th>
            <th rowspan="2">KETERANGAN</th>
        </tr>

        <tr style="background:#FFD700; font-weight:bold; text-align:center;">
            <th>%</th><th>POINT</th>
            <th>%</th><th>POINT</th>
            <th>%</th><th>POINT</th>
            <th>%</th><th>POINT</th>
            <th>RANK</th><th>POINT</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($data as $i => $row)
            @php
                $kelamin = $row->pekerja->kelamin == 1 ? 'L' : 'P';
                $usia = $row->pekerja->tgl_lahir
                    ? $tahunSekarang - Carbon::parse($row->pekerja->tgl_lahir)->year
                    : 0;

                $absensiPoint     = $row->absensi * 0.25;
                $pengetahuanPoint = $row->pengetahuan * 0.25;
                $kualitasPoint    = $row->kualitas * 0.30;
                $sikapPoint       = $row->sikap * 0.20;

                $totalPoint = $absensiPoint + $pengetahuanPoint + $kualitasPoint + $sikapPoint;

                if ($totalPoint >= 50)      $rank = 'A';
                elseif ($totalPoint >= 41)  $rank = 'B';
                elseif ($totalPoint >= 29)  $rank = 'C';
                else                        $rank = 'D';
            @endphp

            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->pekerja->nama }}</td>
                <td>{{ $kelamin }}</td>
                <td>{{ $usia }}</td>
                <td>{{ $row->mk }}</td>

                <td>25%</td><td>{{ $row->absensi }}</td>
                <td>25%</td><td>{{ $row->pengetahuan }}</td>
                <td>30%</td><td>{{ $row->kualitas }}</td>
                <td>20%</td><td>{{ $row->sikap }}</td>

                <td><strong>{{ $rank }}</strong></td>
                <td>{{ $totalPoint }}</td>

                <td>{{ $row->keterangan ?? '-' }}</td>
            </tr>
        @endforeach

        {{-- FOOTER --}} 
        {{-- <tr><td colspan="16"></td></tr> --}} 
        <tr> 
            <td colspan="5"></td> 
            <td colspan="5">Nama</td> 
            <td colspan="3">Tanda Tangan</td> 
            <td colspan="3">Tanggal</td> 
        </tr> 
        <tr> 
            <td colspan="5">Dibuat Oleh Supervisor</td> 
            <td colspan="5"></td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> </tr> 
        <tr> 
            <td colspan="5">Diketahui Oleh Staf Operasional</td> 
            <td colspan="5"></td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> </tr> 
        <tr> 
            <td colspan="5">Disetujui Oleh Manager HRD</td> 
            <td colspan="5"></td> 
            <td colspan="3"></td> 
            <td colspan="3"></td> 
        </tr>
    </tbody>
</table>

</body>
</html>
