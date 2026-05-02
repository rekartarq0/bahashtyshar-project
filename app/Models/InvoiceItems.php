<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItems extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id',
        'invoice_id',
        'name',
        'measure',
        'price',
        'quantity',
        'unit_price',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoices::class);
    }
}
