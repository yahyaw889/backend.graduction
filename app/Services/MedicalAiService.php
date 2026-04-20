<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalAiService
{
    /**
     * Diagnose a skin condition (e.g., Monkeypox/Smallpox) using an image and patient data via Gemini API.
     */
    public function diagnoseSkinDisease($imagePath, array $patientData)
    {
        // 1. Get Gemini API Key from environment variables
        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key is not configured in .env file (GEMINI_API_KEY).');
        }

        if (!config('services.gemini.enabled')) {
            return ['error' => 'خدمة الذكاء الاصطناعي معطلة حالياً من قبل الإدارة.'];
        }

        // 2. Prepare Image
        $imageContent = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContent);
        $mimeType = mime_content_type($imagePath);

        // 3. Prepare Prompt
        $prompt = "أنت طبيب ذكاء اصطناعي خبير في الأمراض الجلدية. قام المستخدم بتقديم صورة لجلده والبيانات السريرية التالية:\n";
        $prompt .= json_encode($patientData, JSON_UNESCAPED_UNICODE) . "\n\n";
        $prompt .= "يرجى تحليل الصورة والبيانات المقدمة لاكتشاف ما إذا كانت هناك علامات لمرض جدري القردة (Mpox) أو الجدري أو جدري الماء أو أي أمراض جلدية أخرى.\n";
        $prompt .= "يجب أن تكون جميع الإجابات (القيم) باللغة العربية الفصحى.\n";
        $prompt .= "قم بإرجاع النتيجة بتنسيق JSON صحيح تمامًا مع المفاتيح الإنجليزية التالية:\n";
        $prompt .= "- 'diagnosis': نص (اسم المرض الأكثر احتمالاً باللغة العربية)\n";
        $prompt .= "- 'confidence_percentage': رقم صحيح (0-100)\n";
        $prompt .= "- 'symptoms_detected': مصفوفة نصوص (الأعراض التي لاحظتها في الصورة أو المذكورة، باللغة العربية)\n";
        $prompt .= "- 'recommendation': نص (ما يجب على المريض فعله كخطوة تالية، باللغة العربية)\n";
        $prompt .= "- 'disclaimer': نص (إخلاء مسؤولية يوضح أن هذا مجرد تحليل ذكاء اصطناعي وليس تشخيصاً طبياً نهائياً، باللغة العربية)\n";
        $prompt .= "Do not include any Markdown formatting like ```json in the output, just the raw JSON object.";

        // 4. Send Request to Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $textOutput = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            $usage = $result['usageMetadata'] ?? [];
            \Illuminate\Support\Facades\DB::table('ai_usages')->insert([
                'service_name' => 'medical_diagnosis',
                'prompt_tokens' => $usage['promptTokenCount'] ?? 0,
                'completion_tokens' => $usage['candidatesTokenCount'] ?? 0,
                'total_tokens' => $usage['totalTokenCount'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return json_decode(trim($textOutput), true);
        }

        Log::error('AI Diagnosis API Error', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);
        
        return ['error' => 'Failed to connect to AI service. Please check your API Key and internet connection.'];
    }
}
