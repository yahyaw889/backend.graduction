<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $stats             = $this->dashboardService->getStats();
        $recentAssessments = $this->dashboardService->getRecentAssessments(5);
        $recentUsers       = $this->dashboardService->getRecentUsers(5);
        $dailyDiagnoses    = $this->dashboardService->getDailyAssessments(30);

        return view('dashboard.index', compact(
            'stats',
            'recentAssessments',
            'recentUsers',
            'dailyDiagnoses'
        ));
    }
}
