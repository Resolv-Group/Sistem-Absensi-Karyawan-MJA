@php
    use Carbon\Carbon;
@endphp

<table width="100%" cellpadding="4">
    {{-- HEADER ATAS --}}
    <tr>
        <td colspan="18" style="font-weight:bold; font-family: Agency FB; font-size: 10px; background-color:#CCCCFF;">
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
        </td>
    </tr>

    <tr>
        <td
            rowspan="26"
            style="
                font-weight:bold;
                font-family:'Agency FB';
                font-size:10px;
                white-space: nowrap;
                text-align:center;
                width:20px;
                background-color:#CCCCFF;
            ">
            PT. MITRA JUA ABADI PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA ABADI PT. MITRA JUA ABADI
        </td>

        <td colspan="3"  style="font-size:20px; font-weight:bold;font-family:'Monotype Corsiva';text-decoration:underline;"> 
            KWITANSI
        </td>
        <td></td>
        <td></td>
        <td colspan="11" align="center" valign="middle" style="font-weight:bold;text-decoration:underline;font-family:'Copperplate Gothic';font-size:25px;color:red">
            PT. MITRA JUA ABADI
        </td>
        <td
            rowspan="26"
            style="
                font-weight:bold;
                font-family:'Agency FB';
                font-size:10px;
                white-space: nowrap;
                text-align:center;
                width:20px;
                background-color:#CCCCFF;
            ">
            PT. MITRA JUA ABADI PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA ABADI PT. MITRA JUA ABADI
        </td>
    </tr>

    <tr>
        <td colspan="18"></td>
    </tr>

    {{-- INFO --}}
    <tr>
        <td colspan="3" style="font-family:'Garamond'; font-weight:bold; font-size:10px;">No</td>
        <td>:</td>
        <td></td>
        <td colspan="9" style="font-family:'Cambria'; font-weight:bold;" align="left">{{ $resi }}</td>
    </tr>

    <tr>
        <td colspan="3" style="font-family:'Garamond'; font-weight:bold; font-size:10px;">Telah terima dari</td>
        <td>:</td>
        <td></td>
        <td colspan="9" style="font-family:'Cambria'; font-weight:bold;">{{ $nama_unit }}</td>
    </tr>

    <tr>
        <td colspan="3" style="font-family:'Garamond'; font-weight:bold; font-size:10px;">Uang sejumlah</td>
        <td>:</td>
        <td></td>
        <td colspan="11" rowspan="2" style="font-family:'Monotype Corsiva'; font-weight:bold;"
        align="left" valign="top"
        >
            {{ $terbilang }}
        </td>
    </tr>

    <tr></tr>

    <tr>
        <td colspan="3" style="font-family:'Garamond'; font-weight:bold; font-size:10px;">Untuk Pembayaran</td>
        <td>:</td>
        <td>-</td>
        <td colspan="11" style="font-family:'Cambria'; font-weight:bold;">
            {{ $bidangUsaha }}   
        </td>
    </tr>

    <tr>
        <td colspan="5"></td>
        <td colspan="11" style="font-family:'Cambria'; font-weight:bold;">
            {{ $MitraKerja }}   
        </td>
    </tr>

    <tr>
        <td colspan="4"></td>
        <td>-</td>
        <td colspan="11" style="font-family:'Cambria'; font-weight:bold;">
            PERIODE : {{ $periode }}   
        </td>
    </tr>

    <tr>
        <td colspan="13"></td>
        <td style="font-family:'Cambria'; font-weight:bold; border:4px solid #000; padding:6px; ">Rp.</td>
        <td style="font-family:'Cambria'; font-weight:bold; border:4px solid #000; padding:6px;" align="center" valign="middle">{{ $total_tagihan }}</td>
    </tr>

    <tr></tr>
    <tr></tr>

    <tr>
        <td colspan="3"></td>
        <td colspan="3" style="font-family:'Cambria'; font-weight:bold;">
            No Rekening :
        </td>
        <td style="font-family:'Cambria'; font-weight:bold;">
            4293030505 BCA A/n PT Mitra Jua Abadi
        </td>
    </tr>

    <tr>
        <td colspan="6"></td>
        <td style="font-family:'Cambria'; font-weight:bold; text-decoration:underline;">
            Dimohon saat transfer ditulis beritanya ( Nomor Kwitansi )
        </td>
    </tr>

    <tr></tr>

    <tr>
        <td colspan="9"></td>
        <td colspan="7" align="center" valign="middle" style="font-size:15px;">Sidoarjo, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
    </tr>

    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <td colspan="3"></td>
        <td colspan="4"  align="center" valign="middle" style="font-family:'Bauhaus 93'; font-weight:bold; background-color:#F2DCDB; font-size:16px;">Rp.</td>
        <td align="center" valign="middle" style="font-family:'Arial Rounded MT Bold'; font-weight:bold; background-color:#F2DCDB; font-size:18px;">{{ $total_tagihan }}</td>
        <td></td>
        <td colspan="7"  align="center" valign="middle" style="font-family:'Cambria'; font-weight:bold; text-decoration:underline;">MOHAMMAD TAUFIQ, S.Kom.</td>
    </tr>

    <tr>
        <td colspan="9"></td>
        <td colspan="7" align="center" valign="middle" style="font-family:'Cambria';">DIREKTUR</td>
    </tr>

    <tr></tr>
    <tr></tr>

    <tr>
        <td colspan="17" style="font-weight:bold; font-family: Agency FB; font-size: 10px; background-color:#CCCCFF;">
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
            PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI  PT. MITRA JUA  ABADI
        </td>
    </tr>

    
</table>
