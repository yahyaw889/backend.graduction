<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssessmentResource;
use App\Http\Resources\MedicalAdviceResource;
use App\Models\Assessment;
use App\Models\MedicalAdvice;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use ApiTrait;

    public function index()
    {
        $query = Assessment::query()->where('user_id', Auth::id());
        
        $assessment = $query->latest()->first();

        $medical_advice = MedicalAdvice::active()->get();
        $total_assessments = $query->count();

        return $this->okResponse([
            'latest_assessment' => $assessment ? new AssessmentResource($assessment) : null,
            'medical_advices' => MedicalAdviceResource::collection($medical_advice),
            'total_assessments' => $total_assessments,
        ], 'Home data retrieved successfully');
    }
}
