<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Message;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments for dashboard.
     */
    public function index(Request $request)
    {
        $query = Assessment::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->paginate(15);

        return view('dashboard.assessments.index', compact('assessments'));
    }

    /**
     * Display specific assessment.
     */
    public function show(Assessment $assessment)
    {
        $assessment->load('user', 'symptoms');
        return view('dashboard.assessments.review', compact('assessment'));
    }

    /**
     * Doctor reviews and adds recommendation.
     */
    public function review(Request $request, Assessment $assessment)
    {
        $request->validate([
            'recommendation' => 'required|string',
            'status' => 'required|in:pending,completed'
        ]);

        $assessment->update([
            'recommendation' => $request->recommendation,
            'status' => $request->status
        ]);

        // If completed, automatically send a message to the user
        if ($request->status === 'completed' && auth()->id()) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $assessment->user_id,
                'message' => "Your skin assessment #{$assessment->id} has been reviewed by a doctor. Recommendation: " . $request->recommendation,
                'is_read' => false
            ]);
        }

        return redirect()->route('assessments.index')->with('success', 'Assessment reviewed successfully and patient notified.');
    }
}
