<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MulkImage extends Model
{

    use HasFactory;
    protected $fillable = ['mulk_id', 'path'];

     public function mulk()
    {
return $this->belongsTo(Mulks::class, 'mulk_id', 'id');
    }

}
