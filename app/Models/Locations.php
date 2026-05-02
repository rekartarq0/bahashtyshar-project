<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Locations extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function customers()
    {
        return $this->belongsToMany(
            Customers::class,
            'customer_location',
            'location_id',
            'customer_id'
        );
    }
}