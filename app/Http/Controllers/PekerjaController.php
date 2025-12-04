<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PekerjaController extends Controller
{
    function viewPekerjaMain(){

        return view('Pekerja.main-pekerja');
    }

    function viewTambahPekerja() {
        return view('Pekerja.CRUD.tambah-pekerja');
    }
    function viewDetailPekerja() {
        return view('Pekerja.detail-pekerja');
    }


}
