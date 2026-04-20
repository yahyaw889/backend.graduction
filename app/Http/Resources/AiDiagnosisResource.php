<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AiDiagnosisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user ? $this->user->name : null,
            ],
            'image_url' => $this->image_path ? url('api/ai-diagnosis/image/' . basename($this->image_path)) : null,
            'patient_data' => [
                'age' => $this->patient_age,
                'gender' => $this->patient_gender,
                'reported_symptoms' => $this->reported_symptoms,
                'duration_days' => $this->symptoms_duration_days,
            ],
            'ai_analysis' => [
                'diagnosis' => $this->diagnosis,
                'confidence_percentage' => $this->confidence_percentage,
                'symptoms_detected' => $this->symptoms_detected,
                'recommendation' => $this->recommendation,
            ],
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
