<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Services\AssessmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAssessmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Assessment $assessment;

    /**
     * Create a new job instance.
     */
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Execute the job.
     */
    public function handle(AssessmentService $assessmentService): void
    {
        try {
            Log::info('Processing assessment: ' . $this->assessment->id);
            
            $assessmentService->processAssessment($this->assessment);
            
            Log::info('Assessment processed successfully: ' . $this->assessment->id);
        } catch (\Exception $e) {
            Log::error('Failed to process assessment: ' . $this->assessment->id . ' - ' . $e->getMessage());
            
            // Update assessment to manual review on failure
            $this->assessment->update([
                'status' => 'pending',
                'report_text' => 'حدث خطأ أثناء المعالجة. طلبك قيد المراجعة من قبل فريق الدعم الطبي.',
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Assessment job failed: ' . $this->assessment->id . ' - ' . $exception->getMessage());
        
        $this->assessment->update([
            'status' => 'pending',
            'report_text' => 'حدث خطأ أثناء المعالجة. طلبك قيد المراجعة من قبل فريق الدعم الطبي.',
        ]);
    }
}
