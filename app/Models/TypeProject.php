<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TypeProject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function customers()
    {
        return $this->belongsToMany(
            Customers::class,
            'customer_type_project',
            'type_project_id',
            'customer_id'
        );
    }
}
