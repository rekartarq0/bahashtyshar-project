<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceNormalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->datecholkrdn_year) {
            $this->merge([
                'datecholkrdn_year' => $this->datecholkrdn_year.'-01-01',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'molat_no' => ['nullable', 'string', 'max:50'],

            'name_one' => ['required', 'string', 'max:255'],
            'penas_one' => ['required', 'string', 'max:50'],
            'phone_one' => ['required', 'string', 'max:20'],

            'name_two' => ['required', 'string', 'max:255'],
            'penas_two' => ['required', 'string', 'max:50'],
            'phone_two' => ['required', 'string', 'max:20'],

            'mulk_one' => ['required', 'string', 'max:255'],
            'mulk_type' => ['required', 'string', 'max:100'],
            'mulk_no' => ['required', 'string', 'max:50'],
            'mulk_garak' => ['required', 'string', 'max:100'],
            'mulk_metter' => ['required', 'string', 'max:50'],

            'nrxi_froshtn' => ['required', 'numeric'],
            'nrxi_peshaki' => ['required', 'numeric'],
            'nrxi_mawa' => ['required', 'numeric'],

            'peshaki_layaniyakam' => ['required', 'numeric'],
            'nrxi_pashimani_one' => ['required', 'numeric'],
            'nrxi_pashimani_two' => ['required', 'numeric'],

            'datecholkrdn_year' => ['nullable', 'date'],
            'from_datecholkrdn' => ['nullable', 'date'],
            'to_datecholkrdn' => ['nullable', 'date'],

            'krey_mangana' => ['nullable', 'numeric'],

            'skay_panjara' => ['nullable', 'string', 'max:50'],
            'pankay_asman' => ['nullable', 'string', 'max:50'],
            'sngi_cheshtnga' => ['nullable', 'string', 'max:50'],
            'dukalkesh' => ['nullable', 'string', 'max:50'],
            'naw_kwlenar' => ['nullable', 'string', 'max:50'],
            'dastshor' => ['nullable', 'string', 'max:50'],
            'tankiaw' => ['nullable', 'string', 'max:50'],

            'garak_one' => ['required', 'string', 'max:255'],
            'garak_two' => ['required', 'string', 'max:255'],

            'job_one' => ['required', 'string', 'max:255'],
            'job_two' => ['required', 'string', 'max:255'],

            'shahid_one' => ['required', 'string', 'max:255'],
            'shahid_two' => ['required', 'string', 'max:255'],
            'emara' => 'nullable|string',
            'qat' => 'nullable|string',
            'zhmarai_shwqa' => 'nullable|string',

            'date_invoice' => ['nullable', 'date'],

            'phone_one_shahid' => 'nullable|string',
            'phone_two_shahid' => 'nullable|string',
            'zhmarai_rozhi_cholkrdn' => 'nullable|string',
            'krey_mangana_currency' => 'nullable|string',

            'mawai_katy_cholkrdn' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name_one.required' => 'ناوی لایەنی یەکەم پێویستە.',
            'penas_one.required' => 'ناسنامەی لایەنی یەکەم پێویستە.',
            'phone_one.required' => 'ژمارەی مۆبایلی لایەنی یەکەم پێویستە.',

            'name_two.required' => 'ناوی لایەنی دووەم پێویستە.',
            'penas_two.required' => 'ناسنامەی لایەنی دووەم پێویستە.',
            'phone_two.required' => 'ژمارەی مۆبایلی لایەنی دووەم پێویستە.',

            'mulk_one.required' => 'ناوی موڵک پێویستە.',
            'mulk_type.required' => 'جۆری موڵک پێویستە.',
            'mulk_no.required' => 'ژمارەی موڵک پێویستە.',
            'mulk_garak.required' => 'گەڕەکی موڵک پێویستە.',
            'mulk_metter.required' => 'مەترەی موڵک پێویستە.',

            'nrxi_froshtn.required' => 'نرخی فرۆشتن پێویستە.',
            'nrxi_froshtn.numeric' => 'نرخی فرۆشتن دەبێت ژمارە بێت.',

            'nrxi_peshaki.required' => 'نرخی پێشەکی پێویستە.',
            'nrxi_peshaki.numeric' => 'نرخی پێشەکی دەبێت ژمارە بێت.',

            'nrxi_mawa.required' => 'نرخی ماوە پێویستە.',
            'nrxi_mawa.numeric' => 'نرخی ماوە دەبێت ژمارە بێت.',

            'peshaki_layaniyakam.required' => 'پێشەکی لایەنی یەکەم پێویستە.',
            'peshaki_layaniyakam.numeric' => 'پێشەکی لایەنی یەکەم دەبێت ژمارە بێت.',

            'nrxi_pashimani_one.required' => 'نرخی پاشمانی لایەنی یەکەم پێویستە.',
            'nrxi_pashimani_one.numeric' => 'نرخی پاشمانی لایەنی یەکەم دەبێت ژمارە بێت.',

            'nrxi_pashimani_two.required' => 'نرخی پاشمانی لایەنی دووەم پێویستە.',
            'nrxi_pashimani_two.numeric' => 'نرخی پاشمانی لایەنی دووەم دەبێت ژمارە بێت.',

            'garak_one.required' => 'گەڕەکی لایەنی یەکەم پێویستە.',
            'garak_two.required' => 'گەڕەکی لایەنی دووەم پێویستە.',

            'job_one.required' => 'کارى لایەنی یەکەم پێویستە.',
            'job_two.required' => 'کارى لایەنی دووەم پێویستە.',

            'shahid_one.required' => 'شاهدی یەکەم پێویستە.',
            'shahid_two.required' => 'شاهدی دووەم پێویستە.',

            'datecholkrdn_year.date' => 'ساڵی چۆڵکردنەوە دەبێت بەروار بێت.',
            'from_datecholkrdn.date' => 'بەرواری دەستپێک دەبێت بەروار بێت.',
            'to_datecholkrdn.date' => 'بەرواری کۆتایی دەبێت بەروار بێت.',

            'krey_mangana.numeric' => 'کرێی مانگانە دەبێت ژمارە بێت.',

            'date_invoice.date' => 'بەرواری پسوڵە دەبێت بەروار بێت.',
        ];
    }
}
