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
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', 'YOUR_AI_API_KEY');
        $this->apiUrl = env('OPENAI_API_URL', 'https://api.x.ai/v1/chat/completions');
    }

    public function askAI($userMessage, $senderId, $receiverId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'grok-beta',
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => 0.7
        ]);

        if ($response->successful()) {
            $aiReply = $response->json()['choices'][0]['message']['content'] ?? 'لا أفهم';

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
     * Analyze assessment data using OpenAI
     */
    public function analyze(string $prompt): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => 'grok-beta',
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد طبي ذكي. قم بتحليل الأعراض وتقديم تقييم للخطورة والتوصيات.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                
                if ($content) {
                    // Parse the AI response (you may need to adjust this based on actual response format)
                    return $this->parseAIResponse($content);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI analyze failed: ' . $e->getMessage());
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