<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MedicalAiService;
use App\Models\AiDiagnosis;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AiDiagnosisResource;
use App\Traits\ApiTrait;
use App\Traits\Images;

class AiDiagnosisController extends Controller
{
    use ApiTrait, Images;
    protected $aiService;

    public function __construct(MedicalAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle the incoming image and data to diagnose the condition.
     */
    public function diagnose(Request $request)
    {
        // 1. Validate incoming data (Image + Text)
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'patient_age' => 'nullable|integer',
            'patient_gender' => 'nullable|string',
            'symptoms' => 'nullable|string', // e.g. "fever, headache, rash"
            'duration_days' => 'nullable|integer'
        ]);

        try {
            // 2. Extract Data and Store Image
            $imagePath = $this->storeImage($request->file('image'), 'diagnoses');
            $fullImagePath = storage_path('app/' . $imagePath);
            
            $patientData = [
                'age' => $request->input('patient_age', 'Unknown'),
                'gender' => $request->input('patient_gender', 'Unknown'),
                'reported_symptoms' => $request->input('symptoms', 'None reported'),
                'symptoms_duration_days' => $request->input('duration_days', 'Unknown'),
            ];

            // 3. Call the AI Service
            $diagnosisResult = $this->aiService->diagnoseSkinDisease($fullImagePath, $patientData);

            if (isset($diagnosisResult['error'])) {
                return $this->errorResponse($diagnosisResult['error'], 500);
            }

            // 4. Save to Database
            $record = AiDiagnosis::create([
                'user_id' => auth()->id(), // assuming route is protected
                'image_path' => $imagePath,
                'patient_age' => $patientData['age'] !== 'Unknown' ? $patientData['age'] : null,
                'patient_gender' => $patientData['gender'] !== 'Unknown' ? $patientData['gender'] : null,
                'reported_symptoms' => $patientData['reported_symptoms'] !== 'None reported' ? $patientData['reported_symptoms'] : null,
                'symptoms_duration_days' => $patientData['symptoms_duration_days'] !== 'Unknown' ? $patientData['symptoms_duration_days'] : null,
                'diagnosis' => $diagnosisResult['diagnosis'] ?? null,
                'confidence_percentage' => $diagnosisResult['confidence_percentage'] ?? null,
                'symptoms_detected' => $diagnosisResult['symptoms_detected'] ?? [],
                'recommendation' => $diagnosisResult['recommendation'] ?? null,
            ]);

            // 5. Return the AI Diagnosis Resource using ApiTrait
            return $this->okResponse(
                new AiDiagnosisResource($record),
                'Analysis completed and saved successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Securely serve the diagnosis image
     */
    public function showImage($filename)
    {
        $path = 'diagnoses/' . $filename;
        
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        return response()->file(storage_path('app/' . $path));
    }
}
