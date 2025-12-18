<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraKerja extends Model
{
    use HasFactory;

    protected $table = 'mitra_kerja';

    protected $fillable = [
        'nama_mitra',
        'bidang_usaha_id',
        'pimpinan',
        'telp_perusahaan',
        'status_pajak',
        'alamat',
        'tgl_mulai_kerjasama',
        'tgl_akhir_mou',
        'status_mou',
        'status_aktif',
        'foto',
    ];

    public function getImageBase64Attribute()
    {
        // Check if the 'foto' column has data
        if ($this->foto) {
            // Convert binary data to a Base64 string that HTML can read
            return 'data:image/jpeg;base64,' . base64_encode($this->foto);
        }

        // Return null if no photo exists
        return null;
    }

    public function bidangUsaha()
    {
        return $this->belongsTo(BidangUsaha::class, 'bidang_usaha_id');
    }

    protected $appends = ['status_mou', 'status_badge'];

    public function getStatusMouAttribute(): string
    {
        $diff = Carbon::today()->diffInDays(
            Carbon::parse($this->tgl_akhir_mou),
            false
        );

        if ($diff < 0) return 'Tidak AKtif';
        if ($diff <= 30) return 'Perpanjangan';
        return 'Aktif Disnaker';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status_mou) {
            'Tidak Aktif' => [
                'text' => 'Habis',
                'bg' => 'bg-red-100',
                'textColor' => 'text-red-800',
                'border' => 'border-red-200',
                'dot' => 'bg-red-500',
            ],
            'Perpanjangan' => [
                'text' => 'Segera Habis',
                'bg' => 'bg-yellow-100',
                'textColor' => 'text-yellow-800',
                'border' => 'border-yellow-200',
                'dot' => 'bg-yellow-500',
            ],
            default => [
                'text' => 'Aktif Disnaker',
                'bg' => 'bg-green-100',
                'textColor' => 'text-green-800',
                'border' => 'border-green-200',
                'dot' => 'bg-green-500',
            ],
        };
    }

    public function getTelpFormattedAttribute()
{
    if (!$this->telp_perusahaan) return '-';

    // 1. Buang selain angka
    $number = preg_replace('/\D/', '', $this->telp_perusahaan);

    // 2. Normalisasi +62 → 0
    if (str_starts_with($number, '62')) {
        $number = '0' . substr($number, 2);
    }

    // 3. Nomor HP (08...)
    if (str_starts_with($number, '08')) {
        // 0812-0000-0000 (4-4-4+)
        return preg_replace(
            '/^(\d{4})(\d{4})(\d{4,})$/',
            '$1-$2-$3',
            $number
        );
    }

    // 4. Telepon rumah (02x / 03x / dll)
    // contoh: 021xxxxxxx → 021-xxxxxxx
    // contoh: 031xxxxxx  → 031-xxxxxx
    if (preg_match('/^0\d{2}/', $number)) {
        return preg_replace(
            '/^(\d{3})(\d+)$/',
            '$1-$2',
            $number
        );
    }

    // 5. Fallback (kalau format aneh)
    return $number;
}

    public function getAlamatFormattedAttribute()
    {
        if (!$this->alamat) return '-';

        return collect(explode(',', $this->alamat))
            ->map(fn ($line) => trim($line))
            ->implode('<br>');
    }
}
