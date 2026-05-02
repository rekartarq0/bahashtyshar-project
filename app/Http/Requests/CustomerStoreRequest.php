<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:20',

            'type_project_id'   => 'required|array|min:1',
            'type_project_id.*' => 'exists:type_projects,id',

            'location_id'       => 'required|array|min:1',
            'location_id.*'     => 'exists:locations,id',

            'price_one'  => 'required|numeric|min:0',
            'price_two'  => 'required|numeric|min:0',
            'note'       => 'nullable|string|max:255',
            'stages'     => 'nullable|string',

            'color'      => 'nullable|string|max:20',   // ✅ new

            'mulks'      => 'nullable|array',
            'mulks.*'    => 'exists:mulks,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'تکایە ناو بنووسە',
            'name.string'            => 'ناو پێویستە تەنها دەقی بێت',
            'name.max'               => 'ناو ناتوانرێت زیاتر لە ٢٥٥ پیت بێت',
            'type_project_id.required' => 'تکایە پڕۆژەیەک یان زیاتر هەڵبژێرە',
            'location_id.required'   => 'تکایە شوێنێک یان زیاتر هەڵبژێرە',
            'phone.string'           => 'ژمارەی مۆبایل پێویستە دەقی بێت',
            'phone.max'              => 'ژمارەی مۆبایل ناتوانرێت زیاتر لە ٢٠ پیت بێت',
            'type_project_id.exists' => 'پڕۆژەی هەڵبژێردراو نەدۆزرایەوە',
            'location_id.exists'     => 'شوێنە هەڵبژێردراو نەدۆزرایەوە',
            'price_one.numeric'      => 'نرخ پێویستە ژمارە بێت',
            'price_one.min'          => 'نرخ ناتوانرێت کەمتر لە 0 بێت',
            'price_two.numeric'      => 'نرخ پێویستە ژمارە بێت',
            'price_two.min'          => 'نرخ ناتوانرێت کەمتر لە 0 بێت',
            'note.string'            => 'تێبینی پێویستە دەقی بێت',
            'mulks.array'            => 'موڵک پێویستە لیست بێت',
            'mulks.*.exists'         => 'موڵکی هەڵبژێردراو نەدۆزرایەوە',
        ];
    }
}