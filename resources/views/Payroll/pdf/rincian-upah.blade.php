<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $data->nama }}</title>
    <style>
        /* General Setup for dompdf */
        @page { margin: 0.5in; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            background-color: #ffffff;
        }

        /* Header Style */
        .header-container {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .document-title {
            float: right;
            font-size: 16px;
            color: #7f8c8d;
            font-weight: normal;
        }
        .clear { clear: both; }

        /* Info Grid */
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .label {
            color: #95a5a6;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            width: 100px;
        }
        .value {
            font-weight: bold;
            color: #2c3e50;
            width: 200px;
        }

        /* Earnings & Deductions Tables */
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            border-left: 5px solid #3498db; /* Blue accent */
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .payroll-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f1f1;
        }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .currency-symbol { color: #bdc3c7; width: 30px; }

        /* Totals */
        .subtotal-row td {
            background-color: #fafafa;
            font-weight: bold;
            border-bottom: 1px solid #2c3e50;
        }

        /* Highlight Box for Take Home Pay */
        .thp-wrapper {
            margin-top: 30px;
            text-align: right;
        }
        .thp-box {
            display: inline-block;
            background-color: #2c3e50; /* Premium Dark Navy */
            color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            min-width: 250px;
        }
        .thp-label {
            font-size: 11px;
            text-transform: uppercase;
            opacity: 0.8;
            display: block;
            margin-bottom: 5px;
        }
        .thp-amount {
            font-size: 24px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            background-color: #fafafa;
        }
        .footer-text {
            font-size: 9px;
            color: #95a5a6;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        <span class="company-name">PT. MITRA JUA ABADI</span>
        <span class="document-title">RINCIAN UPAH KARYAWAN</span>
        <div class="clear"></div>
    </div>

    <!-- Employee & Info Section -->
    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td class="value">{{ $data->nama }}</td>
            <td class="label">Periode Gaji</td>
            <td class="value">{{ \Carbon\Carbon::parse($data->history->period_start)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($data->history->period_end)->translatedFormat('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">ID Pekerja</td>
            <td class="value">{{ $data->id_pekerja }}</td>
            <td class="label">Tanggal Cetak</td>
            <td class="value">{{ now()->translatedFormat('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Divisi/Jabatan</td>
            <td class="value">{{ $data->divisi ?? '-' }}/{{ $data->jabatan ?? '-' }}</td>
            <td class="label">Email</td>
            <td class="value">{{ $data->email ?? '-' }}</td>
        </tr>
    </table>

    <!-- Section: PENDAPATAN (EARNINGS) -->
    <div class="section-title">I. Pendapatan (Earnings)</div>
    <table class="payroll-table">
        <tr>
            <td>Upah Pokok / Hasil Produksi (Basic Salary)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->upah_pokok, 0, ',', '.') }}</td>
        </tr>
        @if($data->history->unit && $data->history->unit->sistem_pengajian == 1)
        <tr>
            <td>Upah Lembur (Overtime)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->lembur, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Upah Lembur Hari Libur (HBN)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->lembur_hbn, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td>Insentif</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->insentif, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan / Penyesuaian (+)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->tunjangan, 0, ',', '.') }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL PENDAPATAN KOTOR (A)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->upah_pokok + $data->lembur + $data->lembur_hbn + $data->insentif + $data->tunjangan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Section: POTONGAN (DEDUCTIONS) -->
    <div class="section-title">II. Potongan (Deductions)</div>
    <table class="payroll-table">
        <tr>
            <td>Absensi / Keterlambatan / Potongan Lainnya</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">{{ number_format($data->potongan, 0, ',', '.') }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL POTONGAN (B)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">({{ number_format($data->potongan, 0, ',', '.') }})</td>
        </tr>
    </table>

    <!-- Final Summary Section -->
    <div class="thp-wrapper">
        <div class="thp-box">
            <span class="thp-label">Gaji Bersih Diterima (Take Home Pay)</span>
            <span class="thp-amount">Rp {{ number_format($data->take_home_pay, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- STICKY FOOTER -->
    <div class="footer">
        <div class="footer-text">
            <strong>DOKUMEN RAHASIA (CONFIDENTIAL)</strong><br>
            Slip gaji ini dihasilkan secara otomatis oleh HR System PT. Mitra Jua Abadi.<br>
            Silahkan hubungi Departemen HR jika terdapat ketidaksesuaian data.
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
    </div>
    
</body>
</html>