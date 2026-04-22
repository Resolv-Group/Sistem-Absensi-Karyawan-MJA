<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHistory_Detail extends Model
{
    protected $table = 'payroll_history_details';

    protected $fillable = [
        'payroll_history_id',
        'id_pekerja',
        'nama',
        'email',
        'divisi',
        'jabatan',
        'upah_pokok',
        'lembur',
        'lembur_hbn',
        'insentif',
        'tunjangan',
        'potongan',
        'take_home_pay',
        'pdf_path',
        'email_sent_at',
        'email_status',
    ];

    public function history()
    {
        return $this->belongsTo(PayrollHistory::class, 'payroll_history_id');
    }
}
