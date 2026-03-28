<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password'=> Hash::make('123456789'),
            'type' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password'=> Hash::make('123456789'),
            'type' => 'patient',
        ]);

        User::factory()->create([
            'name' => 'doctor',
            'email' => 'doctor@gmail.com',
            'password'=> Hash::make('123456789'),
            'type' => 'doctor',
        ]);
    }
}
