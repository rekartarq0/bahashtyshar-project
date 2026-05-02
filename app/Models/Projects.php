<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type_project_id',
        'name',
        'customer_name',
        'phone',
        'address',
        'location',
        'content',
        'measurment',
        'xamlandn',
        'is_archived',
        'telegram_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stages()
    {
        return $this->hasMany(ProjectStage::class, 'project_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeProject::class);
    }

    public function invoice_projects()
    {
        return $this->hasMany(InvoiceProjects::class, 'project_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class, 'project_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoices::class, 'projects_id');
    }
}
