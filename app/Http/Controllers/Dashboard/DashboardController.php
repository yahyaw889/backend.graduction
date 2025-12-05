<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
            'total_reports' =>  0,
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


    

    

   
}
