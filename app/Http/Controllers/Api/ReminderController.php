<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
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

    public function __construct(protected ReminderService $reminderService)
    {
    }

    /**
     * List all reminders for the authenticated user.
     * Supports filters: active_only, frequency
     */
    public function index(Request $request): JsonResponse
    {
        $userId     = $request->user()->id;
        $activeOnly = $request->boolean('active_only', false);

        $reminders = $this->reminderService->getUserReminders($userId, $activeOnly);

        // Additional filter by frequency
        if ($request->filled('frequency')) {
            $freq = $request->frequency;
            $reminders = $reminders->filter(function ($reminder) use ($freq) {
                return $reminder->recurrenceRules->contains('frequency', $freq);
            })->values();
        }

        return $this->okResponse(
            ReminderResource::collection($reminders),
            'Reminders retrieved successfully'
        );
    }

    /**
     * Create a new reminder.
     */
    public function store(StoreReminderRequest $request): JsonResponse
    {
        $data            = $request->validated();
        $data['user_id'] = $request->user()->id;

        $reminder = $this->reminderService->createReminder($data);

        return $this->createdResponse(
            new ReminderResource($reminder),
            'Reminder created successfully'
        );
    }

    /**
     * Show a specific reminder.
     */
    public function show(Request $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $reminder->load(['recurrenceRules', 'exceptions']);

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder retrieved successfully'
        );
    }

    /**
     * Update an existing reminder.
     */
    public function update(UpdateReminderRequest $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $reminder = $this->reminderService->updateReminder($reminder, $request->validated());

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder updated successfully'
        );
    }

    /**
     * Delete a reminder.
     */
    public function destroy(Request $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $this->reminderService->deleteReminder($reminder);

        return $this->okResponse([], 'Reminder deleted successfully');
    }

    /**
     * Toggle the active status of a reminder.
     */
    public function toggle(Request $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $reminder = $this->reminderService->toggleActive($reminder);

        return $this->okResponse(
            new ReminderResource($reminder),
            'Reminder status toggled successfully'
        );
    }

    /**
     * Get upcoming reminders.
     * Supports: start_date, end_date, limit
     */
    public function upcoming(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'limit'      => 'integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors()->toArray(), 'Validation failed');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate   = $request->filled('end_date')   ? Carbon::parse($request->end_date)   : null;
        $limit     = $request->integer('limit', 50);

        $upcoming = $this->reminderService->getUpcomingReminders(
            $request->user()->id,
            $startDate,
            $endDate,
            $limit
        );

        return $this->okResponse($upcoming, 'Upcoming reminders retrieved successfully');
    }

    /**
     * Get next N occurrences for a reminder.
     */
    public function nextOccurrences(Request $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $count = min($request->integer('count', 10), 50);

        $reminder->load(['recurrenceRules', 'exceptions']);
        $occurrences = $this->reminderService->getNextOccurrences($reminder, $count);

        return $this->okResponse($occurrences, 'Next occurrences retrieved successfully');
    }

    /**
     * Add an exception to a reminder.
     */
    public function addException(Request $request, Reminder $reminder): JsonResponse
    {
        if ($reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this reminder');
        }

        $validator = Validator::make($request->all(), [
            'date'     => 'required|date',
            'action'   => 'required|in:skip,modify',
            'new_time' => 'nullable|date_format:H:i|required_if:action,modify',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors()->toArray(), 'Validation failed');
        }

        $exception = $this->reminderService->createException($reminder, $validator->validated());

        return $this->createdResponse($exception, 'Exception added successfully');
    }

    /**
     * Delete a reminder exception.
     */
    public function deleteException(Request $request, ReminderException $exception): JsonResponse
    {
        if ($exception->reminder->user_id !== $request->user()->id) {
            return $this->forbiddenResponse([], 'You do not have access to this exception');
        }

        $this->reminderService->deleteException($exception);

        return $this->okResponse([], 'Exception deleted successfully');
    }
}
