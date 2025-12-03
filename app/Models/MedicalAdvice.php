<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalAdvice extends Model
{
    use HasFactory;

    protected $table = 'medical_advice';

    protected $fillable = [
        'title',
        'desc',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
