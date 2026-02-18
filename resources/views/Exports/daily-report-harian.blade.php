@php
    // 1. SET LOKALISASI KE INDONESIA agar nama hari muncul sebagai 'Min, Sen, Sel...'
    \Carbon\Carbon::setLocale('id');

    // 2. GUNAKAN VARIABEL DARI CONTROLLER
    // Pastikan variabel $tgl_awal dan $tgl_akhir sudah dipass dari controller
    $startDate = \Carbon\Carbon::parse($tglAwal)->startOfDay(); 
    $endDate   = \Carbon\Carbon::parse($tglAkhir)->startOfDay();

    // 3. GENERATE ARRAY TANGGAL PERIODE
    $periodDates = [];
    $tempDate = $startDate->copy();
    while ($tempDate->lte($endDate)) {
        $periodDates[] = $tempDate->copy();
        $tempDate->addDay();
    }

    // 4. METADATA UNTUK COLSPAN
    $totalDays = count($periodDates);
    // Kolom statis: No, ID, Nama, Jabatan, Divisi, Checkman, Pokok, Jam Lembur, Uang Lembur, HBN Jam, Uang HBN, Insentif Rate, Jml Insentif, Tunjangan Rate, Jml Tunjangan
    // Sesuaikan angka ini dengan jumlah th/td sebelum kolom tanggal dimulai
    $staticCols = 15; 
@endphp

