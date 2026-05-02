<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class InvoicesAsaish extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices_asaishes';

    protected $fillable = [
        'user_id',

        'to',
        'matter',
        'content',

        'name_one',
        'name_two',
        'national_one',
        'national_two',
        'phone_one',
        'phone_two',
        'code_one',
        'code_two',

        'mamala_type',

        'zhmarai_mulk',
        'zhmarai_xanw',
        'zhmarai_garak',
        'zhmarai_kolan',

        'note',
        
    ];

    protected $casts = [
        'invoice_year' => 'integer',
    ];

    /**
     * Auto-generate invoice number per year
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            $year = now()->year;

            // Lock rows to prevent duplicates (important!)
            $lastNumber = self::where('invoice_year', $year)
                ->lockForUpdate()
                ->max('invoice_no');

            $invoice->invoice_year = $year;
            $invoice->invoice_no = $lastNumber ? $lastNumber + 1 : 1;
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pretty invoice number: INV-2026-0001
     */
    public function getInvoiceNumberAttribute()
    {
        return 'INV-' .
            $this->invoice_year . '-' .
            str_pad($this->invoice_no, 4, '0', STR_PAD_LEFT);
    }
}
