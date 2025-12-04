<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function viewDashboardMain() {
        return view('dashboard');
    }
}