<table>
    {{-- JUDUL LAPORAN (Baris 1-4) --}}
    <tr>
        <td style="font-weight: bold; font-size: 14pt;">LAPORAN ABSENSI dan UPAH</td>
    </tr>
    <tr>

    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">STATION</td>
        <td colspan="3" style="font-weight: bold;">: {{ strtoupper($unit_name) }}</td>
        <td></td>
        <td colspan="2"
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            BPJS KAB {{ strtoupper($unit_name) }}</td>
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            TK</td>
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            KES</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">PERIODE</td>
        <td colspan="3"style="font-weight: bold;">: {{ $periode }}</td>
        <td></td>
        {{-- UMK --}}
        <td colspan="2"
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            {{ isset($unit->umk) ? number_format($unit->umk) : '0' }}
        </td>

        {{-- BPJS NAKER --}}
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            {{ $unit->bpjs_naker ?? 0 }}%
        </td>

        {{-- BPJS KESEHATAN --}}
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            {{ $unit->bpjs_kesehatan ?? 0 }}%
        </td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="3" style="font-weight: bold;">: {{ strtoupper($unit_name) }}</td>
    </tr> {{-- Spacer --}}

    {{-- ================================================================= --}}
    {{-- PENGGANTI TR KOSONG: HEADER TABEL UTAMA (TANGGAL & HARI) --}}
    {{-- ================================================================= --}}

    {{-- BARIS 1: HEADER STATIS + LOOPING TANGGAL (ANGKA) --}}
    <tr>
        <td colspan="9"></td>
        <th rowspan="2" width="15" style="background-color: #FCE4D6; border: 1px solid #000;">{{ $periode }}
        </th>
        <th colspan="{{ $totalDays }}" align="center" valign="middle"
            style="background-color: #FCE4D6; border: 1px solid #000;">Tanggal</th>


        <td colspan="22"></td>
        <th colspan="{{ $totalDays }}" align="center" valign="middle"
            style="background-color: #FCE4D6; border: 1px solid #000;">Tanggal Tidak Masuk Kerja</th>
    </tr>

    <tr>
        <td colspan="10"></td>
        @foreach ($periodDates as $date)
            <th width="4"
                style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; font-weight: bold; vertical-align: middle;">
                {{ $date->format('d') }} {{-- Akan muncul 01, 02, 03... --}}
            </th>
        @endforeach
        <td colspan="22"></td>
        @foreach ($periodDates as $date)
            <th width="4"
                style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; font-weight: bold; vertical-align: middle;">
                {{ $date->format('d') }} {{-- Akan muncul 01, 02, 03... --}}
            </th>
        @endforeach
    </tr>

    <tr></tr>

    {{-- BARIS 2: LOOPING NAMA HARI (SEN, SEL...) --}}
    <tr>
        {{-- (Kolom Kiri Kosong karena sudah kena Rowspan di atas) --}}
        <td colspan="9"></td>
        <th rowspan="2" style="background-color: #FCE4D6; border: 1px solid #000;">{{ $periode }} </th>
        {{-- LOOPING HARI --}}
        @foreach ($periodDates as $date)
            @php
                // Menggunakan format 'ddd' untuk singkatan 3 huruf (Min, Sen, Sel...)
                $dayName = $date->isoFormat('ddd'); 
                
                // Cek Minggu: dayOfWeek di Carbon adalah 0 untuk Minggu
                $isSunday = $date->dayOfWeek === \Carbon\Carbon::SUNDAY;
                $fontColor = $isSunday ? '#FF0000' : '#000000';
            @endphp
            <th style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 8pt; color: {{ $fontColor }};">
                {{ $dayName }}
            </th>
        @endforeach

        <td colspan="22"></td>
        {{-- LOOPING HARI --}}
        @foreach ($periodDates as $date)
            @php
                // Menggunakan format 'ddd' untuk singkatan 3 huruf (Min, Sen, Sel...)
                $dayName = $date->isoFormat('ddd'); 
                
                // Cek Minggu: dayOfWeek di Carbon adalah 0 untuk Minggu
                $isSunday = $date->dayOfWeek === \Carbon\Carbon::SUNDAY;
                $fontColor = $isSunday ? '#FF0000' : '#000000';
            @endphp
            <th style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 8pt; color: {{ $fontColor }};">
                {{ $dayName }}
            </th>
        @endforeach
        {{-- (Kolom Kanan Kosong karena sudah kena Rowspan di atas) --}}
    </tr>
    <tr>
        <td colspan="9"></td>
        @foreach ($periodDates as $date)
            <th align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">
                0
            </th>
        @endforeach

        <td colspan="22"></td>
        @foreach ($periodDates as $date)
            <th align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">
                0
            </th>
        @endforeach
    </tr>
    <tr>
        <td colspan="9"></td>
        <td align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">Total Jam</td>
        @foreach ($periodDates as $date)
            @php
                $fmtDate = $date->format('Y-m-d');
                $dailySum = 0;

                // Loop seluruh data absensi untuk menjumlahkan jam pada tanggal ini
                if (isset($attendanceMap)) {
                    foreach ($attendanceMap as $workerData) {
                        // Tambahkan jam kerja jika ada, default 0
                        $dailySum += $workerData[$fmtDate] ?? 0;
                    }
                }
            @endphp

            <th align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">
                {{-- Tampilkan hasil penjumlahan --}}
                {{ $dailySum > 0 ? (float)$dailySum : 0 }}
            </th>
        @endforeach

        <td colspan="22"></td>
        @foreach ($periodDates as $date)
            @php
                $fmtDate = $date->format('Y-m-d');
                $dailySum = 0;

                // Loop seluruh data absensi untuk menjumlahkan jam pada tanggal ini
                if (isset($attendanceMap)) {
                    foreach ($attendanceMap as $workerData) {
                        // Tambahkan jam kerja jika ada, default 0
                        $dailySum += $workerData[$fmtDate] ?? 0;
                    }
                }
            @endphp

            <th align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">
                {{-- Tampilkan hasil penjumlahan --}}
                {{ $dailySum > 0 ? (float)$dailySum : 0 }}
            </th>
        @endforeach
    </tr>
    <tr>
    </tr>
    <tr></tr>
    <tr></tr>

    {{-- HEADER TABEL (Baris 6-7) --}}
    <thead>
        <tr>
            <th rowspan="2" width="5">NO.</th>
            <th rowspan="2" width="30">NAMA</th>
            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                POSISI / JABATAN</th>
            <th rowspan="2" width="10"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                DIVISI</th>

            <tH rowspan="2" width="14"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UPAH POKOH/BULAN</th>

            <th rowspan="2" width="12"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UPAH POKOH/HARI</th>

            <th rowspan="2" width="12"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UPAH/JAM (LEMBUR)</th>
            <th rowspan="2" width="12"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UANG INSENTIF</th>
            <th rowspan="2" width="10"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                TUNJANGAN</th>

            <th></th>
            <th rowspan="2" colspan="{{ $totalDays }}" align="center" valign="middle"
                style="background-color: #FCE4D6; border: 1px solid #000;">Absensi</th>
            <th rowspan="2" width="8"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                CHECKMAN</th>
            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. POKOK UPAH(B)</th>
            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JAM LEMBUR (C)</th>
            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. UANG LEMBUR (D)</th>
            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                LEMBUR HBN/JAM (E)</th>

            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML UANG LEMBUR HBN (F)</th>

            <th rowspan="2" width="15"
                style="background-color: #92D050; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UPAH INSTF. (G)</th>

            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. UANG INSENTIF (H)</th>

            <th rowspan="2" width="15"
                style="background-color: #92D050; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                UANG TUNJ. (I)</th>


            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. UANG. TUNJ. (J)</th>

            <th rowspan="2" width="15"
                style="background-color: #92D050; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                POT. ABSEN / HARI (K)</th>


            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. POT. ABSEN / HARI (L)</th>


            <th rowspan="2" width="15"
                style="background-color: #92D050; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                POT. ABSEN / JAM (M)</th>

            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                JML. POT. ABSEN/JAM (N)</th>


            <th rowspan="2" width="15"
                style="background-color: #FFFF00; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                TOTAL UPAH (K) = (B+D+F+H-J)-(L+N)</th>

            <th colspan="3" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                POTONGAN</th>


            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                B. ADMIN PAYROLL</th>

            <th rowspan="2" width="15"
                style="background-color: #FFFF00; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                TAKE HOME PAY</th>

            <th rowspan="2" width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                NO. REKENING</th>

        </tr>
        <tr>
            <td colspan="{{ $totalDays }}"></td>
            <th width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                BPJS NAKER</th>
            <th width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                BPJS KES</th>
            <th width="15"
                style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
                BIAYA KLAIM</th>
        </tr>
    </thead>

    {{-- ISI DATA (Mulai Baris 8) --}}
    <tbody>
        @foreach ($items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->jabatan }}</td>
                <td>{{ $item->divisi }}</td>

                <td style="text-align: center;">{{ $item->rate_pokok }}</td>
                <td style="text-align: center;">{{ $item->rate_lembur }}</td>
                <td style="text-align: center;">{{ $item->rate_hbn }}</td>

                <td style="text-align: right;">{{ $item->insentif }}</td>
                <td style="text-align: right;">{{ $item->tunjangan }}</td>
                <td></td>
                @foreach ($periodDates as $date)
                    @php
                        // Format tanggal harus sama persis dengan key di Controller (Y-m-d)
                        $fmtDate = $date->format('Y-m-d');
                        
                        // Pastikan ID dipaksa jadi integer agar cocok dengan key array
                        $workerId = (int) $item->id_original;
                        
                        // Ambil data dari Map. Jika tidak ada, return null
                        $jamKerja = $attendanceMap[$workerId][$fmtDate] ?? null;
                    @endphp

                    <td align="center" valign="middle" style="border: 1px solid #000; ">
                        {{-- Tampilkan angka (float biar 9.0 jadi 9), kosongkan jika null --}}
                        {{ $jamKerja !== null ? (float)$jamKerja : '' }}
                    </td>
                @endforeach
                {{-- CHECKMAN --}}
                <td style="text-align: center; border: 1px solid #000;">{{ $item->checkman }}</td>

                {{-- (B) JML. POKOK UPAH --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_pokok_upah, 0, ',', '.') }}</td>

                {{-- (C) JAM LEMBUR --}}
                <td style="text-align: center; border: 1px solid #000;">{{ $item->jam_lembur }}</td>

                {{-- (D) JML. UANG LEMBUR --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_lembur) }}</td>

                {{-- (E) LEMBUR HBN/JAM --}}
                <td style="text-align: center; border: 1px solid #000;">{{ $item->jam_lembur_hbn }}</td>

                {{-- (F) JML UANG LEMBUR HBN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_lembur_hbn) }}</td>

                {{-- (G) UPAH INSTF. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->upah_insentif) }}</td>

                {{-- (H) JML. UANG INSENTIF --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_insentif) }}</td>

                {{-- (I) UANG TUNJ. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->uang_tunjangan) }}</td>

                {{-- (J) JML. UANG TUNJ. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_tunjangan) }}</td>

                {{-- (K) POT. ABSEN / HARI --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->pot_absen_per_hari) }}</td>

                {{-- (L) JML. POT. ABSEN / HARI --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_pot_absen_hari) }}</td>

                {{-- (M) POT. ABSEN / JAM --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->pot_absen_per_jam) }}</td>

                {{-- (N) JML. POT. ABSEN/JAM --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_pot_absen_jam) }}</td>

                {{-- TOTAL UPAH (HEADER K) --}}
                <td style="text-align: right;font-weight: bold; border: 1px solid #000;">
                    {{ number_format($item->total_upah_kotor) }}
                </td>

                {{-- POTONGAN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    ({{ number_format($item->bpjs_tk) }})</td>
                <td style="text-align: right; border: 1px solid #000;">
                    ({{ number_format($item->bpjs_kes)}})</td>
                <td style="text-align: right; border: 1px solid #000;">
                    ({{ number_format($item->biaya_klaim)}}) </td>

                {{-- B. ADMIN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    ({{ number_format($item->biaya_admin) }})</td>

                {{-- TAKE HOME PAY --}}
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">
                    {{ number_format($item->thp) }}
                </td>

                {{-- NO REKENING --}}
                <td style="text-align: left; border: 1px solid #000;">{{ $item->no_rekening }}</td>

                <td></td>

                @foreach ($periodDates as $date)
                    <td style="border: 1px solid #000;"></td>
                @endforeach
                
            </tr>
        @endforeach
    </tbody>

    {{-- FOOTER TOTAL --}}
    <tfoot>
        <tr>
            <td colspan="9"></td>
            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">TOTAL MAN POWER</td>
            @foreach ($periodDates as $date)
                @php
                    $fmtDate = $date->format('Y-m-d');
                    $manPowerCount = 0;

                    // Loop seluruh data absensi untuk menghitung orang yang hadir
                    if (isset($attendanceMap)) {
                        foreach ($attendanceMap as $workerData) {
                            // Jika ada data jam kerja DAN nilainya lebih dari 0, hitung 1 orang
                            if (isset($workerData[$fmtDate]) && $workerData[$fmtDate] > 0) {
                                $manPowerCount++;
                            }
                        }
                    }
                @endphp

                <th align="center" valign="middle" style="border: 1px solid #000;">
                    {{-- Tampilkan jumlah orang --}}
                    {{ $manPowerCount }}
                </th>
            @endforeach

            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">GRAND TOTAL</td>

            {{-- (B) JML. POKOK UPAH --}}
            <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_pokok_upah')) }}
        </td>

        {{-- (C) JAM LEMBUR --}}
        <td style="text-align: center; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ $items->sum('jam_lembur') }}
        </td>

        {{-- (D) JML. UANG LEMBUR --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_uang_lembur')) }}
        </td>

        {{-- (E) LEMBUR HBN/JAM --}}
        <td style="text-align: center; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ $items->sum('jam_lembur_hbn') }}
        </td>

        {{-- (F) JML UANG LEMBUR HBN --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_uang_lembur_hbn')) }}
        </td>

        {{-- (G) UPAH INSTF. --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('upah_insentif')) }}
        </td>

        {{-- (H) JML. UANG INSENTIF --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_uang_insentif')) }}
        </td>

        {{-- (I) UANG TUNJ. --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('uang_tunjangan')) }}
        </td>

        {{-- (J) JML. UANG TUNJ. --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_uang_tunjangan')) }}
        </td>

        {{-- (K) POT. ABSEN / HARI --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('pot_absen_per_hari')) }}
        </td>

        {{-- (L) JML. POT. ABSEN / HARI --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_pot_absen_hari')) }}
        </td>

        {{-- (M) POT. ABSEN / JAM --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('pot_absen_per_jam')) }}
        </td>

        {{-- (N) JML. POT. ABSEN/JAM --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            {{ number_format($items->sum('jml_pot_absen_jam')) }}
        </td>

        {{-- TOTAL UPAH (HEADER K) --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FFFF00;">
            {{ number_format($items->sum('total_upah_kotor')) }}
        </td>

        {{-- POTONGAN BPJS TK --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            ({{ number_format($items->sum('bpjs_tk')) }})
        </td>
        {{-- POTONGAN BPJS KES --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            ({{ number_format($items->sum('bpjs_kes')) }})
        </td>
        {{-- BIAYA KLAIM --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            ({{ number_format($items->sum('biaya_klaim')) }})
        </td>

        {{-- B. ADMIN --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FCE4D6;">
            ({{ number_format($items->sum('biaya_admin')) }})
        </td>

        {{-- TAKE HOME PAY --}}
        <td style="text-align: right; font-weight: bold; border: 1px solid #000; background-color: #FFFF00;">
            {{ number_format($items->sum('thp')) }}
        </td>

            {{-- NO REKENING --}}
            <td style="text-align: left; border: 1px solid #000; text-decoration: bold;"></td>
        </tr>
    </tfoot>
</table>
