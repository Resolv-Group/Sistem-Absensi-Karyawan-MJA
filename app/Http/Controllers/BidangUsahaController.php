<?php

namespace App\Http\Controllers;

use App\Models\BidangUsaha;
use Illuminate\Http\Request;

class BidangUsahaController extends Controller
{
    public function tambahBidangUsaha(Request $request)
    {
        try {
            // 1. Validation
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:bidang_usaha,nama',
            ]);
            
            // 2. Create Data
            $bidang = BidangUsaha::create([
                'nama' => $validated['nama'],
            ]);

            // 3. Success Response
            return response()->json(
                [
                    'val' => $bidang->id,
                    'label' => $bidang->nama,
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
