<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHistory extends Model
{
    protected $table = 'payroll_histories';
    protected $fillable = [
        'id_unit',
        'period_start',
        'period_end',
        'total_payroll',
    ];

    public function details()
    {
        return $this->hasMany(PayrollHistory_Detail::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }
}
