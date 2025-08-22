<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyProductRequest extends FormRequest
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
            'invoiceData.company_id' => 'required|exists:companies,id', // Ensure the company exists
            'invoiceData.stock_type_id' => 'required|exists:type_stocks,id', // Ensure the stock type exists
            'invoiceData.status_invoice_money' => 'required|in:1,2', // 1 = debt, 2 = cash
            'invoiceData.invoice_date' => 'required|date',
            'invoiceData.currency_price' => 'required|in:USD,IQD', // Currency validation
            'invoiceData.price_conversion' => 'required|numeric|min:0.01', // Ensure it's greater than 0
            'invoiceData.discount' => 'nullable|numeric|min:0', // Optional discount
            'invoiceData.status_invoice' => 'required|in:draft,straight', // Invoice status
            'invoiceData.total_amount' => 'required|numeric|min:0', // Total amount must be numeric and non-negative
            'invoiceData.notes' => 'nullable|string|max:1000', // Total amount must be numeric and non-negative

            // Items Validation
            'items' => 'required|array|min:1', // Ensure at least one item is provided
            'items.*.id' => 'required|exists:items,id', // Each item ID must exist in the `items` table
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price_usd' => 'required|numeric|min:0.01',
            'items.*.buy_price_iqd' => 'required|numeric|min:0.01',
        ];
    }
    public function messages(): array
    {
        return [
            'invoiceData.company_id.required' => 'دڵنییابەرەوە لە هەڵبژاردنی دابینکار.',
            'invoiceData.stock_type_id.required' => 'دڵنییابەرەوە لە هەڵبژاردنی  کۆگای دروست.',
            'items.required' => 'دڵنییابەرەوە لە هەڵبژاردنی ماددەکان.',
            // i need dont equal to 0 this price_conversion 
            'invoiceData.price_conversion.min' => 'دڵنییابەرەوە لە دیاریکردنی نرخی دۆلار.',
        ];
    }
}
