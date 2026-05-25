<?php

namespace App\Imports;

use App\Models\Borongan;
use App\Models\Kategori;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BoronganImport implements ToModel, WithHeadingRow
{
    protected $id_unit;

    // Menangkap id_unit dari Controller jika data borongan dipisah per Unit
    public function __construct($id_unit = null)
    {
        $this->id_unit = $id_unit;
    }

    /**
     * Sesuai file Excel Anda, Header ("Nama Item", "Kategori", dll) berada di Baris ke-2.
     * Baris 1 yang berisi keterangan akan otomatis diabaikan.
     */
    public function headingRow(): int
    {
        return 2; 
    }

    public function model(array $row)
    {
        // Abaikan jika nama item kosong (mencegah error pada baris kosong di Excel)
        if (empty($row['nama_item'])) {
            return null;
        }

        // ========================================================
        // 1. AUTO CREATE KATEGORI
        // ========================================================
        
        $kategori = Kategori::firstOrCreate([
            'nama' => trim($row['kategori'])
        ]);

        // 3. AMBIL ID SATUAN (Tipe data harus Integer)
        $satuan = Satuan::firstOrCreate([
            'nama' => trim($row['satuan'])
        ]);
        // ========================================================
        // 3. INSERT / UPDATE TABEL BORONGAN
        // ========================================================
        return Borongan::updateOrCreate(
            [
                // Kunci pencarian: Nama Item
                // Jika item sudah ada di database, harganya hanya akan diupdate (ditimpa)
                'nama_item' => trim($row['nama_item']),
            ],
            [
                'id_unit'        => $this->id_unit ?? null,
                
                // Menyesuaikan dengan nama kolom Excel: "Harga Barang Unit" & "Harga Barang Pekerja"
                'harga_unit'     => $row['harga_barang_unit'] ?? 0,
                'harga_pekerja'  => $row['harga_barang_pekerja'] ?? 0,
                
                'kategori'       => (int) $kategori->id, 
                'satuan'         => (int) $satuan->id,
                
                // Menyesuaikan dengan nama kolom Excel: "Max Rej. Subkon(%)"
                'max_rej_subkon' => $row['max_rej_subkon'] ?? 0,
                
                'status_aktif'   => 1, // Otomatis aktif saat diimport
            ]
        );
    }
}