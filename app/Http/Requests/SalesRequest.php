<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
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
            'invoiceData.customer_id' => 'required|exists:customers,id', // Ensure the company exists
            'invoiceData.discount' => 'nullable|min:0', // Ensure the company exists

            'invoiceData.stock_type_id' => 'required|exists:type_stocks,id', // Ensure the stock type exists
            'invoiceData.status_invoice_money' => 'required|in:1,2', // 1 = debt, 2 = cash
            'invoiceData.invoice_date' => 'required|date',
            'invoiceData.currency_price' => 'required|in:USD,IQD', // Currency validation
            'invoiceData.price_conversion' => 'required|numeric|min:0.01', // Ensure it's greater than 0
            'invoiceData.status_invoice' => 'required|in:draft,straight', // Invoice status
            'invoiceData.sale_status' => 'required|in:single_price,whole_price,seat_price,packet_price', // Invoice status
            'invoiceData.total_amount' => 'required|numeric|min:0', // Total amount must be numeric and non-negative
            'invoiceData.notes' => 'nullable|string|max:1000', // Total amount must be numeric and non-negative

            // Items Validation
            'items' => 'required|array|min:1', // Ensure at least one item is provided
            'items.*.id' => 'required|exists:items,id', // Each item ID must exist in the `items` table
            'items.*.quantity' => 'required|numeric|min:0.01', // Quantity must be a positive integer
            'items.*.buy_price_usd' => 'required|numeric|min:0', // Unit price must be numeric and non-negative
            'items.*.buy_price_iqd' => 'required|numeric|min:0', // Unit price must be numeric and non-negative
            'items.*.sell_price_iqd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sell_price_usd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.wholesale_sell_price_iqd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.wholesale_sell_price_usd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sell_set_price_usd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sell_set_price_iqd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sell_packet_price_usd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sell_packet_price_iqd' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.number_in_packet' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.number_in_packet_in_set' => 'required|numeric|min:0', // Unit price must be numeric and
            'items.*.sale_status' => 'required|in:single_price,whole_price,seat_price,packet_price', // Invoice status
        ];
    }
}
