<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mulks extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'location',
        'price',
        'Rwbar',
        'note',
        'number_mulk',
        'link_location',
        'type_project_id',
        'location_id',
        'user_id',
        'is_archived',

        // ✅ NEW
        'emara',
        'qat',
        'zhmarai_shwqa',
        'facebook_link', // ✅ NEW

    ];

    protected $casts = [
    'is_archived' => 'boolean',
];

    // Owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Images
    public function images()
    {
        return $this->hasMany(MulkImage::class, 'mulk_id');
    }

    public function typeProject()
    {
        return $this->belongsTo(TypeProject::class, 'type_project_id');
    }

    public function stages()
    {
        return $this->hasMany(MulkStages::class, 'mulk_id');
    }

    public function customers()
    {
        return $this->belongsToMany(
            Customers::class,
            'customer_mulk',
            'mulk_id',     // FK on pivot
            'customer_id'  // FK on pivot
        );
    }

    public function location()
    {
        return $this->belongsTo(Locations::class, 'location_id');
    }
}
