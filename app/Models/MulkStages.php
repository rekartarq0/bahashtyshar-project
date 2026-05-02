<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MulkStages extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mulkstages';
protected $fillable = [
    'user_id',
    'customer_id',
    'stage',
    'start_time',
    'end_time',
    'note',
    'is_pricing_complete',
    'backed_from_pricing'
];

 protected $casts = [
        'backed_from_pricing' => 'boolean',];
public function customer()
{
    return $this->belongsTo(Customers::class, 'customer_id');
}


    // 👤 User who handled this stage
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🏠 Mulk
    public function mulk()
    {
        return $this->belongsTo(Mulks::class, 'mulk_id');
    }
}
