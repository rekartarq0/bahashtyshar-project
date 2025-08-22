<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'TransferData.from_stock_id' => 'required|exists:type_stocks,id',
            'TransferData.to_stock_id' => 'required|exists:type_stocks,id',

            'items' => 'required|array|min:1', // Ensure at least one item is provided
            'items.*.item_id' => 'required|exists:items,id', // Each item ID must exist in the `items` table
            'items.*.unit_price' => 'required|numeric', // Each item ID must exist in the `items` table
            'items.*.quantity' => 'required|integer|min:1', // Quantity must be a positive integer

        ];
    }
}
