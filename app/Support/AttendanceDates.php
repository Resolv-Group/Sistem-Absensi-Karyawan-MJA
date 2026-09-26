<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceDates
{
    public static function fromRequest(Request $request, ?string $fallback = null): array
    {
        $dates = $request->input('dates', [$request->input('date', $fallback ?? now()->toDateString())]);
        Validator::make(['dates' => $dates], [
            'dates' => 'required|array|min:1|max:7',
            'dates.*' => 'required|date_format:Y-m-d|distinct',
        ], [
            'dates.max' => 'Maksimal 7 tanggal dapat dipilih dalam satu kali absensi.',
            'dates.*.distinct' => 'Tanggal tidak boleh dipilih lebih dari sekali.',
        ])->validate();
        sort($dates);

        return array_values($dates);
    }
}
