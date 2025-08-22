<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'price_conversion' => ['nullable', 'numeric'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['required', 'in:send,receive'],
            'is_businessman' => ['required', 'boolean'],
            'amount_USD' => ['required', 'numeric'],
            'amount_IQD' => ['required', 'numeric'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'is_customer' => ['nullable', 'boolean'],

        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('company_id') && !$this->filled('customer_id') && !$this->filled('name')) {
                $validator->errors()->add('company_id', 'بەکەمترینێک ناوی کڕیار یان ناوی فرۆشیار پێویستە.');
            }
        });
    }
}
