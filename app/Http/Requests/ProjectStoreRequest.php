<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'user_id'         => 'nullable|exists:users,id',
            'type_project_id' => 'required|exists:type_projects,id',
            'customer_name'   => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'address'         => 'required|string',
            'location'        => 'required|string',
            'content'         => 'required|string',
            'measurment'      => 'nullable|string|max:255',
            'telegram_link'   => 'nullable|string',
            'stages'=>'nullable|string',

            // multiple images
            'desgin_img'      => 'nullable|array',
            'desgin_img.*'    => 'image|mimes:jpg,jpeg,png|max:10240',

            'xamlandn'        => 'nullable|string',
            'is_archived'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'ناوی پڕۆژە پێویستە.',
            'name.string'              => 'ناوی پڕۆژە دەبێت تێکستی بێت.',
            'name.max'                 => 'ناوی پڕۆژە نابێت زۆرتر لە 255 پیت بێت.',

            'user_id.exists'           => 'ئەو بەکارهێنەرە نابێت بۆ هەڵبژاردن.',
            'stages.string'           => 'دڵنیابەرەوە لە هەڵبژاردنی قۆناخ',

            'type_project_id.required' => 'جۆری پڕۆژە پێویستە.',
            'type_project_id.exists'   => 'جۆری پڕۆژە هەڵەیە.',

            'customer_name.required'   => 'ناوی کڕیار پێویستە.',
            'customer_name.string'     => 'ناوی کڕیار دەبێت تێکست بێت.',
            'customer_name.max'        => 'ناوی کڕیار نابێت زۆرتر لە 255 پیت بێت.',

            'phone.required'           => 'ژمارەی تەلەفون پێویستە.',
            'phone.string'             => 'ژمارەی تەلەفون دەبێت تێکست بێت.',
            'phone.max'                => 'ژمارەی تەلەفون نابێت زۆرتر لە 50 پیت بێت.',

            'address.required'         => 'ناونیشان پێویستە.',
            'location.required'        => 'شوێن پێویستە.',
            'content.required'         => 'ناوەڕۆکی پڕۆژە پێویستە.',
            'measurment.required'      => 'پیمانەکان پێویستە.',
            'measurment.max'           => 'پیمانەکان نابێت زۆرتر لە 255 پیت بێت.',

            'desgin_img.array'          => 'وێنەکان دەبێت لیستی بێت.',
            'desgin_img.*.image'        => 'هەموو فایلەکان دەبێت وێنە بێن.',
            'desgin_img.*.mimes'        => 'وێنەکان دەبێت jpg, jpeg, یا png بێن.',
            'desgin_img.*.max'          => 'قەبارەی وێنەکان نابێت زۆرتر لە 2MB بێت.',

            'xamlandn.required'        => 'زانیاری زەمین پێویستە.',
            'is_archived.boolean'      => 'بایدەستە بۆ ڕاست یان هەڵە.',
        ];
    }
}
