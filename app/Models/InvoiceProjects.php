<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceProjects extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'project_id',
        'qty',
        'price',
        'total',
        'user_id',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
