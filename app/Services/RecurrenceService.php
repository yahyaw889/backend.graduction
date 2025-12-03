<?php

namespace App\Services;

use App\Models\RecurrenceRule;
use App\Models\ReminderException;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RecurrenceService
{
    /**
     * Calculate next N occurrences for a recurrence rule
     */
    public function calculateNextOccurrences(RecurrenceRule $rule, int $count = 30, ?Carbon $from = null): Collection
    {
        $from = $from ?? Carbon::now();
        $occurrences = collect();
        $current = Carbon::parse($rule->start_date);

        // Make sure we start from the 'from' date if it's after start_date
        if ($from->gt($current)) {
            $current = $from->copy();
        }

        $endDate = $rule->end_date ? Carbon::parse($rule->end_date) : null;
        $maxIterations = $count * 100; // Prevent infinite loops
        $iterations = 0;

        while ($occurrences->count() < $count && $iterations < $maxIterations) {
            $iterations++;

            // Check if we've passed the end date
            if ($endDate && $current->gt($endDate)) {
                break;
            }

            // Check if current date matches the recurrence pattern
            if ($this->isOccurrenceDate($rule, $current)) {
                $occurrenceDateTime = $current->copy();
                
                // Add time if specified
                if ($rule->time) {
                    $time = Carbon::parse($rule->time);
                    $occurrenceDateTime->setTime($time->hour, $time->minute);
                }

                $occurrences->push($occurrenceDateTime->copy());
            }

            // Move to next potential occurrence
            $current = $this->getNextPotentialDate($rule, $current);
        }

        return $occurrences;
    }

    /**
     * Get all occurrences in a date range
     */
    public function getOccurrencesInRange(RecurrenceRule $rule, Carbon $start, Carbon $end): Collection
    {
        $occurrences = collect();
        $current = Carbon::parse($rule->start_date);

        // Start from the range start if it's after rule start
        if ($start->gt($current)) {
            $current = $start->copy();
        }

        $ruleEndDate = $rule->end_date ? Carbon::parse($rule->end_date) : null;
        $maxIterations = 10000; // Safety limit
        $iterations = 0;

        while ($current->lte($end) && $iterations < $maxIterations) {
            $iterations++;

            // Check if we've passed the rule's end date
            if ($ruleEndDate && $current->gt($ruleEndDate)) {
                break;
            }

            if ($this->isOccurrenceDate($rule, $current)) {
                $occurrenceDateTime = $current->copy();
                
                if ($rule->time) {
                    $time = Carbon::parse($rule->time);
                    $occurrenceDateTime->setTime($time->hour, $time->minute);
                }

                $occurrences->push($occurrenceDateTime->copy());
            }

            $current = $this->getNextPotentialDate($rule, $current);
        }

        return $occurrences;
    }

    /**
     * Apply exceptions to occurrences
     */
    public function applyExceptions(Collection $occurrences, Collection $exceptions): Collection
    {
        return $occurrences->map(function ($occurrence) use ($exceptions) {
            $dateString = $occurrence->format('Y-m-d');
            
            $exception = $exceptions->first(function ($exc) use ($dateString) {
                return Carbon::parse($exc->date)->format('Y-m-d') === $dateString;
            });

            if (!$exception) {
                return $occurrence;
            }

            // Skip this occurrence
            if ($exception->action === 'skip') {
                return null;
            }

            // Modify the time
            if ($exception->action === 'modify' && $exception->new_time) {
                $newTime = Carbon::parse($exception->new_time);
                $occurrence->setTime($newTime->hour, $newTime->minute);
            }

            return $occurrence;
        })->filter(); // Remove null values (skipped occurrences)
    }

    /**
     * Check if a date matches the recurrence pattern
     */
    public function isOccurrenceDate(RecurrenceRule $rule, Carbon $date): bool
    {
        // Check if date is before start date
        if ($date->lt(Carbon::parse($rule->start_date))) {
            return false;
        }

        // Check if date is after end date
        if ($rule->end_date && $date->gt(Carbon::parse($rule->end_date))) {
            return false;
        }

        switch ($rule->frequency) {
            case 'daily':
                return $this->matchesDailyPattern($rule, $date);
            
            case 'weekly':
                return $this->matchesWeeklyPattern($rule, $date);
            
            case 'monthly':
                return $this->matchesMonthlyPattern($rule, $date);
            
            case 'yearly':
                return $this->matchesYearlyPattern($rule, $date);
            
            case 'custom':
                return $this->matchesCustomPattern($rule, $date);
            
            default:
                return false;
        }
    }

    /**
     * Check if date matches daily pattern
     */
    protected function matchesDailyPattern(RecurrenceRule $rule, Carbon $date): bool
    {
        $startDate = Carbon::parse($rule->start_date);
        $daysDiff = $startDate->diffInDays($date);
        
        return $daysDiff % $rule->interval === 0;
    }

    /**
     * Check if date matches weekly pattern
     */
    protected function matchesWeeklyPattern(RecurrenceRule $rule, Carbon $date): bool
    {
        $startDate = Carbon::parse($rule->start_date);
        $weeksDiff = $startDate->diffInWeeks($date);
        
        // Check if it's the right week interval
        if ($weeksDiff % $rule->interval !== 0) {
            return false;
        }

        // Check if it's the right day of week
        if ($rule->days_of_week) {
            $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
            return in_array($dayOfWeek, $rule->days_of_week);
        }

        return true;
    }

    /**
     * Check if date matches monthly pattern
     */
    protected function matchesMonthlyPattern(RecurrenceRule $rule, Carbon $date): bool
    {
        $startDate = Carbon::parse($rule->start_date);
        $monthsDiff = $startDate->diffInMonths($date);
        
        // Check if it's the right month interval
        if ($monthsDiff % $rule->interval !== 0) {
            return false;
        }

        // Check if it's the right day of month
        if ($rule->days_of_month) {
            return in_array($date->day, $rule->days_of_month);
        }

        // Default to same day as start date
        return $date->day === $startDate->day;
    }

    /**
     * Check if date matches yearly pattern
     */
    protected function matchesYearlyPattern(RecurrenceRule $rule, Carbon $date): bool
    {
        $startDate = Carbon::parse($rule->start_date);
        $yearsDiff = $startDate->diffInYears($date);
        
        // Check if it's the right year interval
        if ($yearsDiff % $rule->interval !== 0) {
            return false;
        }

        // Check if it's the right month
        if ($rule->months_of_year) {
            if (!in_array($date->month, $rule->months_of_year)) {
                return false;
            }
        } else {
            // Default to same month as start date
            if ($date->month !== $startDate->month) {
                return false;
            }
        }

        // Check day of month
        if ($rule->days_of_month) {
            return in_array($date->day, $rule->days_of_month);
        }

        return $date->day === $startDate->day;
    }

    /**
     * Check if date matches custom pattern
     */
    protected function matchesCustomPattern(RecurrenceRule $rule, Carbon $date): bool
    {
        // Custom pattern can combine multiple rules
        // For now, treat it like daily with all filters
        $startDate = Carbon::parse($rule->start_date);
        $daysDiff = $startDate->diffInDays($date);
        
        if ($daysDiff % $rule->interval !== 0) {
            return false;
        }

        // Apply all filters if they exist
        if ($rule->days_of_week && !in_array($date->dayOfWeek, $rule->days_of_week)) {
            return false;
        }

        if ($rule->days_of_month && !in_array($date->day, $rule->days_of_month)) {
            return false;
        }

        if ($rule->months_of_year && !in_array($date->month, $rule->months_of_year)) {
            return false;
        }

        return true;
    }

    /**
     * Get next potential date based on frequency
     */
    protected function getNextPotentialDate(RecurrenceRule $rule, Carbon $current): Carbon
    {
        switch ($rule->frequency) {
            case 'daily':
            case 'custom':
                return $current->copy()->addDay();
            
            case 'weekly':
                return $current->copy()->addDay();
            
            case 'monthly':
                return $current->copy()->addDay();
            
            case 'yearly':
                return $current->copy()->addDay();
            
            default:
                return $current->copy()->addDay();
        }
    }
}
