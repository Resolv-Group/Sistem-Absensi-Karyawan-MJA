<?php

namespace App\Jobs;

use App\Mail\SendSlipGaji;
use App\Models\PayrollHistory_Detail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPayrollEmailsJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $historyId;

    /**
     * Create a new job instance.
     */
    public function __construct($historyId)
    {
        $this->historyId = $historyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $details = PayrollHistory_Detail::where('payroll_history_id', $this->historyId)
                                        ->where('email_status', 'pending')
                                        ->get();

        foreach ($details as $d) {
            try {
                if($d->email) {
                    Mail::to($d->email)->send(new SendSlipGaji($d));
                    
                    $d->update([
                        'email_status' => 'sent',
                        'email_sent_at' => now()
                    ]);
                } else {
                    $d->update([
                        'email_status' => 'failed' // No email assigned
                    ]);
                }
            } catch (\Exception $e) {
                $d->update([
                    'email_status' => 'failed'
                ]);
            }
        }
    }
}
