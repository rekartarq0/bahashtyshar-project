<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStagesRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'stage' => 'required|in:request,prepare,show,handling,contract',

            'start_time' => 'required|date',   // accepts any valid datetime
            'end_time' => 'nullable|date',   // accepts any valid datetime
            'note' => 'nullable|string',   // accepts any valid datetime
            'mulks' => 'nullable|array',
            'mulks.*' => 'exists:mulks,id', // each mulk must exist in mulks table

        ];
    }
}
