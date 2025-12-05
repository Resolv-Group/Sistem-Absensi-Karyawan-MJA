<?php

use Carbon\Carbon;

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal)
    {
        return Carbon::parse($tanggal)->translatedFormat('d F Y');
    }
}
