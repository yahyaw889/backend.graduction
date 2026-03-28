<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'risk_percentage',
        'recommendation',
        'report_text',
        'symptoms_text',
        'symptoms_selected',
        'status',
        'reason',
        'model_type',
    ];

    protected $casts = [
        'image_path' => 'array',
        'symptoms_selected' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function symptoms()
    {
        if (!$this->symptoms_selected) {
            return collect([]);
        }
        return Symptoms::whereIn('id', $this->symptoms_selected)->get();
    }
    

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
