<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 14px; font-weight: bold;">DAFTAR ASSET UNIT</th>
        </tr>
        <tr></tr> <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Nama Barang</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Keterangan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Jumlah</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Tahun Perolehan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Harga Perolehan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Lokasi</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #1B365D; color: #ffffff;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalJumlah = 0;
            $totalHargaPerolehan = 0;
        @endphp

        @foreach($assets as $index => $asset)
            @php
                $totalJumlah += $asset->jumlah;
                $totalHargaPerolehan += $asset->harga_perolehan;
            @endphp
            <tr>
                <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid black;">{{ $asset->nama_barang }}</td>
                <td style="border: 1px solid black;">{{ $asset->keterangan }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $asset->jumlah }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $asset->tahun_perolehan }}</td>
                <td style="border: 1px solid black; text-align: right;">{{ $asset->harga_perolehan }}</td>
                <td style="border: 1px solid black;">{{ $asset->lokasi }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $asset->status }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">TOTAL KESELURUHAN</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; background-color: #e6e6e6;">{{ $totalJumlah }}</td>
            <td style="border: 1px solid black; background-color: #e6e6e6;"></td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #e6e6e6;">{{ $totalHargaPerolehan }}</td>
            <td style="border: 1px solid black; background-color: #e6e6e6;"></td>
            <td style="border: 1px solid black; background-color: #e6e6e6;"></td>
        </tr>
    </tbody>
</table>