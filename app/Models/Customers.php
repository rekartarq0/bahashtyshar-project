<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'price_one',
        'price_two',
        'note',
        'color',         // ✅ new
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->belongsToMany(
            Locations::class,
            'customer_location',
            'customer_id',
            'location_id'
        );
    }

    public function typeProjects()
    {
        return $this->belongsToMany(
            TypeProject::class,
            'customer_type_project',
            'customer_id',
            'type_project_id'
        );
    }

    public function mulks()
    {
        return $this->belongsToMany(
            Mulks::class,
            'customer_mulk',
            'customer_id',
            'mulk_id'
        );
    }

    public function mulkstages()
    {
        return $this->hasMany(MulkStages::class, 'customer_id');
    }
}