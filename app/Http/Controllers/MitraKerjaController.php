<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MitraKerjaController extends Controller
{
    function viewMitraKerjaMain() {
        return view('Mitra Kerja.main-mitra-kerja');
    }

    function viewTambahMitraKerja() {
        return view('Mitra Kerja.CRUD.tambah-mitra-kerja');
    }

    function viewDetailMitraKerja(Request $request) {

    }

    function tambahMitraKerja(Request $request) {

    }

    function ubahMitraKerja(Request $request) {

    }

    function updateMitraKerja(Request $request) {

    }
}
