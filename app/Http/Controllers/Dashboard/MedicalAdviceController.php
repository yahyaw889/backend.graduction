<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MedicalAdvice;
use Illuminate\Http\Request;

class MedicalAdviceController extends Controller
{
    /**
     * List all medical advice entries with optional filters.
     */
    public function index(Request $request)
    {
        $query = MedicalAdvice::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('desc', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        $advices = $query->latest()->paginate(10);

        return view('dashboard.medical-advice', compact('advices'));
    }

    /**
     * Create a new advice entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'desc'   => 'required|string',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        MedicalAdvice::create($validated);

        return redirect()->back()->with('success', 'Medical advice created successfully.');
    }

    /**
     * Update an existing advice entry.
     */
    public function update(Request $request, int $id)
    {
        $advice = MedicalAdvice::findOrFail($id);

        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'desc'   => 'required|string',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status', $advice->status);

        $advice->update($validated);

        return redirect()->back()->with('success', 'Medical advice updated successfully.');
    }

    /**
     * Delete an advice entry.
     */
    public function destroy(int $id)
    {
        MedicalAdvice::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Medical advice deleted successfully.');
    }
}
