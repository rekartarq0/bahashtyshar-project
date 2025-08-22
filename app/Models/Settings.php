<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone1',
        'phone2',
        'title1',
        'title2',
        'note',
        'image',
    ];
}
