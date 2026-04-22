<?php

namespace App\Jobs;

use App\Models\PayrollHistory_Detail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GeneratePayrollPdfJob implements ShouldQueue
{
    use Queueable;

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
        // Only process the details for this specific generated history
        $details = PayrollHistory_Detail::with('history.unit')->where('payroll_history_id', $this->historyId)->get();

        foreach ($details as $d) {
            $pdf = Pdf::loadView('payroll.pdf.rincian-upah', [
                'data' => $d
            ]);

            $path = "payroll/{$d->id}.pdf";

            Storage::put($path, $pdf->output());

            $d->update([
                'pdf_path' => $path
            ]);
        }
    }
}
