<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Models\Projects;
use App\Models\ProjectStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectsController extends Controller
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

        $Projects = Projects::with(['user:id,name','stages'])
            ->where('name', 'LIKE', '%' . $q . '%')
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString(); // Change '10' to the number of items per page you want

        return Inertia::render('Projects/Index', [
            'data' => $Projects // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function store(ProjectStoreRequest $request){
        $data = $request->validated();
        // Create the project for the logged-in user
        $project = Auth::user()->Projects()->create($data);

        // Create the default stage "Planning"
        ProjectStage::create([
            'project_id' => $project->id,
            'stage'      => 'Planning',
            'start_time' => Carbon::now(),
            'user_id'    => Auth::id(),
        ]);
        return redirect()->back()->with([
            'message' => 'زیادکرا'
        ]);
    }
    public function show($id)
    {
        $project = Projects::findorfail($id);
        if (!$project) {
            return redirect()->back()->with(['message' => 'پڕۆژەکە نەدۆزراییەوە'], 404);
        }
        return redirect()->back()->with([
            'data' => $project // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function update(ProjectStoreRequest $request, $id)
    {
        $data = $request->validated();
        $Item = Projects::find($id);
        $Item->update($data);
        return redirect()->back()->with([
            'message' => 'نوێکرایەوە',
        ]);
    }
    public function destroy($id)
    {
        $Item = Projects::findOrFail($id);
        $Item->delete(); // Soft delete the Projects
        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }

}
