<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderException extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'date',
        'action',
        'new_time',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
}
