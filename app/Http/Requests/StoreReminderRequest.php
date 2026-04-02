<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                                  => 'required|string|max:255',
            'description'                            => 'nullable|string',
            'active'                                 => 'boolean',
            'recurrence_rules'                       => 'required|array|min:1',
            'recurrence_rules.*.frequency'           => 'required|in:daily,weekly,monthly,yearly,custom',
            'recurrence_rules.*.interval'            => 'integer|min:1',
            'recurrence_rules.*.days_of_week'        => 'nullable|array',
            'recurrence_rules.*.days_of_week.*'      => 'integer|min:0|max:6',
            'recurrence_rules.*.days_of_month'       => 'nullable|array',
            'recurrence_rules.*.days_of_month.*'     => 'integer|min:1|max:31',
            'recurrence_rules.*.months_of_year'      => 'nullable|array',
            'recurrence_rules.*.months_of_year.*'    => 'integer|min:1|max:12',
            'recurrence_rules.*.time'                => 'nullable|date_format:H:i',
            'recurrence_rules.*.start_date'          => 'required|date',
            'recurrence_rules.*.end_date'            => 'nullable|date|after_or_equal:recurrence_rules.*.start_date',
            'exceptions'                             => 'nullable|array',
            'exceptions.*.date'                      => 'required|date',
            'exceptions.*.action'                    => 'required|in:skip,modify',
            'exceptions.*.new_time'                  => 'nullable|date_format:H:i|required_if:exceptions.*.action,modify',
        ];
    }
}
