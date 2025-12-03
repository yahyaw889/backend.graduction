<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recurrenceRules()
    {
        return $this->hasMany(RecurrenceRule::class);
    }

    public function exceptions()
    {
        return $this->hasMany(ReminderException::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
