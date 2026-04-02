<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Assessment;
use App\Traits\ApiTrait;
use App\Traits\Images;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    use ApiTrait, Images;

    /**
     * List user's assessments with filters and pagination.
     *
     * Filters: start_date, end_date, status, risk_level, search
     * Pagination: per_page (default 10), page
     * Sorting: sort_by (created_at|risk_percentage), sort_dir (asc|desc)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Assessment::where('user_id', Auth::id());

            // Date range filter
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Risk level filter
            if ($request->filled('risk_level')) {
                $query = match ($request->risk_level) {
                    'low'    => $query->where('risk_percentage', '<', 30),
                    'medium' => $query->whereBetween('risk_percentage', [30, 70]),
                    'high'   => $query->where('risk_percentage', '>', 70),
                    default  => $query,
                };
            }

            // Full-text search in symptoms
            if ($request->filled('search')) {
                $query->where('symptoms_text', 'like', '%' . $request->search . '%');
            }

            // Sorting
            $sortBy  = in_array($request->sort_by, ['created_at', 'risk_percentage']) ? $request->sort_by : 'created_at';
            $sortDir = in_array($request->sort_dir, ['asc', 'desc']) ? $request->sort_dir : 'desc';
            $query->orderBy($sortBy, $sortDir);

            // Pagination
            $perPage     = min($request->integer('per_page', 10), 100);
            $assessments = $query->paginate($perPage);

            return $this->okResponse([
                'assessments' => AssessmentResource::collection($assessments),
                'pagination'  => [
                    'total'        => $assessments->total(),
                    'per_page'     => $assessments->perPage(),
                    'current_page' => $assessments->currentPage(),
                    'last_page'    => $assessments->lastPage(),
                    'from'         => $assessments->firstItem(),
                    'to'           => $assessments->lastItem(),
                ],
            ], 'Assessments retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to fetch assessments: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'Failed to retrieve assessments');
        }
    }

    /**
     * Store a new assessment.
     */
    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        try {
            $images = [];

            if ($request->hasFile('image_path')) {
                foreach ($request->file('image_path') as $image) {
                    $images[] = $image->store('assessment_images', 'public');
                }
            }

            $assessment = Assessment::create([
                'user_id'           => Auth::id(),
                'image_path'        => $images,
                'symptoms_text'     => $request->symptoms_text ?? null,
                'symptoms_selected' => $request->symptoms_selected ?? null,
                'status'            => 'pending',
            ]);

            return $this->createdResponse(
                new AssessmentResource($assessment),
                'Assessment submitted successfully. Processing in progress...'
            );

        } catch (\Exception $e) {
            Log::error('Failed to create assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'Failed to submit assessment');
        }
    }

    /**
     * Show a single assessment.
     */
    public function show(Assessment $assessment): JsonResponse
    {
        try {
            if ($assessment->user_id !== Auth::id()) {
                return $this->forbiddenResponse([], 'You are not authorized to view this assessment');
            }

            return $this->okResponse(
                new AssessmentResource($assessment),
                'Assessment retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to fetch assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'Failed to retrieve assessment');
        }
    }

    /**
     * Delete an assessment.
     */
    public function destroy(Assessment $assessment): JsonResponse
    {
        try {
            if ($assessment->user_id !== Auth::id()) {
                return $this->forbiddenResponse([], 'You are not authorized to delete this assessment');
            }

            $assessment->delete();

            return $this->okResponse([], 'Assessment deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete assessment: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'Failed to delete assessment');
        }
    }

    /**
     * Get statistics summary for user's assessments.
     */
    public function statistics(): JsonResponse
    {
        try {
            $assessments = Assessment::where('user_id', Auth::id())->get();

            $latest = $assessments->sortByDesc('created_at')->first();

            return $this->okResponse([
                'total'              => $assessments->count(),
                'by_status'          => $assessments->groupBy('status')->map->count(),
                'by_recommendation'  => $assessments->groupBy('recommendation')->map->count(),
                'average_risk'       => round($assessments->avg('risk_percentage'), 2),
                'latest'             => $latest ? new AssessmentResource($latest) : null,
            ], 'Statistics retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to fetch statistics: ' . $e->getMessage());
            return $this->errorResponse([], 500, 'Failed to retrieve statistics');
        }
    }
}
