<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\RecurrenceRule;
use App\Models\ReminderException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    protected RecurrenceService $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        $this->recurrenceService = $recurrenceService;
    }

    /**
     * Create a new reminder with recurrence rules
     */
    public function createReminder(array $data): Reminder
    {
        return DB::transaction(function () use ($data) {
            // Create the reminder
            $reminder = Reminder::create([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'active' => $data['active'] ?? true,
            ]);

            // Create recurrence rules if provided
            if (isset($data['recurrence_rules'])) {
                foreach ($data['recurrence_rules'] as $ruleData) {
                    $this->createRecurrenceRule($reminder, $ruleData);
                }
            }

            // Create exceptions if provided
            if (isset($data['exceptions'])) {
                foreach ($data['exceptions'] as $exceptionData) {
                    $this->createException($reminder, $exceptionData);
                }
            }

            return $reminder->load(['recurrenceRules', 'exceptions']);
        });
    }

    /**
     * Update a reminder
     */
    public function updateReminder(Reminder $reminder, array $data): Reminder
    {
        return DB::transaction(function () use ($reminder, $data) {
            // Update basic reminder data
            $reminder->update([
                'title' => $data['title'] ?? $reminder->title,
                'description' => $data['description'] ?? $reminder->description,
                'active' => $data['active'] ?? $reminder->active,
            ]);

            // Update recurrence rules if provided
            if (isset($data['recurrence_rules'])) {
                // Delete old rules
                $reminder->recurrenceRules()->delete();
                
                // Create new rules
                foreach ($data['recurrence_rules'] as $ruleData) {
                    $this->createRecurrenceRule($reminder, $ruleData);
                }
            }

            // Update exceptions if provided
            if (isset($data['exceptions'])) {
                // Delete old exceptions
                $reminder->exceptions()->delete();
                
                // Create new exceptions
                foreach ($data['exceptions'] as $exceptionData) {
                    $this->createException($reminder, $exceptionData);
                }
            }

            return $reminder->fresh(['recurrenceRules', 'exceptions']);
        });
    }

    /**
     * Delete a reminder
     */
    public function deleteReminder(Reminder $reminder): bool
    {
        return $reminder->delete();
    }

    /**
     * Get user's reminders
     */
    public function getUserReminders(int $userId, bool $activeOnly = false): Collection
    {
        $query = Reminder::where('user_id', $userId)
            ->with(['recurrenceRules', 'exceptions']);

        if ($activeOnly) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Get upcoming reminders for a user in a date range
     */
    public function getUpcomingReminders(int $userId, ?Carbon $start = null, ?Carbon $end = null, int $limit = 50): Collection
    {
        $start = $start ?? Carbon::now();
        $end = $end ?? Carbon::now()->addDays(30);

        $reminders = $this->getUserReminders($userId, true);
        $upcomingOccurrences = collect();

        foreach ($reminders as $reminder) {
            foreach ($reminder->recurrenceRules as $rule) {
                // Get occurrences in range
                $occurrences = $this->recurrenceService->getOccurrencesInRange($rule, $start, $end);
                
                // Apply exceptions
                $occurrences = $this->recurrenceService->applyExceptions(
                    $occurrences,
                    $reminder->exceptions
                );

                // Add reminder info to each occurrence
                foreach ($occurrences as $occurrence) {
                    $upcomingOccurrences->push([
                        'reminder_id' => $reminder->id,
                        'title' => $reminder->title,
                        'description' => $reminder->description,
                        'occurrence_date' => $occurrence,
                        'recurrence_rule_id' => $rule->id,
                    ]);
                }
            }
        }

        // Sort by occurrence date and limit
        return $upcomingOccurrences
            ->sortBy('occurrence_date')
            ->take($limit)
            ->values();
    }

    /**
     * Toggle reminder active status
     */
    public function toggleActive(Reminder $reminder): Reminder
    {
        $reminder->update(['active' => !$reminder->active]);
        return $reminder->fresh();
    }

    /**
     * Create a recurrence rule for a reminder
     */
    protected function createRecurrenceRule(Reminder $reminder, array $data): RecurrenceRule
    {
        return $reminder->recurrenceRules()->create([
            'frequency' => $data['frequency'],
            'interval' => $data['interval'] ?? 1,
            'days_of_week' => $data['days_of_week'] ?? null,
            'days_of_month' => $data['days_of_month'] ?? null,
            'months_of_year' => $data['months_of_year'] ?? null,
            'time' => $data['time'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
        ]);
    }

    /**
     * Create an exception for a reminder
     */
    public function createException(Reminder $reminder, array $data): ReminderException
    {
        return $reminder->exceptions()->create([
            'date' => $data['date'],
            'action' => $data['action'], // 'skip' or 'modify'
            'new_time' => $data['new_time'] ?? null,
        ]);
    }

    /**
     * Delete an exception
     */
    public function deleteException(ReminderException $exception): bool
    {
        return $exception->delete();
    }

    /**
     * Get next N occurrences for a reminder
     */
    public function getNextOccurrences(Reminder $reminder, int $count = 10): Collection
    {
        $allOccurrences = collect();

        foreach ($reminder->recurrenceRules as $rule) {
            $occurrences = $this->recurrenceService->calculateNextOccurrences($rule, $count);
            
            // Apply exceptions
            $occurrences = $this->recurrenceService->applyExceptions(
                $occurrences,
                $reminder->exceptions
            );

            foreach ($occurrences as $occurrence) {
                $allOccurrences->push([
                    'occurrence_date' => $occurrence,
                    'recurrence_rule_id' => $rule->id,
                ]);
            }
        }

        return $allOccurrences
            ->sortBy('occurrence_date')
            ->take($count)
            ->values();
    }
}
