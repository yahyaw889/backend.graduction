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
        // 1. Get OpenAI API Key from environment variables
        $apiKey = config('services.openai.key');
        
        if (!$apiKey) {
            throw new \Exception('OpenAI API key is not configured in .env file (OPENAI_API_KEY).');
        }

        // 2. Prepare Image
        $imageContent = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContent);
        $mimeType = mime_content_type($imagePath);
        $dataUri = "data:{$mimeType};base64,{$base64Image}";

        // 3. Prepare Prompt
        $prompt = "You are an expert dermatologist AI assistant. The user has provided an image of their skin and the following clinical data:\n";
        $prompt .= json_encode($patientData, JSON_UNESCAPED_UNICODE) . "\n\n";
        $prompt .= "Please analyze the image and the provided data to detect if there are signs of Monkeypox (Mpox), Smallpox, Chickenpox, or other skin conditions.\n";
        $prompt .= "Return the result EXACTLY in valid JSON format with the following keys:\n";
        $prompt .= "- 'diagnosis': string (the most likely condition)\n";
        $prompt .= "- 'confidence_percentage': integer (0-100)\n";
        $prompt .= "- 'symptoms_detected': array of strings (what you observe in the image or match from data)\n";
        $prompt .= "- 'recommendation': string (what the patient should do next)\n";
        $prompt .= "- 'disclaimer': string (always state that this is an AI analysis and not a final medical diagnosis)\n";
        $prompt .= "Do not include any Markdown formatting like ```json in the output, just the raw JSON object.";

        // 4. Send Request to OpenAI Vision API
        $response = Http::withToken($apiKey)->withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $dataUri
                            ]
                        ]
                    ]
                ]
            ],
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 1000
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $textOutput = $result['choices'][0]['message']['content'] ?? '{}';
            
            return json_decode(trim($textOutput), true);
        }

        Log::error('AI Diagnosis API Error', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);
        
        return ['error' => 'Failed to connect to AI service. Please check your API Key and internet connection.'];
    }
}
