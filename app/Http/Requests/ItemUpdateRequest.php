<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemUpdateRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'barcode' => ['required', 'string', 'max:255'],
            'price_buy' => ['required', 'numeric'],
            'price_sell' => ['required', 'numeric'],
            'expires_at' => ['nullable', 'date'],
            'unit_id' => ['required', 'exists:units,id'],
            'sell_packet_price_usd' => ['nullable', 'numeric', 'min:0'],
            'sell_packet_price_iqd' => ['nullable', 'numeric', 'min:0'],
            'sell_set_price_usd' => ['nullable', 'numeric', 'min:0'],
            'sell_set_price_iqd' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
