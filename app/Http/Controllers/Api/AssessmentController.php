<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Jobs\ProcessAssessmentJob;
use App\Models\Assessment;
use App\Traits\ApiTrait;
use App\Traits\Images;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    use ApiTrait , Images;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Assessment::where('user_id', Auth::id());

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by risk level
            if ($request->has('risk_level')) {
                switch ($request->risk_level) {
                    case 'low':
                        $query->where('risk_percentage', '<', 30);
                        break;
                    case 'medium':
                        $query->whereBetween('risk_percentage', [30, 70]);
                        break;
                    case 'high':
                        $query->where('risk_percentage', '>', 70);
                        break;
                }
            }

            $assessments = $query->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->okResponse([
                'assessments' => AssessmentResource::collection($assessments),
                'pagination' => [
                    'total' => $assessments->total(),
                    'per_page' => $assessments->perPage(),
                    'current_page' => $assessments->currentPage(),
                    'last_page' => $assessments->lastPage(),
                ],
            ], 'تم جلب التقييمات بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to fetch assessments: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'حدث خطأ أثناء جلب التقييمات');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        try {
            // Create assessment with pending status


            if ($request->hasFile('image_path')) {
                foreach ($request->file('image_path') as $image) {
                    $path = $image->store('assessment_images', 'public'); 
                    $images[] = $path;
                }
            }
            $assessment = Assessment::create([
                'user_id' => Auth::id(),
                'image_path' => $images,
                'symptoms_text' => $request->symptoms_text ?? null,
                'symptoms_selected' => $request->symptoms_selected ?? null,
                'status' => 'pending',
            ]);

            // Dispatch job to process assessment in background
            // ProcessAssessmentJob::dispatch($assessment);

            return $this->createdResponse(
                new AssessmentResource($assessment),
                'تم استلام طلبك بنجاح. جاري المعالجة...'
            );

        } catch (\Exception $e) {
            Log::error('Failed to create assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'حدث خطأ أثناء إنشاء التقييم');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Assessment $assessment): JsonResponse
    {
        try {
            // Check authorization
            if ($assessment->user_id !== Auth::id()) {
                return $this->forbiddenResponse([], 'غير مصرح لك بالوصول إلى هذا التقييم');
            }

            return $this->okResponse(
                new AssessmentResource($assessment),
                'تم جلب التقييم بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'حدث خطأ أثناء جلب التقييم');
        }
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assessment $assessment): JsonResponse
    {
        try {
            // Check authorization
            if ($assessment->user_id !== Auth::id()) {
                return $this->forbiddenResponse([], 'غير مصرح لك بحذف هذا التقييم');
            }

            $assessment->delete();

            return $this->okResponse([], 'تم حذف التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to delete assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'حدث خطأ أثناء حذف التقييم');
        }
    }

    /**
     * Get statistics for user assessments.
     */
    public function statistics(): JsonResponse
    {
        try {
            $assessments = Assessment::where('user_id', Auth::id())->get();

            return $this->okResponse([
                'total' => $assessments->count(),
                'by_status' => $assessments->groupBy('status')->map->count(),
                'by_recommendation' => $assessments->groupBy('recommendation')->map->count(),
                'average_risk' => round($assessments->avg('risk_percentage'), 2),
                'latest' => $assessments->sortByDesc('created_at')->first() 
                    ? new AssessmentResource($assessments->sortByDesc('created_at')->first())
                    : null,
            ], 'تم جلب الإحصائيات بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to fetch statistics: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'حدث خطأ أثناء جلب الإحصائيات');
        }
    }
}
