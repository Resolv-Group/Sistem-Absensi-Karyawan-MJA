@php
use Carbon\Carbon;
@endphp

<style>
    * {
        font-family: "Times New Roman", Times, serif !important;
    }

    table {
        font-family: "Times New Roman", Times, serif;
        font-size: 12px;
    }

    td, th {
        font-family: "Times New Roman", Times, serif;
    }
</style>


<table width="100%" cellpadding="4" cellspacing="0">
    {{-- HEADER --}}
    <tr>
        <td colspan="12" align="center" style="font-size:35px; font-weight:bold;color:red; text-decoration: underline;">
            PT. MITRA JUA ABADI
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center" style="font-weight:bold;">
            PERUM. DELTA SARI INDAH BLOK BE 15 - 17 KEC. WARU KAB. SIDOARJO
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center" style="font-weight:bold;">
            TELP : (031) 8535871 ; 99660431
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center" style="border-bottom:4px solid #000; font-weight:bold;">
            EMAIL : hrd@mitrajasa.com
        </td>
    </tr>

    <tr>
        <td colspan="12" style="border-top:4px solid #000;">
        </td>
    </tr>

    {{-- JUDUL --}}
    <tr>
        <td colspan="12" align="center" style="font-weight:bold;font-size:16px; text-decoration:underline;">
            INVOICE
        </td>
    </tr>

    <tr>
    </tr>

    {{-- INFO --}}
    <tr>
        <td style="width:100px; font-weight:bold;">No :</td>
        <td colspan="9" align="left" style="font-weight:bold;">
            {{ $resi }}
        </td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Kepada :</td>
        <td colspan="9" style="font-weight:bold;">{{ $nama_mitra }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">d / a :</td>
        <td colspan="9" style="font-weight:bold;">
            {{ $alamat }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="9" style="font-weight:bold;">
            -
        </td>
    </tr>

    <tr><td colspan="10"></td></tr>

    <tr>
        <td colspan="10" style="font-weight:bold;">{{ $bidangUsaha }}</td>
    </tr>

    <tr>
        <td colspan="10" style="font-weight:bold;">{{ $nama_unit }}</td>
    </tr>


    <tr></tr>

    <tr><td colspan="10" style="font-size: 9px; text-indent:2px;">       Berdasarkan Surat Perjanjian Kerja, antara PT. MITRA JUA ABADI dengan PT. BISI INTERNATIONAL, TBK tentang kerjasama Sumber Daya Manusia sampai saat ini sudah berjalan,</td></tr>
    <tr><td colspan="10" style="font-size: 9px;">maka dengan ini kami mengajukan tagihan upah tenaga kerja PT. MITRA JUA ABADI yang ditempatkan atau ditugaskan di PT. BISI INTERNATIONAL, TBK adalah sebagai berikut :</td></tr>

    <tr></tr>

    <tr>
        <td>Periode :</td>
        <td colspan="9">{{ $periode }}</td>
    </tr>

    {{-- TABLE HEADER --}}
    <tr style="font-weight:bold;">
        <td rowspan="4" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;">No</td>
        <td rowspan="4" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;">Jml Naker</td>
        <td rowspan="4" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;">Item</td>
        <td rowspan="3" align="center" valign="middle" style="background-color:#F2F2F2; white-space: normal;
        word-wrap: break-word; font-weight:bold;">Total Biaya Pengelolaan</td>
        <td colspan="2" align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">BPJS NAKER</td>
        <td colspan="2" align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">BPJS KESEHATAN</td>
        <td rowspan="2" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;
        white-space: normal;
        word-wrap: break-word; ">Management Fee</td>
        <td rowspan="2" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;">PPN</td>
        <td rowspan="2" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold;">PPH 23</td>
        <td rowspan="3" align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold; white-space: normal;
        word-wrap: break-word;">Grand Total Tagihan</td>
    </tr>

    {{-- DATA --}}
    <tr>
        <td align="center" valign="middle" colspan="2"  style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">4,24%</td>
        <td align="center" valign="middle" colspan="2" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">4%</td>
    </tr>

    <tr>
        <td align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">JML PESERTA</td>
        <td align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">{{ $countBpjsNaker }}</td>
        <td align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">JML PESERTA</td>
        <td align="center" valign="middle" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">{{ $countBpjsKesehatan }}</td>
        <td align="center" valign="middle" style=" white-space: normal;
        word-wrap: break-word; font-weight:bold;">{{ $persentase_management_fee }}%</td>
        <td align="center" valign="middle" style=" white-space: normal;
        word-wrap: break-word; font-weight:bold;">11%</td>
        <td align="center" valign="middle" style=" white-space: normal;
        word-wrap: break-word; font-weight:bold;">2%</td>
    </tr>

    <tr>
        <td align="center" valign="middle" style=" white-space: normal;
        word-wrap: break-word; font-weight:bold;">A</td>
        <td align="center" valign="middle" colspan="2" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">B = UMK x JML. PESERTA x 4,24%</td>
        <td align="center" valign="middle" colspan="2" style="background-color:#92D050; white-space: normal;
        word-wrap: break-word; font-weight:bold;">C = UMK x JML. PESERTA x 4%</td>
        <td align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold; white-space: normal;
        word-wrap: break-word;">D = A x {{ $persentase_management_fee }}%</td>
        <td align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold; white-space: normal;
        word-wrap: break-word;">E = D x 11%</td>
        <td align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold; white-space: normal;
        word-wrap: break-word;">F = D x 2%</td>
        <td align="center" valign="middle" style="background-color:#F2F2F2; font-weight:bold; white-space: normal;
        word-wrap: break-word;">G = (A + B + C + D + E + F)</td>
    </tr>
    
    {{-- Tabel Data --}}
    <tr>
        <td align="center" valign="middle">1</td>
        <td align="center" valign="middle">0</td>
        <td style="white-space: normal;
        word-wrap: break-word;">{{ $nama_mitra }}</td>
        <td align="right" valign="middle" style="font-weight:bold;">{{ $grand_total }}</td>
        <td colspan="2" align="right" valign="middle" style="font-weight:bold;">{{ $naker }}</td>
        <td colspan="2" align="right" valign="middle" style="font-weight:bold;">{{ $kesehatan }}</td>
        <td align="right" valign="middle" style="font-weight:bold;">{{ $management_fee }}</td>
        <td align="right" valign="middle" style="font-weight:bold;">{{ $ppn }}</td>
        <td align="right" valign="middle" style="font-weight:bold;">{{ $pph }}</td>
        <td align="right" valign="middle" style="font-weight:bold;">{{ $total_tagihan }}</td>
    </tr>

    {{-- TERBILANG --}}
    <tr>
        <td colspan="2">
            Terbilang :
        </td>
        <td colspan="8" style="font-weight:bold;">
            <i>{{ $terbilang }}</i>
        </td>
    </tr>
    <tr>
        <td>Mohon Pembayaran Transfer Ke Rekening</td>
    </tr>
    <tr>
        <td>Bank BCA (Bank Central Asia) - Surabaya</td>
    </tr>

    {{-- FOOTER --}}
    <tr>
        <td>A/N</td>
        <td colspan="8" style="font-weight:bold;">: PT. MITRA JUA ABADI</td>
        <td colspan="3" align="center">
            SIDOARJO, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </td>
    </tr>
    <tr>
        <td>No. Rek</td>
        <td colspan="8" style="font-weight:bold;">: 4293030505</td>
        <td colspan="3"></td>
    </tr>

    <tr>
        <td colspan="10" height="60"></td>
    </tr>

    <tr>
        <td colspan="9"></td>
        <td colspan="3" align="center" style="font-weight:bold">
            {{ $nama }}
        </td>
    </tr>
    <tr>
        <td colspan="9"></td>
        <td colspan="3" align="center">{{ $jabatan }}</td>
    </tr>
</table>
