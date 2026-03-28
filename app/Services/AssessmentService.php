<?php

namespace App\Services;

use App\Models\Assessment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssessmentService
{
    protected $imageModelUrl;
    protected $textModelUrl;
    protected $bothModelUrl;
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
        // Configure your AI model URLs from config or env
        $this->imageModelUrl = env('IMAGE_MODEL_URL', null);
        $this->textModelUrl = env('TEXT_MODEL_URL', null);
        $this->bothModelUrl = env('BOTH_MODEL_URL', null);
    }

    /**
     * Process assessment using AI models
     */
    public function processAssessment(Assessment $assessment): Assessment
    {
        try {
            $result = null;

            // Determine which model to use based on model_type
            // make code her

            // If primary model failed, try OpenAI
            if (!$result) {
                $result = $this->fallbackToOpenAI($assessment);
            }

            // If OpenAI also failed, fallback to manual review
            if (!$result) {
                return $this->fallbackToManualReview($assessment);
            }

            // Update assessment with AI results
            $assessment->update([
                'risk_percentage' => $result['risk_percentage'] ?? 0,
                'recommendation' => $result['recommendation'] ?? 'take_precautions',
                'report_text' => $result['report_text'] ?? 'تم تحليل البيانات بنجاح',
                'status' => 'completed',
            ]);

            return $assessment->fresh();

        } catch (\Exception $e) {
            Log::error('Assessment processing failed: ' . $e->getMessage());
            return $this->fallbackToManualReview($assessment);
        }
    }

    /**
     * Analyze image using image model
     */
    

    /**
     * Fallback to OpenAI if primary models fail
     */
    protected function fallbackToOpenAI(Assessment $assessment): ?array
    {
        try {
            // Prepare prompt for OpenAI
            $prompt = $this->buildOpenAIPrompt($assessment);
            
            $response = $this->openAIService->analyze($prompt);

            if ($response) {
                return [
                    'risk_percentage' => $response['risk_percentage'] ?? 50,
                    'recommendation' => $response['recommendation'] ?? 'take_precautions_and_see_doctor',
                    'report_text' => $response['report_text'] ?? 'تم التحليل باستخدام الذكاء الاصطناعي',
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI fallback failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build prompt for OpenAI
     */
    protected function buildOpenAIPrompt(Assessment $assessment): string
    {
        $prompt = "قم بتحليل الحالة الطبية التالية:\n\n";

        if ($assessment->symptoms_text) {
            $prompt .= "الأعراض: " . $assessment->symptoms_text . "\n";
        }

        if ($assessment->symptoms_selected) {
            $prompt .= "الأعراض المحددة: " . implode(', ', $assessment->symptoms_selected) . "\n";
        }

        $prompt .= "\nالرجاء تقديم:\n";
        $prompt .= "1. نسبة الخطورة (0-100)\n";
        $prompt .= "2. التوصية (take_precautions, take_precautions_and_see_doctor, see_doctor)\n";
        $prompt .= "3. تقرير مفصل بالعربية\n";

        return $prompt;
    }

    /**
     * Fallback to manual review if all AI models fail
     */
    protected function fallbackToManualReview(Assessment $assessment): Assessment
    {
        $assessment->update([
            'status' => 'pending',
            'report_text' => 'طلبك قيد المراجعة من قبل فريق الدعم الطبي. سيتم الرد عليك قريباً.',
            'risk_percentage' => 0,
            'recommendation' => null,
        ]);

        return $assessment->fresh();
    }
}
