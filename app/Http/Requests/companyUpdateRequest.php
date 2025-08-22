<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class companyUpdateRequest extends FormRequest
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
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            //add the debt_IQD,debt_USD 
            'debt_IQD' => ['nullable', 'numeric', 'min:0'],
            'debt_USD' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
