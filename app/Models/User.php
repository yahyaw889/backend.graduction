<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// use Laravel\Sanctum\HasApiTokens; // Removed as per instruction

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens; // Removed HasApiTokens

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type', // Added 'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function contactUs()
    {
        return $this->hasMany(ContactUs::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function scopeDoctors($query)
    {
        return $query->where('type', 'doctor');
    }

    public function scopePatients($query)
    {
        return $query->where('type', 'patient');
    }

    public function scopeAdmins($query)
    {
        return $query->where('type', 'admin');
    }
}
