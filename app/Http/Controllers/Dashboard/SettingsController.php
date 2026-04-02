<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('dashboard.settings');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Password update
        if ($request->input('type') === 'password') {
            $validated = $request->validate([
                'current_password' => 'required|current_password',
                'new_password'     => 'required|string|min:8|confirmed',
            ]);

            $user->update(['password' => bcrypt($validated['new_password'])]);

            return back()->with('success', 'Password updated successfully.');
        }

        // Profile update
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}
