<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrenceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'frequency',
        'interval',
        'days_of_week',
        'days_of_month',
        'months_of_year',
        'time',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'days_of_month' => 'array',
        'months_of_year' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
}
