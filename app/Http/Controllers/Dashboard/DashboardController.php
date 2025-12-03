<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_assessments' => Assessment::count(),
            'pending_assessments' => Assessment::where('status', 'قيد_المراجعة')->count(),
            'total_reports' => Report::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'critical_cases' => Assessment::where('recommendation', 'رعاية_طارئة')->count(),
        ];

        $recentAssessments = Assessment::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        // Chart Data: Assessments per day for last 30 days
        $dailyDiagnoses = Assessment::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('count', 'date');

        return view('dashboard.index', compact('stats', 'recentAssessments', 'recentUsers', 'dailyDiagnoses'));
    }


    public function chat()
    {
        // Get latest message ID for each sender-receiver pair
        $latestMessageIds = Message::selectRaw('MAX(id) as id')
            ->groupBy('sender_id', 'receiver_id')
            ->pluck('id');

        $conversations = Message::whereIn('id', $latestMessageIds)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                return min($message->sender_id, $message->receiver_id).'-'.max($message->sender_id, $message->receiver_id);
            })
            ->map(function ($group) {
                return $group->first();
            });

        return view('dashboard.chat', compact('conversations'));
    }

    public function chatConversation($userId)
    {
        $user = User::findOrFail($userId);

        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', auth()->id());
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dashboard.chat-conversation', compact('user', 'messages'));
    }

    public function sendMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $userId,
            'message' => $request->message,
        ]);

        // Dispatch event later
        // broadcast(new MessageSent($message))->toOthers();

        return redirect()->route('dashboard.chat.conversation', $userId);
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:assessment,health_summary,monthly',
            'user_id' => 'required|exists:users,id',
            // Add other fields as necessary for report generation logic
        ]);

        // Simplified logic for demo - in real app use the generation logic from Api\ReportController
        Report::create([
            'user_id' => $validated['user_id'],
            'report_type' => $validated['report_type'],
            'report_data' => json_encode(['note' => 'Generated from dashboard']), // Placeholder
            'generated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Report created successfully');
    }

    public function updateReport(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $validated = $request->validate([
            'report_type' => 'required|in:assessment,health_summary,monthly',
        ]);

        $report->update([
            'report_type' => $validated['report_type'],
        ]);

        return redirect()->back()->with('success', 'Report updated successfully');
    }

    public function deleteReport($id)
    {
        Report::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Report deleted successfully');
    }

   
}
