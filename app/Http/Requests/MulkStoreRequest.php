<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MulkStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'type_project_id' => 'required|exists:type_projects,id',
            'location_id' => 'required|exists:locations,id',

            'phone' => 'required|string|max:50',

            // ✅ NEW FIELDS
            'price' => 'required|numeric|min:0',
            'Rwbar' => 'required|string|max:255',
            'note' => 'nullable|string',
            'number_mulk' => 'nullable|string|max:100',
            'link_location' => 'nullable|url',

            'stages' => 'nullable|string',

            // ✅ NEW FIELDS
            'emara' => 'nullable|string|max:255',
            'qat' => 'nullable|string|max:255',
            'zhmarai_shwqa' => 'nullable|string|max:255',
            'facebook_link' => 'nullable|url',

            // Images
            'desgin_img' => 'nullable|array',
            'desgin_img.*' => 'image|mimes:jpg,jpeg,png|max:10240',

            'xamlandn' => 'nullable|string',
            'is_archived' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [

            'price.numeric' => 'نرخ دەبێت ژمارە بێت.',
            'price.min' => 'نرخ نابێت نزمتر لە 0 بێت.',

            'Rwbar.string' => 'ڕووبار دەبێت تێکست بێت.',
            'note.string' => 'تێبینی دەبێت تێکست بێت.',

            'number_mulk.string' => 'ژمارەی موڵک دەبێت تێکست بێت.',
            'link_location.url' => 'لینکی شوێن هەڵەیە.',
            'name.required' => 'ناوی پڕۆژە پێویستە.',
            'name.string' => 'ناوی پڕۆژە دەبێت تێکستی بێت.',
            'name.max' => 'ناوی پڕۆژە نابێت زۆرتر لە 255 پیت بێت.',

            'user_id.exists' => 'ئەو بەکارهێنەرە نابێت بۆ هەڵبژاردن.',
            'stages.string' => 'دڵنیابەرەوە لە هەڵبژاردنی قۆناخ',

            'type_project_id.required' => 'جۆری پڕۆژە پێویستە.',
            'type_project_id.exists' => 'جۆری پڕۆژە هەڵەیە.',

            'emara.string' => 'عيماره دەبێت تێکست بێت.',
            'qat.string' => 'قات دەبێت تێکست بێت.',
            'zhmarai_shwqa.string' => 'ژمارەی شوقە دەبێت تێکست بێت.',

            'customer_name.required' => 'ناوی کڕیار پێویستە.',
            'customer_name.string' => 'ناوی کڕیار دەبێت تێکست بێت.',
            'customer_name.max' => 'ناوی کڕیار نابێت زۆرتر لە 255 پیت بێت.',

            'phone.required' => 'ژمارەی تەلەفون پێویستە.',
            'phone.string' => 'ژمارەی تەلەفون دەبێت تێکست بێت.',
            'phone.max' => 'ژمارەی تەلەفون نابێت زۆرتر لە 50 پیت بێت.',

            'address.required' => 'ناونیشان پێویستە.',
            'content.required' => 'ناوەڕۆکی پڕۆژە پێویستە.',
            'measurment.required' => 'پیمانەکان پێویستە.',
            'measurment.max' => 'پیمانەکان نابێت زۆرتر لە 255 پیت بێت.',

            'desgin_img.array' => 'وێنەکان دەبێت لیستی بێت.',
            'desgin_img.*.image' => 'هەموو فایلەکان دەبێت وێنە بێن.',
            'desgin_img.*.mimes' => 'وێنەکان دەبێت jpg, jpeg, یا png بێن.',
            'desgin_img.*.max' => 'قەبارەی وێنەکان نابێت زۆرتر لە 2MB بێت.',

            'xamlandn.required' => 'زانیاری زەمین پێویستە.',
            'is_archived.boolean' => 'بایدەستە بۆ ڕاست یان هەڵە.',
        ];
    }
}
