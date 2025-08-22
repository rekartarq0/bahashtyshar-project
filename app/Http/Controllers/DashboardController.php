<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\ProjectStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request){
        $dateRange = $request->input('datefilter');

        if (!is_array($dateRange) || count($dateRange) !== 2 || empty($dateRange[0]) || empty($dateRange[1])) {
            // fallback default only if needed
            $dateRange = [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->toDateString()
            ];
        }

        $dateOne = Carbon::parse($dateRange[0])->startOfDay();
        $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        $stages = ['Planning', 'Creative', 'Design', 'Sale'];

        $projectsByStage = ProjectStage::with('project')
            ->whereHas('project', function ($query) use ($dateOne, $dateTwo) {
                $query->whereBetween('created_at', [$dateOne, $dateTwo]);
            })
            ->get()
            ->groupBy('stage');
        return Inertia::render('Dashboard',[
            'data'=>[
                'stages'=> $stages,
                'projectsByStage' => $projectsByStage,
                'datefilter' => [$dateOne->toDateString(), $dateTwo->toDateString()], // send to frontend


            ]
        ]);
    }
}
