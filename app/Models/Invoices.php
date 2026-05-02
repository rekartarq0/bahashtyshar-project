<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'to',
        'matter',
        'content',
        'total_price',
        'projects_id',
        'datenow'
    ];
    protected $casts = [
        'datenow' => 'date',
    ];

    // In Invoices model
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItems::class, 'invoice_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function projects(){
        return $this->belongsTo(Projects::class);
    }
}
