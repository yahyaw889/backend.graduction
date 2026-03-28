<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image_path' => 'nullable|array',
            'image_path.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'symptoms_text' => 'nullable|string|max:5000',
            'symptoms_selected' => 'nullable|array',
            'symptoms_selected.*' => 'integer|exists:symptoms,id',
        ];
    }

    /**
     * Get custom messages for validator errors in Arabic.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image_path.array' => 'يجب أن تكون الصور في صيغة مصفوفة',
            'symptoms_text.string' => 'يجب أن يكون وصف الأعراض نصاً',
            'symptoms_text.max' => 'وصف الأعراض يجب ألا يتجاوز 5000 حرف',
            'symptoms_selected.array' => 'يجب أن تكون الأعراض المحددة في صيغة مصفوفة',
            'symptoms_selected.*.integer' => 'يجب أن يكون معرف العرض رقماً صحيحاً',
            'symptoms_selected.*.exists' => 'العرض المحدد غير موجود',
            'model_type.required' => 'نوع النموذج مطلوب',
            'model_type.in' => 'نوع النموذج يجب أن يكون: صورة، نص، كلاهما، أو آخر',
        ];
    }

    /**
     * Get custom attributes for validator errors in Arabic.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'image_path' => 'مسار الصور',
            'symptoms_text' => 'وصف الأعراض',
            'symptoms_selected' => 'الأعراض المحددة',
            'model_type' => 'نوع النموذج',
            'reason' => 'السبب',
        ];
    }
}
