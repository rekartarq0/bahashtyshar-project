<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStagesRequest;
use App\Models\Projects;
use App\Models\ProjectStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProjectStageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read projects')->only('index');
        $this->middleware('permission:write projects')->only('store');
        $this->middleware('permission:edit projects')->only(['show', 'update']);
        $this->middleware('permission:delete projects')->only('destroy');
    }
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 25);

        $Projects = Projects::with(['user:id,name', 'stages'])
            ->where('name', 'LIKE', '%' . $q . '%')
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString(); // Change '10' to the number of items per page you want

        return Inertia::render('ProjectStages/Index', [
            'data' => $Projects // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function show($id)
    {
        $project = Projects::findorfail($id);
        $projectStages = ProjectStage::with(['user:id,name'])
            ->where('project_id', $id)
            ->get();
        return Inertia::render('ProjectStages/Detail/Index', [
            'data' => [
                'project' => $project,
                'projectStages' => $projectStages,
                'stages' => [
                    [
                        'name'  => 'Planning',
                        'label' => 'پلاندانان',
                    ],
                    [
                        'name'  => 'Creative',
                        'label' => 'دروستکردن',
                    ],
                    [
                        'name'  => 'Design',
                        'label' => 'دیزاین',
                    ],
                    [
                        'name'  => 'Sale',
                        'label' => 'فرۆشتن',
                    ],
                ]

            ],
        ]);
    }
    public function store(ProjectStagesRequest $request)
    {
        $request->validated();


        $projectId = $request->project_id;

        $project = Projects::with('stages')->findOrFail($projectId);

        // 1. Close the current active stage
        $activeStage = $project->stages()->whereNull('end_time')->latest('start_time')->first();
        if ($activeStage) {
            $activeStage->update([
                'end_time' => $request->end_time
                    ? Carbon::parse($request->end_time)
                    : Carbon::now(),
            ]);
        }

        // 2. Create the new stage
        ProjectStage::create([
            'project_id' => $projectId,
            'stage'      => $request->stage,
            'start_time' => $request->start_time
                ? Carbon::parse($request->start_time)
                : Carbon::now(),
            'end_time'   => $request->end_time
                ? Carbon::parse($request->end_time)
                : null,
            'user_id'    => Auth::id(),
        ]);

        return redirect()->back()->with([
            'message' => 'زیادکرا'
        ]);
    }
    public function edit($id)
    {
        $projectStage = ProjectStage::findorfail($id);
        return redirect()->back()->with([
            'data' => $projectStage // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
  
    public function update(ProjectStagesRequest $request, $id)
    {
        $request->validated();

        $stage = ProjectStage::findOrFail($id);

        $stage->update([
            'project_id' => $request->project_id,
            'stage'      => $request->stage,
            'start_time' => $request->start_time ? Carbon::parse($request->start_time)->timezone('Asia/Baghdad') : $stage->start_time,
            'end_time'   => $request->end_time ? Carbon::parse($request->end_time)->timezone('Asia/Baghdad') : $stage->end_time,
            'user_id'    => auth()->id(),
        ]);

        return redirect()->back()->with([
            'message' => 'بەسەرکەوتووی نوێکرایەوە!',
        ]);
    }
    public function destroy($id)
    {
        $stage = ProjectStage::findOrFail($id);
        $stage->delete(); // Soft delete the Projects
        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }
}
