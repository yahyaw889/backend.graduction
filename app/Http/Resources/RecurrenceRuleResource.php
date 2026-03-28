<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecurrenceRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reminder_id' => $this->reminder_id,
            'frequency' => $this->frequency,
            'interval' => $this->interval,
            'days_of_week' => $this->days_of_week,
            'days_of_month' => $this->days_of_month,
            'months_of_year' => $this->months_of_year,
            'time' => $this->time,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
