<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List users with search, type filter, and pagination.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = min($request->integer('per_page', 10), 100);
        $users   = $query->latest()->paginate($perPage);

        return view('dashboard.users', compact('users'));
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'type'     => 'nullable|in:patient,doctor,admin',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'type'     => $validated['type'] ?? 'patient',
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'type'     => 'nullable|in:patient,doctor,admin',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'type'  => $validated['type'] ?? $user->type,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function delete(int $id)
    {
        User::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
