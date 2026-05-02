<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceAsaishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // add permission check later if needed
    }

    public function rules(): array
    {
        return [
            
            'invoices.to' => ['required', 'string', 'max:255'],
            'invoices.matter' => ['required', 'string', 'max:255'],
            'invoices.content' => ['nullable', 'string'],

            'invoices.name_one' => ['required', 'string', 'max:255'],
            'invoices.name_two' => ['required', 'string', 'max:255'],

            'invoices.national_one' => ['required', 'string', 'max:255'],
            'invoices.national_two' => ['required', 'string', 'max:255'],

            'invoices.phone_one' => ['required', 'string', 'max:30'],
            'invoices.phone_two' => ['required', 'string', 'max:30'],

            'invoices.code_one' => ['required', 'string', 'max:50'],
            'invoices.code_two' => ['required', 'string', 'max:50'],

            'invoices.mamala_type' => ['required', 'string', 'max:50'],

            'invoices.zhmarai_mulk' => ['required', 'string', 'max:50'],
            'invoices.zhmarai_xanw' => ['required', 'string', 'max:50'],
            'invoices.zhmarai_garak' => ['required', 'string', 'max:50'],
            'invoices.zhmarai_kolan' => ['required', 'string', 'max:50'],

            'invoices.note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoices.to.required' => 'خانەی (بۆ) پێویستە',
            'invoices.matter.required' => 'بابەت پێویستە',

            'invoices.name_one.required' => 'ناوی لایەنی یەکەم پێویستە',
            'invoices.name_two.required' => 'ناوی لایەنی دووەم پێویستە',

            'invoices.phone_one.required' => 'ژمارەی تەلەفۆنی لایەنی یەکەم پێویستە',
            'invoices.phone_two.required' => 'ژمارەی تەلەفۆنی لایەنی دووەم پێویستە',

            'invoices.mamala_type.required' => 'جۆری مامەڵە پێویستە',
        ];
    }
}
