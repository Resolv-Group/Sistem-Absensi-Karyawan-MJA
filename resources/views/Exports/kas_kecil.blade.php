@php
    $saldoAwal = $data->sum('debit');
    $totalPengeluaran = $data->sum('kredit');
    $sisaSaldo = $saldoAwal - $totalPengeluaran;
    $pengajuanKembali = $totalPengeluaran; 
    
    $saldoBerjalan = 0; 
@endphp

<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14px; font-weight: bold;">LAPORAN KAS KECIL</th>
        </tr>
        <tr></tr>
        
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Tanggal</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Akun</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Keterangan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Debit</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Kredit</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $row)
            @php
                $saldoBerjalan += $row->debit;
                $saldoBerjalan -= $row->kredit;
            @endphp
            <tr>
                <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $row->akun }}</td>
                <td style="border: 1px solid black;">{{ $row->keterangan }}</td>
                
                <td style="border: 1px solid black; text-align: right;">{{ $row->debit }}</td>
                <td style="border: 1px solid black; text-align: right;">{{ $row->kredit }}</td>
                <td style="border: 1px solid black; text-align: right; background-color: #f9f9f9;">{{ $saldoBerjalan }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">TOTAL KESELURUHAN</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">{{ $saldoAwal }}</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">{{ $totalPengeluaran }}</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">{{ $sisaSaldo }}</td>
        </tr>
    </tbody>
    
    <tfoot>
        <tr></tr>
        
        <tr>
            <td colspan="3"></td> <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Saldo Awal</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right;">{{ $saldoAwal }}</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Total Pengeluaran</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; color: red;">{{ $totalPengeluaran }}</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Sisa Saldo</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right;">{{ $sisaSaldo }}</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Pengajuan Pengisian Kembali</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; color: green;">{{ $pengajuanKembali }}</td>
            <td colspan="2"></td>
        </tr>

        <tr></tr><tr></tr>
        <tr>
            <td colspan="2" style="text-align: center;">Diajukan Oleh,</td>
            <td></td>
            <td colspan="2" style="text-align: center;">Diperiksa Oleh,</td>
            <td colspan="2" style="text-align: center;">Disetujui Oleh,</td>
        </tr>
        <tr></tr><tr></tr><tr></tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $diajukan }}</td>
            <td></td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $diperiksa }}</td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $disetujui }}</td>
        </tr>
    </tfoot>
</table>