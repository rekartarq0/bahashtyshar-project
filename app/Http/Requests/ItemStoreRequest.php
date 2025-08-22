<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
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
            'barcode' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,id'],
            'brand' => ['nullable', 'string'],
            'marka' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'number_in_packet' => ['nullable', 'numeric', 'min:0'],
            'number_in_packet_in_set' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'limt' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],

            'buy_price_usd' => ['required', 'numeric', 'min:0'],
            'sell_price_usd' => ['required', 'numeric', 'min:0'],
            'wholesale_sell_price_usd' => ['required', 'numeric', 'min:0'],

            'sell_packet_price_usd' => ['nullable', 'numeric', 'min:0'],
            'sell_packet_price_iqd' => ['nullable', 'numeric', 'min:0'],
            'sell_set_price_usd' => ['nullable', 'numeric', 'min:0'],
            'sell_set_price_iqd' => ['nullable', 'numeric', 'min:0'],


            'buy_price_iqd' => ['required', 'numeric', 'min:0'],
            'sell_price_iqd' => ['required', 'numeric', 'min:0'],
            'wholesale_sell_price_iqd' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:3048'], // Add rules for image validation


        ];
    }
}
