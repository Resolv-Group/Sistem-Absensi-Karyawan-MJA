<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pekerja;
use Illuminate\Support\Facades\Storage;

class PekerjaController extends Controller
{
    function viewPekerjaMain(){

        return view('Pekerja.main-pekerja');
    }

    function viewTambahPekerja() {
        return view('Pekerja.CRUD.tambah-pekerja');
    }
    function tambahPekerja(request $request) {
    {
        // dd($request->all());
    
        // ✅ Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:pekerja,nik',
            'no_kk' => 'required|digits:16',
            'tempat_lahir' => 'required|string|max:100',
            'tgl_lahir' => 'required|date',
            'kelamin' => 'required|boolean',
            'pendidikan' => 'required|string',
            'status_kawin' => 'required|string',
            'anak' => 'nullable|integer|min:0',
            'tgl_bergabung' => 'required|date',
            'tgl_resign' => 'nullable|date',

            'alamat' => 'required|string',
            'desa' => 'required|string',
            'rt' => 'nullable|integer',
            'rw' => 'nullable|integer',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
            'provinsi' => 'required|string',

            'email' => 'nullable|email',
            'telp' => 'nullable|string|max:16',

            'nama_rek' => 'nullable|string',
            'rekening' => 'nullable|string|max:30',

            'nama_emergency' => 'required|string|max:255',
            'telp_emergency' => 'required|string|max:16',
            'hubungan_emergency' => 'required|string',

            'ibu_kandung' => 'string|max:255',

            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // dd($request->all());

        // ✅ Upload foto
        // $fotoPath = null;
        // if ($request->hasFile('foto_profil')) {
        //     $fotoPath = $request->file('foto_profil')->store('foto-pekerja', 'public');
        // }

        // ✅ Simpan ke database
        Pekerja::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'kelamin' => $request->kelamin,
            'pendidikan' => $request->pendidikan,
            'status_kawin' => $request->status_kawin,
            'anak' => $request->anak ?? 0,
            'tgl_bergabung' => $request->tgl_bergabung,
            'tgl_resign' => $request->tgl_resign,

            'alamat' => $request->alamat,
            'desa' => $request->desa,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,

            'email' => $request->email,
            'telp' => $request->telp,

            'rekening' => $request->rekening,
            'nama_rek' => $request->nama_rek,

            'nama_emergency' => $request->nama_emergency,
            'telp_emergency' => $request->telp_emergency,
            'hubungan_emergency' => $request->hubungan_emergency,
            'ibu_kandung' => $request->ibu_kandung,

            'foto' => $request->foto,

            'status_aktif' => 1
        ]);

        return redirect('/daftar-pekerja')->with('success', 'Pekerja berhasil ditambahkan.');
    }
    
    }
}