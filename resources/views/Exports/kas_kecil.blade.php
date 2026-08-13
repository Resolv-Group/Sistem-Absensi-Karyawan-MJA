@php
    $saldoAwal = $data->sum('debit');
    $totalPengeluaran = $data->sum('kredit');
    $sisaSaldo = $saldoAwal - $totalPengeluaran;
    $pengajuanKembali = $totalPengeluaran; 
    
    $saldoBerjalan = 0; 
@endphp

<table>
    <!-- BAGIAN HEADER -->
    <thead>
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 12px; vertical-align: middle;">Dibayarkan kepada: {{ $kepada }}</td>
            <td colspan="3" style="text-align: center; font-size: 14px; font-weight: bold; vertical-align: middle; text-decoration: underline;">BUKTI KAS KELUAR</td>
            <!-- Menggunakan waktu saat ini berdasarkan timezone lokasi (WIB/Jakarta) -->
            <td colspan="2" style="text-align: right; font-weight: bold; font-size: 12px; vertical-align: middle;">Tanggal: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d-m-Y') }}</td>
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
    
    <!-- BAGIAN DATA TABEL -->
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
    
    <!-- BAGIAN FOOTER & REKAP -->
    <tfoot>
        <tr></tr>
        
<tr>
            <td colspan="3"></td> 
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Saldo Awal</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right;">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Total Pengeluaran</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right; color: red;">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Sisa Saldo</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right;">{{ number_format($sisaSaldo, 0, ',', '.') }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border: 1px solid black; font-weight: bold; background-color: #e6e6e6;">Pengajuan Pengisian Kembali</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right; color: green;">{{ number_format($pengajuanKembali, 0, ',', '.') }}</td>
            <td></td>
        </tr>

        <tr></tr>
        <tr></tr>

        <!-- BAGIAN TANDA TANGAN (Dibagi menjadi 4 Kolom secara rata) -->
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">Pembukuan</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">Mengetahui</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">Kasir</td>
            <td style="text-align: center; font-weight: bold;">Penerima</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $pembukuan }}</td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $mengetahui }}</td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $kasir }}</td>
            <td style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $penerima }}</td>
        </tr>

        <!-- BAGIAN CATATAN BAWAH -->
        <tr></tr>
        <tr>
            <td colspan="7" style="border: 1px solid black; vertical-align: top; height: 60px;">
                <strong>Catatan:</strong><br>
                {{ $catatan ?? '-' }}
            </td>
        </tr>
    </tfoot>
</table>