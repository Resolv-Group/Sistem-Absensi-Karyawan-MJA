<?php

namespace App\Http\Controllers;

use App\Models\BidangUsaha;
use Illuminate\Http\Request;

class MitraKerjaController extends Controller
{
    function viewMitraKerjaMain() {
        return view('Mitra Kerja.main-mitra-kerja');
    }

    function viewTambahMitraKerja() {

        $bidangUsahaList = BidangUsaha::select('id as val', 'nama as label')->get();

        return view('Mitra Kerja.CRUD.tambah-mitra-kerja', compact('bidangUsahaList'));
    }

    function viewDetailMitraKerja(Request $request) {
        return view('Mitra Kerja.detail-mitra-kerja');
    }

    function tambahMitraKerja(Request $request) {

    }

    function ubahMitraKerja(Request $request) {

    }

    function updateMitraKerja(Request $request) {

    }
}
