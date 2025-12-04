<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    function viewStaffMain(){
        return view('Staff.main-staff');
    }

    function viewTambahStaff() {
        return view('Staff.CRUD.tambah-staff');
    }

    function viewDetailStaff() {
        return view('Staff.detail-staff');
    }
}
