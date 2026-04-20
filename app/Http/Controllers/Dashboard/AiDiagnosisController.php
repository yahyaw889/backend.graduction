<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AiDiagnosis;
use Illuminate\Http\Request;

class AiDiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $query = AiDiagnosis::with('user')->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('diagnosis', 'like', '%' . $request->search . '%')
                  ->orWhere('patient_age', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $diagnoses = $query->paginate(10);

        return view('dashboard.ai-diagnoses', compact('diagnoses'));
    }

    public function destroy($id)
    {
        $diagnosis = AiDiagnosis::findOrFail($id);
        $diagnosis->delete();
        return redirect()->back()->with('success', 'Diagnosis record deleted successfully.');
    }
}
