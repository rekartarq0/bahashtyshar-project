<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\ProjectStage;
use Illuminate\Http\Request;

class ApiRepportController extends Controller
    {
    public function projects(Request $request)
    {
        $project_id = $request->input('projects_id', []); // match payload!

        $dateFilter = $request->input('datefilter', []);

        $query = ProjectStage::with(['project', 'user:id,name'])
            ->SearchByDates($dateFilter[0] ?? null, $dateFilter[1] ?? null)
            ->SearchByProjectID($project_id)
            ->orderByDesc('project_id')
            ->get();

        $itemfilters = ProjectStage::whereIn('project_id', $project_id)->get();

        $filters = [
            'projectstage' => $itemfilters,
        ];

        return response()->json([
            'status' => true,
            'data' => $query,
            'filters' => $filters
        ]);
    }
    }