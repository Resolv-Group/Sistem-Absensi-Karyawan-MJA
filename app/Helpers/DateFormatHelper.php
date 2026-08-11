<?php

use Carbon\Carbon;

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal)
    {
        if (empty($tanggal)) {
            return '-';
        }
        return Carbon::parse($tanggal)->translatedFormat('d F Y');
    }
}
