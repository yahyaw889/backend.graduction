<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'image_path' => collect($this->image_path)->map(fn($img) => asset('storage/'.$img)),
            'risk_percentage' => $this->risk_percentage,
            'recommendation' => $this->getRecommendationInArabic(),
            'recommendation_en' => $this->recommendation,
            'report_text' => $this->report_text,
            'symptoms_text' => $this->symptoms_text,
            'symptoms_selected' => SymptomResource::collection($this->symptoms()),
            'status' => $this->getStatusInArabic(),
            'status_en' => $this->status,
            'reason' => $this->reason,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get status in Arabic
     */
    protected function getStatusInArabic(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'جاري المعالجة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Get recommendation in Arabic
     */
    protected function getRecommendationInArabic(): ?string
    {
        if (!$this->recommendation) {
            return null;
        }

        return match($this->recommendation) {
            'take_precautions' => 'اتخذ الاحتياطات',
            'take_precautions_and_see_doctor' => 'اتخذ الاحتياطات واستشر طبيب',
            'see_doctor' => 'استشر طبيب فوراً',
            default => $this->recommendation,
        };
    }

    /**
     * Get model type in Arabic
     */
    protected function getModelTypeInArabic(): string
    {
        return match($this->model_type) {
            'model_image' => 'نموذج الصور',
            'model_text' => 'نموذج النصوص',
            'both' => 'كلاهما',
            'other' => 'آخر',
            default => $this->model_type,
        };
    }
}
