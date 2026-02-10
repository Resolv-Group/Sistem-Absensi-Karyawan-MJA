<table>
    {{-- LOOPING VERTIKAL (Per Grup isi 3 Orang) --}}
    @foreach($chunks as $group)

        {{-- ========================================================== --}}
        {{-- BAGIAN 1: HEADER & IDENTITAS --}}
        {{-- ========================================================== --}}

        {{-- BARIS 1: Header Judul --}}
        <tr>
            @foreach($group as $item)
                <td colspan="9" style="border: 1px solid #000000; border-bottom: none; background-color: #FCE4D6; font-weight: bold; text-align: center; font-size: 12pt; text-decoration: underline;">
                    RINCIAN UPAH
                </td>
                <td style="width: 20px;"></td> {{-- SPACER ANTAR STRUK --}}
            @endforeach
        </tr>

        {{-- BARIS 2: Nama --}}
        <tr>
            @foreach($group as $item)
                <td style="border-left: 1px solid #000000; font-weight: bold; background-color: #FCE4D6;">NAMA</td>
                <td style="text-align: center; background-color: #FCE4D6;">:</td>
                <td colspan="7" style="border-right: 1px solid #000000; font-weight: bold; background-color: #FCE4D6;">{{ $item->nama }}</td>
                <td></td> {{-- SPACER --}}
            @endforeach
        </tr>

        {{-- BARIS 3: ID --}}
        <tr>
            @foreach($group as $item)
                <td style="border-left: 1px solid #000000; font-weight: bold; background-color: #FCE4D6;">ID</td>
                <td style="text-align: center; background-color: #FCE4D6;">:</td>
                <td colspan="7" style="border-right: 1px solid #000000; font-weight: bold; text-align: left; background-color: #FCE4D6;">{{ $item->id_karyawan }}</td>
                <td></td> {{-- SPACER --}}
            @endforeach
        </tr>

        {{-- BARIS 4: Jabatan / Divisi --}}
        <tr>
            @foreach($group as $item)
                <td style="border-left: 1px solid #000000; font-weight: bold; background-color: #FCE4D6;">JAB. / DIV.</td>
                <td style="text-align: center; background-color: #FCE4D6;">:</td>
                <td colspan="4" style="font-weight: bold; background-color: #FCE4D6;">{{ $item->jabatan }}</td>
                <td style="background-color: #FCE4D6;"></td>
                <td colspan="2" style="border-right: 1px solid #000000; font-weight: bold; text-align: right; background-color: #FCE4D6;">{{ $item->divisi }}</td>
                <td></td> {{-- SPACER --}}
            @endforeach
        </tr>

        {{-- BARIS 5: Periode --}}
        <tr>
            @foreach($group as $item)
                <td colspan="9" style="border: 1px solid #000000; background-color: #FCE4D6; font-weight: bold; text-align: center;">
                    {{ $periode }}
                </td>
                <td></td> {{-- SPACER --}}
            @endforeach
        </tr>

        {{-- ========================================================== --}}
        {{-- BAGIAN 2: PENDAPATAN --}}
        {{-- ========================================================== --}}

        {{-- BARIS 6: Upah Pokok --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000;">UPAH POKOK</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->upah_pokok, 0, ',', '.') }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 7: Upah Lembur --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">UPAH LEMBUR</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">{{ $item->lembur_jam }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->lembur_rate) }}</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->total_lembur_biasa ) }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 8: Upah Lembur HBN --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">UPAH LEMBUR HBN</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">{{ $item->lembur_hbn_jam }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->lembur_hbn_rate) }}</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->total_lembur_hbn) }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 9: Insentif --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">UANG INSENTIF</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">0</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">-</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 10: Tunjangan --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">UANG TUNJANGAN</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">0</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->tunjangan ?? '-') }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 11: Spacer Garis Ganda --}}
        <tr>
            @foreach($group as $item)
                <td colspan="8" style="border-left: 1px solid #000000;"></td>
                <td style="border-right: 1px solid #000000; border-bottom: 3px double #000000;"></td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 12: Jumlah 1 --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000; font-weight: bold; font-style: italic; text-align: center;">JUMLAH 1</td>
                <td></td>
                <td style="border-right: 1px solid #000000; font-weight: bold; text-align: right;">{{ number_format($item->jumlah_1, 0, ',', '.') ?? '-'}}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 13: Jarak Kosong --}}
        <tr>
            @foreach($group as $item)
                <td colspan="9" style="border-left: 1px solid #000000; border-right: 1px solid #000000;">&nbsp;</td>
                <td></td>
            @endforeach
        </tr>

        {{-- ========================================================== --}}
        {{-- BAGIAN 3: POTONGAN --}}
        {{-- ========================================================== --}}

        {{-- BARIS 14: Header Potongan --}}
        <tr>
            @foreach($group as $item)
                <td colspan="9" style="border-left: 1px solid #000000; border-right: 1px solid #000000; font-weight: bold; text-decoration: underline;">POTONGAN</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 15: Absensi Hari --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">ABSENSI / HARI</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">{{ $item->absen_hari ?? 0 }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->potongan_hari_rate ?? 0) }}</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->potongan_hari ?? 0, 0, ',', '.') ?? '-'}}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 16: Absensi Jam --}}
        <tr>
            @foreach($group as $item)
                <td colspan="4" style="border-left: 1px solid #000000;">ABSENSI / JAM</td>
                <td colspan="2" style="border: 1px solid #000000; text-align: center;">{{ $item->absen_jam ?? 0 }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->potongan_jam_rate ?? 0)}}</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->potongan_jam ?? 0, 0, ',', '.')?? '-' }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 17: BPJS TK --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000;">BPJS KETENAGAKERJAAN</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">({{ number_format($item->bpjs_tk ?? 0, 0, ',', '.') ?? '-'}})</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 18: BPJS Kes --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000;">BPJS KESEHATAN</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">({{ number_format($item->bpjs_kes ?? 0, 0, ',', '.') ?? '-' }})</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 19: Klaim --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000;">BIAYA KLAIM</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">-</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 20: Lain-lain --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000;">LAIN - LAIN</td>
                <td style="text-align: center;">=</td>
                <td style="border-right: 1px solid #000000; text-align: right;">{{ number_format($item->potonganLain ?? '-') }}</td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 21: Spacer Garis Ganda --}}
        <tr>
            @foreach($group as $item)
                <td colspan="8" style="border-left: 1px solid #000000;"></td>
                <td style="border-right: 1px solid #000000; border-bottom: 3px double #000000;"></td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS 22: Jumlah 2 --}}
        <tr>
            @foreach($group as $item)
                <td colspan="7" style="border-left: 1px solid #000000; font-weight: bold; font-style: italic; text-align: center;">JUMLAH 2</td>
                <td></td>
                <td style="border-right: 1px solid #000000; font-weight: bold; text-align: right;">({{ number_format($item->jumlah_2 ?? 0, 0, ',', '.') ?? '-' }})</td>
                <td></td>
            @endforeach
        </tr>

        {{-- ========================================================== --}}
        {{-- BAGIAN 4: FOOTER --}}
        {{-- ========================================================== --}}

        {{-- BARIS 23: Take Home Pay --}}
        <tr>
            @foreach($group as $item)
                <td colspan="8" style="border: 1px solid #000000; font-weight: bold; font-size: 12pt;">TAKE HOME PAY</td>
                <td style="border: 1px solid #000000; font-weight: bold; font-size: 12pt; text-align: right;">
                    {{ number_format($item->take_home_pay, 0, ',', '.') }}
                </td>
                <td></td>
            @endforeach
        </tr>

        {{-- BARIS SPASI VERTIKAL ANTAR GRUP --}}
        <tr><td colspan="30" style="height: 30px;"></td></tr>

    @endforeach
</table>