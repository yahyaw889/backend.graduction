<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptoms extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'category',
    ];

    public function scopeVaricella($query)
    {
        return $query->where('category', 'varicella');
    }

    public function scopeOther($query)
    {
        return $query->where('category', 'other');
    }
}
