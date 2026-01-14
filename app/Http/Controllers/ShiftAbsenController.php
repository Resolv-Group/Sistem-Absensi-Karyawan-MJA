<?php

namespace App\Http\Controllers;

use App\Models\Shift_Absen;
use Illuminate\Http\Request;

class ShiftAbsenController extends Controller
{
    public function update(Request $request, $id)
    {
        $shifts = $request->shifts;

        if (is_string($shifts)) {
            $shifts = json_decode($shifts, true);
        }

        $request->merge(['shifts' => $shifts]);

        $request->validate([
            'shifts' => 'required|array',
            'shifts.*.nama' => 'required|string|max:50',
            'shifts.*.waktu_masuk' => 'required',
            'shifts.*.waktu_keluar' => 'required',
        ]);

        // 1️⃣ Ambil ID shift lama dari DB
        $existingIds = Shift_Absen::where('id_unit', $id)
            ->pluck('id')
            ->toArray();

        // 2️⃣ Ambil ID shift dari request (yang sudah ada)
        $requestIds = collect($request->shifts)
            ->pluck('id')
            ->filter() // buang null (shift baru)
            ->toArray();

        // 3️⃣ Cari shift yang harus dihapus
        $idsToDelete = array_diff($existingIds, $requestIds);

        if (!empty($idsToDelete)) {
            Shift_Absen::whereIn('id', $idsToDelete)->delete();
        }

        // 4️⃣ Update / Insert
        foreach ($request->shifts as $shift) {

            if (!empty($shift['id'])) {
                // UPDATE
                Shift_Absen::where('id', $shift['id'])
                    ->where('id_unit', $id)
                    ->update([
                        'nama' => $shift['nama'],
                        'waktu_masuk' => $shift['waktu_masuk'],
                        'waktu_keluar' => $shift['waktu_keluar'],
                    ]);
            } else {
                // INSERT
                Shift_Absen::create([
                    'id_unit' => $id,
                    'nama' => $shift['nama'],
                    'waktu_masuk' => $shift['waktu_masuk'],
                    'waktu_keluar' => $shift['waktu_keluar'],
                ]);
            }
        }

        return back()->with('success', 'Shift berhasil diperbarui');
    }



}