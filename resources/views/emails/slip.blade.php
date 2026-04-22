<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #2c3e50; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 24px; letter-spacing: 1px;">PT. MITRA JUA ABADI</h2>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.9;">Rincian Upah Karyawan</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px;">Yth. Bapak/Ibu <strong>{{ $details->nama }}</strong>,</p>
            
            <p>Terlampir adalah rincian upah atau slip gaji Anda untuk periode ini. Dokumen elektronik ini diterbitkan secara otomatis dan sah sebagai bukti pembayaran upah Anda.</p>
            
            <div style="background-color: #f8f9fa; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
                <p style="margin: 0 0 10px; font-weight: bold;">Ringkasan:</p>
                <ul style="margin: 0; padding-left: 20px; color: #555;">
                    <li><strong>Periode Gaji:</strong> {{ \Carbon\Carbon::parse($details->history->period_start)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($details->history->period_end)->translatedFormat('d M Y') }}</li>
                    <li><strong>ID Pekerja:</strong> {{ $details->id_pekerja }}</li>
                </ul>
            </div>

            <p>Silakan unduh dokumen PDF terlampir untuk melihat detail lengkap perhitungan gaji, tunjangan, dan potongan Anda.</p>
            
            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                Jika Anda memiliki pertanyaan mengenai rincian ini, silakan hubungi tim HR kami.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f4f6f8; padding: 15px; text-align: center; border-top: 1px solid #e0e0e0;">
            <p style="margin: 0; font-size: 12px; color: #999;">
                &copy; {{ date('Y') }} PT. Mitra Jua Abadi. Hak cipta dilindungi.<br>
                Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
