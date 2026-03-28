<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use App\Models\Reminder;
use App\Models\ReminderException;
use App\Services\ReminderService;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReminderController extends Controller
{
    use ApiTrait;
    protected ReminderService $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    /**
     * Display a listing of the user's reminders.
     */
    public function index(Request $request): JsonResponse
    {
        $activeOnly = $request->boolean('active_only', false);
        $reminders = $this->reminderService->getUserReminders(
            $request->user()->id,
            $activeOnly
        );

        return $this->okResponse(
            ReminderResource::collection($reminders),
            'Reminders retrieved successfully'
        );
    }

    /**
     * Store a newly created reminder.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'recurrence_rules' => 'required|array|min:1',
            'recurrence_rules.*.frequency' => 'required|in:daily,weekly,monthly,yearly,custom',
            'recurrence_rules.*.interval' => 'integer|min:1',
            'recurrence_rules.*.days_of_week' => 'nullable|array',
            'recurrence_rules.*.days_of_week.*' => 'integer|min:0|max:6',
            'recurrence_rules.*.days_of_month' => 'nullable|array',
            'recurrence_rules.*.days_of_month.*' => 'integer|min:1|max:31',
            'recurrence_rules.*.months_of_year' => 'nullable|array',
            'recurrence_rules.*.months_of_year.*' => 'integer|min:1|max:12',
            'recurrence_rules.*.time' => 'nullable|date_format:H:i',
            'recurrence_rules.*.start_date' => 'required|date',
            'recurrence_rules.*.end_date' => 'nullable|date|after_or_equal:recurrence_rules.*.start_date',
            'exceptions' => 'nullable|array',
            'exceptions.*.date' => 'required|date',
            'exceptions.*.action' => 'required|in:skip,modify',
            'exceptions.*.new_time' => 'nullable|date_format:H:i|required_if:exceptions.*.action,modify',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        $reminder = $this->reminderService->createReminder($data);

        return $this->createdResponse(
            new ReminderResource($reminder),
            'Reminder created successfully'
        );
    }

    /**
     * Display the specified reminder.
     */
    public function show(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $reminder->load(['recurrenceRules', 'exceptions']);

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder retrieved successfully'
        );
    }

    /**
     * Update the specified reminder.
     */
    public function update(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'recurrence_rules' => 'array',
            'recurrence_rules.*.frequency' => 'required|in:daily,weekly,monthly,yearly,custom',
            'recurrence_rules.*.interval' => 'integer|min:1',
            'recurrence_rules.*.days_of_week' => 'nullable|array',
            'recurrence_rules.*.days_of_week.*' => 'integer|min:0|max:6',
            'recurrence_rules.*.days_of_month' => 'nullable|array',
            'recurrence_rules.*.days_of_month.*' => 'integer|min:1|max:31',
            'recurrence_rules.*.months_of_year' => 'nullable|array',
            'recurrence_rules.*.months_of_year.*' => 'integer|min:1|max:12',
            'recurrence_rules.*.time' => 'nullable|date_format:H:i',
            'recurrence_rules.*.start_date' => 'required|date',
            'recurrence_rules.*.end_date' => 'nullable|date|after_or_equal:recurrence_rules.*.start_date',
            'exceptions' => 'nullable|array',
            'exceptions.*.date' => 'required|date',
            'exceptions.*.action' => 'required|in:skip,modify',
            'exceptions.*.new_time' => 'nullable|date_format:H:i|required_if:exceptions.*.action,modify',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $reminder = $this->reminderService->updateReminder($reminder, $validator->validated());

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder updated successfully'
        );
    }

    /**
     * Remove the specified reminder.
     */
    public function destroy(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->reminderService->deleteReminder($reminder);

        return $this->okResponse([], 'Reminder deleted successfully');
    }

    /**
     * Toggle reminder active status.
     */
    public function toggle(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $reminder = $this->reminderService->toggleActive($reminder);

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder status toggled successfully'
        );
    }

    /**
     * Get upcoming reminders.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $limit = $request->limit ?? 50;

        $upcomingReminders = $this->reminderService->getUpcomingReminders(
            $request->user()->id,
            $startDate,
            $endDate,
            $limit
        );

        return $this->okResponse(
            $upcomingReminders,
            'Upcoming reminders retrieved successfully'
        );
    }

    /**
     * Get next occurrences for a reminder.
     */
    public function nextOccurrences(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $count = $request->integer('count', 10);
        $count = min($count, 50); // Max 50

        $reminder->load(['recurrenceRules', 'exceptions']);
        $occurrences = $this->reminderService->getNextOccurrences($reminder, $count);

        return $this->okResponse(
            $occurrences,
            'Next occurrences retrieved successfully'
        );
    }

    /**
     * Add an exception to a reminder.
     */
    public function addException(Request $request, Reminder $reminder): JsonResponse
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'action' => 'required|in:skip,modify',
            'new_time' => 'nullable|date_format:H:i|required_if:action,modify',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $exception = $this->reminderService->createException($reminder, $validator->validated());

        return $this->createdResponse(
            $exception,
            'Exception added successfully'
        );
    }

    /**
     * Remove an exception.
     */
    public function deleteException(Request $request, ReminderException $exception): JsonResponse
    {
        // Ensure user owns this reminder
        if ($exception->reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'Unauthorized');
        }

        $this->reminderService->deleteException($exception);

        return $this->okResponse([], 'Exception deleted successfully');
    }
}
