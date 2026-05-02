<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceNormal extends Model
{
    protected $fillable = [
        'user_id',
        'molat_no',
        'name_one',
        'penas_one',
        'phone_one',
        'name_two',
        'penas_two',
        'phone_two',

        'mulk_one',
        'mulk_type',
        'mulk_no',
        'mulk_garak',
        'mulk_metter',
        'nrxi_froshtn',
        'nrxi_peshaki',
        'nrxi_mawa',
        'peshaki_layaniyakam',
        'nrxi_pashimani_one',
        'nrxi_pashimani_two',
        'datecholkrdn_year',
        'from_datecholkrdn',
        'to_datecholkrdn',
        
        'krey_mangana',
        'skay_panjara',
        'pankay_asman',
        'sngi_cheshtnga',
        'dukalkesh',
        'naw_kwlenar',
        'dastshor',
        'tankiaw',
        'garak_one',
        'garak_two',
        'job_one',
        'job_two',
        'shahid_one',
        'shahid_two',

        'date_invoice',
    ];

    protected $casts = [
        'date_invoice' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
