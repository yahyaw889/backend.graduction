<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    
    public function index()
    {
        $reports = Report::with(['user', 'assessment'])
            ->latest('generated_at')
            ->paginate(20);

        $reportStats = [
            'total' => Report::count(),
            'by_type' => Report::selectRaw('report_type, count(*) as count')
                ->groupBy('report_type')
                ->pluck('count', 'report_type'),
        ];

        return view('dashboard.reports', compact('reports', 'reportStats'));
    }
}
