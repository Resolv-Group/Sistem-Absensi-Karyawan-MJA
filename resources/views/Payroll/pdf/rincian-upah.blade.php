<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - Elvina Mandasari</title>
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
        <span class="company-name">PT. GLOBAL LOGISTICS INDO</span>
        <span class="document-title">RINCIAN UPAH KARYAWAN</span>
        <div class="clear"></div>
    </div>

    <!-- Employee & Info Section -->
    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td class="value">Elvina Mandasari</td>
            <td class="label">Periode Gaji</td>
            <td class="value">01 Apr 2026 - 21 Apr 2026</td>
        </tr>
        <tr>
            <td class="label">ID Karyawan</td>
            <td class="value">1.28398E+15</td>
            <td class="label">Status Pajak</td>
            <td class="value">K/1 (Menikah/1 Anak)</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="value">SC / Operator Foreman</td>
            <td class="label">Bank</td>
            <td class="value">BCA - **** 5592</td>
        </tr>
        <tr>
            <td class="label">Divisi</td>
            <td class="value">Insectisida / Borongan</td>
            <td class="label">Status Kerja</td>
            <td class="value">Karyawan Tetap</td>
        </tr>
    </table>

    <!-- Section: PENDAPATAN (EARNINGS) -->
    <div class="section-title">I. Pendapatan (Earnings)</div>
    <table class="payroll-table">
        <tr>
            <td>Upah Pokok (Basic Salary)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">5.200.000</td>
        </tr>
        <tr>
            <td>Upah Lembur (Overtime)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">57.803</td>
        </tr>
        <tr>
            <td>Upah Lembur Hari Libur (HBN)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">115.606</td>
        </tr>
        <tr>
            <td>Tunjangan Jabatan / Transport</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">250.000</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL PENDAPATAN KOTOR (A)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">5.623.409</td>
        </tr>
    </table>

    <!-- Section: POTONGAN (DEDUCTIONS) -->
    <div class="section-title">II. Potongan (Deductions)</div>
    <table class="payroll-table">
        <tr>
            <td>Absensi / Keterlambatan (Unpaid Leave)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">400.000</td>
        </tr>
        <tr>
            <td>BPJS Ketenagakerjaan (JHT/JKM)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">104.500</td>
        </tr>
        <tr>
            <td>BPJS Kesehatan</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">52.000</td>
        </tr>
        <tr>
            <td>Potongan Lain-lain</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">0</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL POTONGAN (B)</td>
            <td class="currency-symbol">Rp</td>
            <td class="text-right">(556.500)</td>
        </tr>
    </table>

    <!-- Final Summary Section -->
    <div class="thp-wrapper">
        <div class="thp-box">
            <span class="thp-label">Gaji Bersih Diterima (Take Home Pay)</span>
            <span class="thp-amount">Rp 5.066.909</span>
        </div>
    </div>

    <!-- Footer / Legal Note -->

    <!-- STICKY FOOTER -->
    <div class="footer">
        <div class="footer-text">
            <strong>DOKUMEN RAHASIA (CONFIDENTIAL)</strong><br>
            Slip gaji ini dihasilkan secara otomatis oleh HR System PT. Global Logistics Indo.<br>
            Silahkan hubungi Departemen HR jika terdapat ketidaksesuaian data.
            <p>Dicetak pada: 21 April 2026, 09:45 WIB</p>
        </div>
    </div>
    
</body>
</html>