<table>
    <tr>
        <td colspan="8" align="center" style="font-family:'Times New Roman'; font-size:12px; font-weight:bold;">TANDA TERIMA UPAH KARYAWAN PT. MITRA JUA ABADI</td>
    </tr>
    <tr>
        <td colspan="8" align="center" style="font-family:'Times New Roman'; font-size:12px; font-weight:bold;">UNIT PT. ATLANTIC BIRURAYA</td>
    </tr>
    <tr>
        <td colspan="8" align="center" style="font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ $periode }}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-family:'Times New Roman'; font-size:12px;">DIVISI : {{ $divisi }}</td>
    </tr>

    <tr>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">NO</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">ID</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">NAMA</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">POSISI</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">DIVISI</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">NO.REKENING</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; white-space: normal;
                word-wrap: break-word; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">UPAH TENAGA KERJA</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#FCE4D6; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">TANDA TANGAN</td>
    </tr>
    
    @foreach($data as $row)
        <tr>
            {{-- No --}}
            <td align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px;">
                {{ $row['no'] }}
            </td>
            
            {{-- ID / NIK --}}
            <td align="center" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px;">
                {{ $row['id'] }}
            </td>
            
            {{-- Nama --}}
            <td align="left" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px; padding-left: 5px;">
                {{ strtoupper($row['nama']) }}
            </td>
            
            {{-- Posisi --}}
            <td align="center" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px;">
                {{ strtoupper($row['posisi']) }}
            </td>
            
            {{-- Divisi --}}
            <td align="center" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px;">
                {{ strtoupper($row['divisi']) }}
            </td>
            
            {{-- No Rekening --}}
            <td align="center" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px;">
                {{ $row['no_rek'] }}
            </td>
            
            {{-- Upah --}}
            <td align="right" valign="middle" style="border:1px solid #000; font-family:'Times New Roman'; font-size:12px; padding-right: 5px;">
                {{ $row['upah_tenaga_kerja'] }}
            </td>

            {{-- Kolom Tanda Tangan Zig-Zag --}}
            <td align="{{ $loop->iteration % 2 != 0 ? 'left' : 'right' }}" 
                valign="top" 
                style="border:1px solid #000; font-family:'Times New Roman'; font-size:9px; padding: 2px;">
                {{ $row['no'] }}
            </td>
        </tr>
    @endforeach

    <tr>
        <td colspan="6" align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">GRAND TOTAL UPAH</td>
        <td align="right" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ $total }}</td>
        <td style="border:1px solid #000; background-color:#F2F2F2"></td>
    </tr>

    <tr>
        <td colspan="2" align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">MENGETAHUI</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">NAMA</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">TGL</td>
        <td colspan="3" align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">PERUSAHAAN</td>
        <td align="center" valign="middle" style="border:1px solid #000; background-color:#F2F2F2; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">TTD</td>
    </tr>

    <tr>
        <td colspan="2" align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">ADMINISTRASI</td>
        <td align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ $admin }}</td>
        <td align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y')}}</td>
        <td colspan="3" align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">PT. MITRA JUA ABADI</td>
        <td style="border:1px solid #000;"></td>
    </tr>

    <tr>
        <td colspan="2" align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">SUPERVISOR</td>
        <td align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ $supervisor }}</td>
        <td align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y')}}</td>
        <td colspan="3" align="center" valign="middle" style="border:1px solid #000; height:46px; line-height:46px; font-family:'Times New Roman'; font-size:12px; font-weight:bold;">PT. MITRA JUA ABADI</td>
        <td style="border:1px solid #000;"></td>
    </tr>

</table>