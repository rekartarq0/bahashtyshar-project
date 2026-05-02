<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Mulks;
use App\Models\MulkStages;
use App\Models\ProjectStage;
use App\Models\TypeProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $typeProject = TypeProject::select('id', 'name')->orderByDesc('id')->get();
        $locations = Locations::select('id', 'name')->orderByDesc('id')->get();
        $mulks = Mulks::with([
            'typeProject:id,name',
            'location:id,name',
        ])
            ->select('id', 'name', 'price', 'Rwbar', 'type_project_id', 'location_id',
                'emara',
                'qat',
                'zhmarai_shwqa',
                'facebook_link',
            )
            ->where('is_archived', 0)
            ->orderByDesc('id')
            ->get();

        $dateRange = $request->input('datefilter');

        $dateOne = null;
        $dateTwo = null;

        if (is_array($dateRange) && count($dateRange) === 2 && ! empty($dateRange[0]) && ! empty($dateRange[1])) {
            $dateOne = Carbon::parse($dateRange[0])->startOfDay();
            $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        }

        $stages = [
            'request',
            'prepare',
            'show',
            'handling',
            'contract',
        ];

        $projectsByStage = MulkStages::select(
            'id',
            'customer_id',
            'stage',
            'start_time',
            'end_time',
            'backed_from_pricing',
            'note'
        )
            ->with([
                'customer:id,name,price_one,price_two,color', // ✅ color included
                'customer.locations:id,name',
                'customer.mulks:id,name,price,Rwbar,type_project_id,location_id',
                    'customer.typeProjects:id,name', // ✅ ADD THIS

            ])
            ->whereHas('customer', function ($query) use ($dateOne, $dateTwo) {
                if ($dateOne && $dateTwo) {
                    $query->whereBetween('created_at', [$dateOne, $dateTwo]);
                }
                $query->where('is_archived', false);
            })
            ->get()
            ->groupBy('customer_id');

        $latestStages = collect();
        foreach ($projectsByStage as $projectId => $stagesForProject) {
            $latestStage = $stagesForProject->sortBy(function ($stage) use ($stages) {
                return array_search($stage->stage, $stages);
            })->last();

            $latestStages->push($latestStage);
        }

        $latestProjectsByStage = $latestStages->groupBy('stage');

        return Inertia::render('Dashboard', [
            'data' => [
                'stages'       => $stages,
                'typeProjects' => $typeProject,
                'locations'    => $locations,
                'mulksByStage' => $latestProjectsByStage,
                'mulks'        => $mulks,
                'datefilter'   => $dateOne && $dateTwo
                    ? [$dateOne->toDateString(), $dateTwo->toDateString()]
                    : null,
            ],
        ]);
    }

    public function screenOne(Request $request)
    {
        $dateRange = $request->input('datefilter');

        if (! is_array($dateRange) || count($dateRange) !== 2 || empty($dateRange[0]) || empty($dateRange[1])) {
            $dateRange = [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->toDateString(),
            ];
        }

        $dateOne = Carbon::parse($dateRange[0])->startOfDay();
        $dateTwo = Carbon::parse($dateRange[1])->endOfDay();

        $stages = ['Sale', 'Design'];

        $projectsByStage = ProjectStage::with('project')
            ->whereHas('project', function ($query) use ($dateOne, $dateTwo) {
                $query->whereBetween('created_at', [$dateOne, $dateTwo])
                    ->where('is_archived', false);
            })
            ->whereIn('stage', $stages)
            ->get()
            ->groupBy('project_id');

        $latestStages = collect();
        foreach ($projectsByStage as $projectId => $stagesForProject) {
            $latestStage = $stagesForProject->sortBy(function ($stage) use ($stages) {
                return array_search($stage->stage, $stages);
            })->last();

            $latestStages->push($latestStage);
        }

        $latestProjectsByStage = $latestStages->groupBy('stage');

        return Inertia::render('ScreenOne/Index', [
            'data' => [
                'stages'         => $stages,
                'projectsByStage' => $latestProjectsByStage,
                'datefilter'     => [$dateOne->toDateString(), $dateTwo->toDateString()],
            ],
        ]);
    }

    public function screenTwo(Request $request)
    {
        $dateRange = $request->input('datefilter');

        if (! is_array($dateRange) || count($dateRange) !== 2 || empty($dateRange[0]) || empty($dateRange[1])) {
            $dateRange = [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->toDateString(),
            ];
        }

        $dateOne = Carbon::parse($dateRange[0])->startOfDay();
        $dateTwo = Carbon::parse($dateRange[1])->endOfDay();

        $stages = [
            'Creative',
            'Planning',
        ];

        $projectsByStage = ProjectStage::with('project')
            ->whereHas('project', function ($query) use ($dateOne, $dateTwo) {
                $query->whereBetween('created_at', [$dateOne, $dateTwo])
                    ->where('is_archived', false);
            })
            ->whereIn('stage', $stages)
            ->get()
            ->groupBy('project_id');

        $latestStages = collect();
        foreach ($projectsByStage as $projectId => $stagesForProject) {
            $latestStage = $stagesForProject->sortBy(function ($stage) use ($stages) {
                return array_search($stage->stage, $stages);
            })->last();

            $latestStages->push($latestStage);
        }

        $latestProjectsByStage = $latestStages->groupBy('stage');

        return Inertia::render('ScreenTwo/Index', [
            'data' => [
                'stages'         => $stages,
                'projectsByStage' => $latestProjectsByStage,
                'datefilter'     => [$dateOne->toDateString(), $dateTwo->toDateString()],
            ],
        ]);
    }
}