<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
            'invoices.to' => 'required|string|max:255',
            'invoices.matter' => 'required|string|max:255',
            'invoices.content' => 'required|string|max:255',
            'invoices.projects_id' => 'required|exists:projects,id',
            'invoices.datenow' => 'required|date',

            'selectedItems' => 'nullable|array',
            'selectedItems.*.name' => 'required|string|max:255',
            'selectedItems.*.measure' => 'required|string|max:255',
            'selectedItems.*.quantity' => 'required|numeric',
            'selectedItems.*.unit_price' => 'required|numeric',
            'selectedItems.*.price' => 'required|numeric',

        ];
    }

    public function messages(): array
    {
        return [
            // Invoice fields
            'invoices.to.required' => 'ناوی کەسەکە یان کۆمپانیای وەک گیرایەوە پێویستە.',
            'invoices.to.string'   => 'ناوی کەسەکە یان کۆمپانیا دەبێت تەنها نووسین بێت.',
            'invoices.to.max'      => 'ناوی کەسەکە نابێت زیاتر لە ٢٥٥ پیت بێت.',
            'invoices.projects_id.required' => 'ناوی پڕۆژەی پرۆژە پێویستە.',
            'invoices.projects_id.exists' => 'ناوی پڕۆژەی پرۆژە دەبێت تەنها نووسین بێت.',
            'invoices.datenow.required' => 'بەروار پێویستە.',
            'invoices.datenow.date' => 'بەروار دەبێت تەنها نووسین بێت.',

            'invoices.matter.required' => 'بابەت پێویستە.',
            'invoices.matter.string'   => 'بابەت دەبێت تەنها نووسین بێت.',
            'invoices.matter.max'      => 'بابەت نابێت زیاتر لە ٢٥٥ پیت بێت.',

            'invoices.content.required' => 'ناوەڕۆک پێویستە.',
            'invoices.content.string'   => 'ناوەڕۆک دەبێت تەنها نووسین بێت.',
            'invoices.content.max'      => 'ناوەڕۆک نابێت زیاتر لە ٢٥٥ پیت بێت.',

            // Selected items (array)
            'selectedItems.required' => 'دەتوانیت بەلایەنی کەم ١ بابەت زیاد بکەیت.',
            'selectedItems.array'    => 'پێویستە لیستی بابەتکان دابنرێت.',

            // Selected items -> name
            'selectedItems.*.name.required' => 'ناوی بابەت پێویستە.',
            'selectedItems.*.name.string'   => 'ناوی بابەت دەبێت نووسین بێت.',
            'selectedItems.*.name.max'      => 'ناوی بابەت نابێت زیاتر لە ٢٥٥ پیت بێت.',

            // Selected items -> measure
            'selectedItems.*.measure.required' => 'یەکەی بابەت پێویستە.',
            'selectedItems.*.measure.string'   => 'یەکەی بابەت دەبێت نووسین بێت.',
            'selectedItems.*.measure.max'      => 'یەکەی بابەت نابێت زیاتر لە ٢٥٥ پیت بێت.',

            // Selected items -> quantity
            'selectedItems.*.quantity.required' => 'ژمارەی بابەت پێویستە.',
            'selectedItems.*.quantity.numeric'  => 'ژمارەی بابەت دەبێت ژمارە بێت.',

            // Selected items -> unit price
            'selectedItems.*.unit_price.required' => 'نرخی یەکەی بابەت پێویستە.',
            'selectedItems.*.unit_price.numeric'  => 'نرخی یەکەی بابەت دەبێت ژمارە بێت.',

            // Selected items -> price
            'selectedItems.*.price.required' => 'نرخی بابەت پێویستە.',
            'selectedItems.*.price.numeric'  => 'نرخی بابەت دەبێت ژمارە بێت.',
        ];
    }
}
