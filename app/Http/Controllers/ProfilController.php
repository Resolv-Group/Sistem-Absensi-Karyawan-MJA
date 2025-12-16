<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfilController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            return back()->withErrors(['system' => 'Data staff tidak ditemukan.']);
        }

        // ================= VALIDATION =================
        $request->validate([
            'email' => 'nullable|email',
            'telp' => 'nullable|string|max:16',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // password section
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'email.email' => 'Format email tidak valid.',
            'telp.max' => 'No telepon maksimal 16 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg/jpeg/png.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $logoutRequired = false;

        // ================= UPDATE EMAIL =================
        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $staff->email = $request->email;
            $logoutRequired = true;
        }

        // ================= UPDATE PHONE =================
        if ($request->filled('telp')) {
            $staff->telp = $request->telp;
        }

        // ================= UPDATE FOTO =================
        if ($request->hasFile('foto')) {
            $staff->foto = file_get_contents($request->file('foto')->getRealPath());
        }

        // ================= UPDATE PASSWORD =================
        if ($request->filled('new_password')) {

            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Password saat ini salah.',
                ]);
            }

            $user->password = Hash::make($request->new_password);
            $logoutRequired = true;
        }

        // ================= SAVE =================
        $user->save();
        $staff->save();

        History::create([
            'foreign_id' => $staff->id,
            'nama_tabel' => 'staff',
            'updated_by' => $user->id,
            'jabatan' => optional($user->staff)->jabatan ?? 'system',
            'when' => now(),
        ]);

        // ================= FORCE LOGOUT =================
        if ($logoutRequired) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'Profil berhasil diperbarui. Silakan login kembali.');
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
