<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function tambahSatuan(Request $request)
    {
        try {
            // 1. Validation
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:satuan,nama',
            ]);

            // 2. Create Data
            $satuan = Satuan::create([
                'nama' => $validated['nama'],
            ]);

            // 3. Success Response
            return response()->json(
                [
                    'val' => $satuan->id,
                    'label' => $satuan->nama,
                ],
                200,
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors specifically
            return response()->json(
                [
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            // Return generic server errors (like Database issues)
            return response()->json(
                [
                    'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
