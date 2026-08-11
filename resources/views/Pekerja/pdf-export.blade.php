<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pekerja - {{ $pekerja->nama }}</title>
    <style>
        .photo-container {
            text-align: right;
        }
        .photo-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            background-color: #ffffff;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #1e3a8a;
            font-size: 20px;
        }
        .section-title {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td {
            padding: 6px;
            vertical-align: top;
        }
        .label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .value {
            width: 70%;
        }
        .colon {
            width: 2%;
            text-align: center;
        }
        .photo-container {
            text-align: right;
            margin-bottom: -80px; /* Tarik foto ke atas */
        }
        .photo-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #ccc;
            border-radius: 8px;
        }
        .grid-half {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>DATA DETAIL PEKERJA</h1>
        <p>ID Pekerja: {{ $pekerja->id_pekerja }}</p>
    </div>

    <!-- Tampilkan Foto Jika Ada -->
<!-- LAYOUT DUA KOLOM UNTUK IDENTITAS & FOTO -->
    <table style="width: 100%; border: none; margin-bottom: 0; padding: 0;">
        <tr>
            <!-- Kolom Kiri: Judul dan Data Identitas (Lebar 75%) -->
            <td style="width: 75%; vertical-align: top; padding: 0;">
                <div class="section-title" style="margin-top: 0;">Identitas Pribadi</div>
                <table style="width: 100%;">
                    <tr><td class="label">NIK</td><td class="colon">:</td><td class="value">{{ $pekerja->nik }}</td></tr>
                    <tr><td class="label">Nama Lengkap</td><td class="colon">:</td><td class="value">{{ $pekerja->nama }}</td></tr>
                    <tr><td class="label">Tempat, Tgl Lahir</td><td class="colon">:</td><td class="value">{{ $pekerja->tempat_lahir }}, {{ \Carbon\Carbon::parse($pekerja->tgl_lahir)->translatedFormat('d F Y') }}</td></tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td><td class="colon">:</td>
                        <td class="value">{{ $pekerja->kelamin == '1' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr><td class="label">Pendidikan</td><td class="colon">:</td><td class="value">{{ $pekerja->pendidikan }}</td></tr>
                    <tr><td class="label">Status Perkawinan</td><td class="colon">:</td><td class="value">{{ $pekerja->status_kawin }} (Anak: {{ $pekerja->anak ?? 0 }})</td></tr>
                    <tr><td class="label">BPJS Ketenagakerjaan</td><td class="colon">:</td><td class="value">{{ $pekerja->kpj ?? '-' }}</td></tr>
                    <tr><td class="label">BPJS Kesehatan</td><td class="colon">:</td><td class="value">{{ $pekerja->naker ?? '-' }}</td></tr>
                    <tr><td class="label">Nomor KK</td><td class="colon">:</td><td class="value">{{ $pekerja->no_kk ?? '-' }}</td></tr>
                    <tr><td class="label">Tanggal Bergabung</td><td class="colon">:</td><td class="value">{{ \Carbon\Carbon::parse($pekerja->tgl_bergabung)->translatedFormat('d F Y') }}</td></tr>
                    <tr><td class="label">Tanggal Resign</td><td class="colon">:</td><td class="value">{{ $pekerja->tgl_resign ? \Carbon\Carbon::parse($pekerja->tgl_resign)->translatedFormat('d F Y') : 'Masih Aktif' }}</td></tr>
                </table>
            </td>

            <!-- Kolom Kanan: Tempat Foto Berada (Lebar 25%) -->
            <td style="width: 25%; vertical-align: top; text-align: right; padding: 0; padding-top: 5px;">
                <div class="photo-container">
                    @if($pekerja->foto)
                        @php
                            $foto = $pekerja->foto;
                            $base64 = null;
                            try {
                                if (filter_var($foto, FILTER_VALIDATE_URL)) {
                                    $context = stream_context_create(["ssl" => ["verify_peer"=>false, "verify_peer_name"=>false]]);
                                    $image = @file_get_contents($foto, false, $context);
                                    if ($image) $base64 = 'data:image/jpeg;base64,' . base64_encode($image);
                                } elseif (str_contains($foto, 'data:image')) {
                                    $base64 = $foto;
                                } else {
                                    $path = storage_path('app/public/' . $foto);
                                    if (file_exists($path)) {
                                        $mime = mime_content_type($path);
                                        $image = file_get_contents($path);
                                        $base64 = 'data:' . $mime . ';base64,' . base64_encode($image);
                                    } else {
                                        $base64 = 'data:image/jpeg;base64,' . base64_encode($foto);
                                    }
                                }
                            } catch (\Exception $e) { $base64 = null; }
                        @endphp
                        
                        @if($base64)
                            <img src="{{ $base64 }}" class="photo-img" alt="Foto Profil">
                        @else
                            <div class="photo-img" style="display:inline-block; line-height:120px; text-align:center; background:#f3f4f6; color:#9ca3af; font-size:10px;">Gagal Dimuat</div>
                        @endif
                    @else
                        <div class="photo-img" style="display:inline-block; line-height:120px; text-align:center; background:#f3f4f6; color:#9ca3af; font-size:10px;">No Photo</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 2: Alamat Domisili -->
    <div class="section-title">Alamat Domisili</div>
    <table>
        <tr><td class="label">Jalan / Gedung</td><td class="colon">:</td><td class="value">{{ $pekerja->alamat }}</td></tr>
        <tr><td class="label">Kelurahan / Desa</td><td class="colon">:</td><td class="value">{{ $pekerja->desa }}</td></tr>
        <tr><td class="label">RT / RW</td><td class="colon">:</td><td class="value">{{ $pekerja->rt ?? '-' }} / {{ $pekerja->rw ?? '-' }}</td></tr>
        <tr><td class="label">Kecamatan</td><td class="colon">:</td><td class="value">{{ $pekerja->kecamatan }}</td></tr>
        <tr><td class="label">Kota / Kabupaten</td><td class="colon">:</td><td class="value">{{ $pekerja->kota }}</td></tr>
        <tr><td class="label">Provinsi</td><td class="colon">:</td><td class="value">{{ $pekerja->provinsi }}</td></tr>
    </table>

    <div style="width: 100%; margin-top: 20px;">
        <!-- SECTION 3: Kontak & Rekening (Kiri) -->
        <div class="grid-half">
            <div class="section-title" style="margin-right: 10px;">Kontak & Rekening</div>
            <table>
                <tr><td class="label" style="width: 40%">No. Telepon</td><td class="colon">:</td><td class="value">{{ $pekerja->telp ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">Email</td><td class="colon">:</td><td class="value">{{ $pekerja->email ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">Nama Bank</td><td class="colon">:</td><td class="value">{{ $pekerja->nama_rek ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">No. Rekening</td><td class="colon">:</td><td class="value">{{ $pekerja->rekening ?? '-' }}</td></tr>
            </table>
        </div>

        <!-- SECTION 4: Kontak Darurat (Kanan) -->
        <div class="grid-half">
            <div class="section-title">Kontak Darurat</div>
            <table>
                <tr><td class="label" style="width: 40%">Nama Kontak</td><td class="colon">:</td><td class="value">{{ $pekerja->nama_emergency ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">Hubungan</td><td class="colon">:</td><td class="value">{{ $pekerja->hubungan_emergency ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">No. Telepon</td><td class="colon">:</td><td class="value">{{ $pekerja->telp_emergency ?? '-' }}</td></tr>
                <tr><td class="label" style="width: 40%">Ibu Kandung</td><td class="colon">:</td><td class="value">{{ $pekerja->ibu_kandung ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <!-- SECTION 5: Penempatan Unit -->
    @if($pekerja->id_mitra || $pekerja->id_unit)
    <div class="section-title" style="clear: both; margin-top: 20px;">Penempatan Saat Ini</div>
    <table>
        <tr>
            <td class="label">Mitra Kerja</td><td class="colon">:</td>
            <td class="value">{{ $pekerja->mitra->nama_mitra ?? 'Belum ada Mitra' }}</td>
        </tr>
        <tr>
            <td class="label">Unit / Site</td><td class="colon">:</td>
            <td class="value">{{ $pekerja->unit->nama_unit ?? 'Belum ada Unit' }}</td>
        </tr>
    </table>
    @endif

</body>
</html>