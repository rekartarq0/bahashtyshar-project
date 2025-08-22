<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStage extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['project_id', 'stage', 'start_time', 'end_time', 'user_id'];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Search by dates
    public function ScopeSearchByDates($query, $startdate, $enddate)
    {
        if (!empty($startdate) && !empty($enddate)) {
            $enddate = Carbon::parse($enddate)->endOfDay();
            $startdate = Carbon::parse($startdate)->startOfDay();
            $query->whereBetween('start_time', [$startdate, $enddate]);
        }
        return $query;
    }
    // Search by project ID
    public function scopeSearchByProjectID($query, $search)
    {
        if (!empty($search)) {
            $scopeIDS = $search;
            $query->whereHas('project', function ($typequery) use ($scopeIDS) {
                $typequery->whereIn('id', $scopeIDS);
            });
        }

        return $query;
    }
}
