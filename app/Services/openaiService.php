<?php 


namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Message;
use App\Events\MessageSent;
class openaiService
{
    

     public function askAI($userMessage, $senderId, $receiverId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer YOUR_AI_API_KEY',
            'Content-Type' => 'application/json',
        ])->post('https://api.x.ai/v1/chat/completions', [ // أو OpenAI
            'model' => 'grok-beta',
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => 0.7
        ]);

        if ($response->successful()) {
            $aiReply = $response->json()['choices'][0]['message']['content'] ?? 'لا أفهم';

            $aiMessage = Message::create([
                'sender_id' => $receiverId, // نعتبر AI هو المستقبل
                'receiver_id' => $senderId,
                'message' => $aiReply,
                'is_ai' => true,
            ]);

            broadcast(new MessageSent($aiMessage))->toOthers();
        }
    }
}