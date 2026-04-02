<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get all dashboard KPI statistics.
     */
    public function getStats(): array
    {
        return [
            'total_users'          => User::count(),
            'total_assessments'    => Assessment::count(),
            'pending_assessments'  => Assessment::where('status', 'pending')->count(),
            'total_reports'        => 0,
            'unread_messages'      => Message::where('is_read', false)->count(),
            'critical_cases'       => Assessment::where('recommendation', 'see_doctor')->count(),
            'total_doctors'        => User::where('type', 'doctor')->count(),
            'total_patients'       => User::where('type', 'patient')->count(),
        ];
    }

    /**
     * Get recent assessments with user relationship.
     */
    public function getRecentAssessments(int $limit = 5)
    {
        return Assessment::with('user')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get recently registered users.
     */
    public function getRecentUsers(int $limit = 5)
    {
        return User::latest()->take($limit)->get();
    }

    /**
     * Get daily assessment counts for the last N days.
     */
    public function getDailyAssessments(int $days = 30): \Illuminate\Support\Collection
    {
        return Assessment::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
    }
}
