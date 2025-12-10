<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    // ==========================
    // FORM LOGIN
    // ==========================
    public function showLoginForm()
    {
        return view('login');
    }

    // ==========================
    // PROSES LOGIN
    // ==========================
    public function login(Request $request)
    {
        // ✅ VALIDASI INPUT
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi'
        ]);

        // ✅ AMBIL KREDENTIAL
        $credentials = $request->only('email', 'password');

        // ✅ CEK LOGIN
        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            return redirect()->route('view.dashboard');

        }

        // ❌ LOGIN GAGAL
        return back()->withErrors([
            'email' => 'Email atau password salah'
        ])->withInput();
    }

    // ==========================
    // LOGOUT
    // ==========================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil! Silahkan login kembali.');
}
}
