<?php 


namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', 'YOUR_AI_API_KEY');
    }

    public function askAI($userMessage, $senderId, $receiverId)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $userMessage]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $aiReply = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'لا أفهم';

            $aiMessage = Message::create([
                'sender_id' => $receiverId,
                'receiver_id' => $senderId,
                'message' => $aiReply,
                'is_ai' => true,
            ]);

            broadcast(new MessageSent($aiMessage))->toOthers();
        }
    }

    /**
     * Analyze assessment data using Gemini (formerly OpenAI)
     */
    public function analyze(string $prompt): ?array
    {
        try {
            $systemPrompt = 'أنت مساعد طبي ذكي. قم بتحليل الأعراض وتقديم تقييم للخطورة والتوصيات.';
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\n\n" . $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($content) {
                    // Parse the AI response (you may need to adjust this based on actual response format)
                    return $this->parseAIResponse($content);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gemini analyze failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse AI response to extract structured data
     */
    protected function parseAIResponse(string $content): array
    {
        // This is a simple parser - you may need to adjust based on actual AI response format
        return [
            'risk_percentage' => 50, // Extract from response
            'recommendation' => 'take_precautions_and_see_doctor',
            'report_text' => $content,
        ];
    }
}