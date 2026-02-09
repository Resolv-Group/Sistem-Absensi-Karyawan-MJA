@php
    // GENERATE TANGGAL (21 Bulan Lalu s/d 20 Bulan Ini)
    // Logika ini otomatis membuat list tanggal berdasarkan input periode
    $tgl_awal = '2025-09-21';
    $tgl_akhir = '2025-10-20';
    $startDate = \Carbon\Carbon::parse($tgl_awal); // misal 2025-09-21
    $endDate = \Carbon\Carbon::parse($tgl_akhir); // misal 2025-10-20

    $periodDates = [];
    $tempDate = $startDate->copy();
    while ($tempDate->lte($endDate)) {
        $periodDates[] = $tempDate->copy();
        $tempDate->addDay();
    }

    $totalDays = count($periodDates);
    // Hitung total kolom statis (No, ID, Nama... sampai Tunjangan) = 10 Kolom
    $staticCols = 10;
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
        <td colspan="2"
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            1000000</td>
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            1%</td>
        <td
            style="background-color: #FCE4D6; font-weight: bold; text-align: center; border: 1px solid #000; white-space: normal;">
            2%</td>
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

        {{-- 2. LOOPING TANGGAL (ANGKA: 21, 22, 23...) --}}

        {{-- Jika ada kolom Total Upah di kanan, tambahkan disini --}}
        {{-- <th rowspan="2" width="15" ...>TOTAL UPAH</th> --}}
    </tr>

    <tr>
        <td colspan="10"></td>
        @foreach ($periodDates as $date)
            <th width="4"
                style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; font-weight: bold; vertical-align: middle;">
                {{ $date->format('d') }}
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
                $dayName = $date->isoFormat('dddd'); // Senin, Selasa...
                $isSunday = $date->dayOfWeek === 0; // 0 = Minggu
                $fontColor = $isSunday ? '#FF0000' : '#000000'; // Merah jika Minggu
            @endphp
            <th
                style="background-color: #FCE4D6; border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 8pt; color: {{ $fontColor }};">
                {{-- Ambil 3 huruf (Sen, Sel, Rab) --}}
                {{ substr($dayName, 0, 3) }}
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
    </tr>
    <tr>
        <td colspan="9"></td>
        <td align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">Total Jam</td>
        @foreach ($periodDates as $date)
            <th align="center" valign="middle" style="background-color: #FCE4D6; border: 1px solid #000;">
                {{-- Ambil 3 huruf (Sen, Sel, Rab) --}}
                0
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

                <td style="text-align: center;">{{ $item->jam_kerja }}</td>
                <td style="text-align: center;">{{ $item->jam_lembur }}</td>
                <td style="text-align: center;">{{ $item->jam_hbn }}</td>

                <td style="text-align: right;">{{ $item->total_pokok }}</td>
                <td style="text-align: right;">{{ $item->total_lembur_biasa + $item->total_lembur_hbn }}</td>
                <td style="text-align: right;">{{ $item->tunjangan }}</td>
                <td></td>
                @foreach ($periodDates as $date)
                    <th align="center" valign="middle" style="border: 1px solid #000;">
                        {{-- Ambil 3 huruf (Sen, Sel, Rab) --}}
                        0
                    </th>
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
                    {{ number_format($item->jml_uang_lembur, 0, ',', '.') }}</td>

                {{-- (E) LEMBUR HBN/JAM --}}
                <td style="text-align: center; border: 1px solid #000;">{{ $item->jam_lembur_hbn }}</td>

                {{-- (F) JML UANG LEMBUR HBN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_lembur_hbn, 0, ',', '.') }}</td>

                {{-- (G) UPAH INSTF. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->upah_insentif, 0, ',', '.') }}</td>

                {{-- (H) JML. UANG INSENTIF --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_insentif, 0, ',', '.') }}</td>

                {{-- (I) UANG TUNJ. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->uang_tunjangan, 0, ',', '.') }}</td>

                {{-- (J) JML. UANG TUNJ. --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_uang_tunjangan, 0, ',', '.') }}</td>

                {{-- (K) POT. ABSEN / HARI --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->pot_absen_per_hari, 0, ',', '.') }}</td>

                {{-- (L) JML. POT. ABSEN / HARI --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_pot_absen_hari, 0, ',', '.') }}</td>

                {{-- (M) POT. ABSEN / JAM --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->pot_absen_per_jam, 0, ',', '.') }}</td>

                {{-- (N) JML. POT. ABSEN/JAM --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->jml_pot_absen_jam, 0, ',', '.') }}</td>

                {{-- TOTAL UPAH (HEADER K) --}}
                <td style="text-align: right;font-weight: bold; border: 1px solid #000;">
                    {{ number_format($item->total_upah_kotor, 0, ',', '.') }}
                </td>

                {{-- POTONGAN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->bpjs_tk, 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->bpjs_kes, 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->biaya_klaim, 0, ',', '.') }}</td>

                {{-- B. ADMIN --}}
                <td style="text-align: right; border: 1px solid #000;">
                    {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>

                {{-- TAKE HOME PAY --}}
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">
                    {{ number_format($item->thp, 0, ',', '.') }}
                </td>

                {{-- NO REKENING --}}
                <td style="text-align: left; border: 1px solid #000;">{{ $item->no_rekening }}</td>
            </tr>
        @endforeach
    </tbody>

    {{-- FOOTER TOTAL --}}
    <tfoot>
        <tr>
            <td colspan="9"></td>
            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">GRAND TOTAL</td>
            @foreach ($periodDates as $date)
                <th align="center" valign="middle" style="border: 1px solid #000;">
                    {{-- Ambil 3 huruf (Sen, Sel, Rab) --}}
                    0
                </th>
            @endforeach

            {{-- CHECKMAN --}}
            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">{{ $item->checkman }}</td>

            {{-- (B) JML. POKOK UPAH --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_pokok_upah, 0, ',', '.') }}</td>

            {{-- (C) JAM LEMBUR --}}
            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">{{ $item->jam_lembur }}</td>

            {{-- (D) JML. UANG LEMBUR --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_uang_lembur, 0, ',', '.') }}</td>

            {{-- (E) LEMBUR HBN/JAM --}}
            <td style="text-align: center; border: 1px solid #000; text-decoration: bold;">{{ $item->jam_lembur_hbn }}</td>

            {{-- (F) JML UANG LEMBUR HBN --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_uang_lembur_hbn, 0, ',', '.') }}</td>

            {{-- (G) UPAH INSTF. --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->upah_insentif, 0, ',', '.') }}</td>

            {{-- (H) JML. UANG INSENTIF --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_uang_insentif, 0, ',', '.') }}</td>

            {{-- (I) UANG TUNJ. --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->uang_tunjangan, 0, ',', '.') }}</td>

            {{-- (J) JML. UANG TUNJ. --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_uang_tunjangan, 0, ',', '.') }}</td>

            {{-- (K) POT. ABSEN / HARI --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->pot_absen_per_hari, 0, ',', '.') }}</td>

            {{-- (L) JML. POT. ABSEN / HARI --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_pot_absen_hari, 0, ',', '.') }}</td>

            {{-- (M) POT. ABSEN / JAM --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->pot_absen_per_jam, 0, ',', '.') }}</td>

            {{-- (N) JML. POT. ABSEN/JAM --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->jml_pot_absen_jam, 0, ',', '.') }}</td>

            {{-- TOTAL UPAH (HEADER K) --}}
            <td style="text-align: right;font-weight: bold; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->total_upah_kotor, 0, ',', '.') }}
            </td>

            {{-- POTONGAN --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">{{ number_format($item->bpjs_tk, 0, ',', '.') }}
            </td>
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">{{ number_format($item->bpjs_kes, 0, ',', '.') }}
            </td>
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->biaya_klaim, 0, ',', '.') }}</td>

            {{-- B. ADMIN --}}
            <td style="text-align: right; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>

            {{-- TAKE HOME PAY --}}
            <td style="text-align: right; font-weight: bold; border: 1px solid #000; text-decoration: bold;">
                {{ number_format($item->thp, 0, ',', '.') }}
            </td>

            {{-- NO REKENING --}}
            <td style="text-align: left; border: 1px solid #000; text-decoration: bold;">{{ $item->no_rekening }}</td>
        </tr>
    </tfoot>
</table>
