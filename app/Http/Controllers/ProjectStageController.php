<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStagesRequest;
use App\Models\Customers;
use App\Models\MulkStages;
use App\Models\Projects;
use App\Models\ProjectStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectStageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 25);

        $Projects = Projects::with(['user:id,name', 'stages'])
            ->where('name', 'LIKE', '%'.$q.'%')
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString(); // Change '10' to the number of items per page you want

        return Inertia::render('ProjectStages/Index', [
            'data' => $Projects, // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }

    public function detachMulk($customerId, $mulkId)
{
    $customer = Customers::findOrFail($customerId);

    // remove relation only
    $customer->mulks()->detach($mulkId);

    return back()->with('message', 'موڵک لابرا لە پڕۆژە');
}


    public function acceptDesign(Request $request)
    {
        $project = Projects::with('stages')->findOrFail($request->project_id);

        // Find current Design stage
        $PreviousStage = $project->stages()->where('stage', 'Design')->latest('start_time')->first();
        if (! $PreviousStage) {
            return response()->json(['error' => 'Design stage not found'], 404);
        }

        // Mark as accepted and close the stage
        $PreviousStage->update([
            'is_accepted' => true,
            'end_time' => now(),
        ]);

        // Create Pricing stage automatically
        ProjectStage::create([
            'project_id' => $project->id,
            'stage' => 'Pricing',
            'start_time' => now(),
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Design accepted and moved to Pricing']);
    }

    public function backPrevStage(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stage' => 'required|string',
            'note' => 'required|string|max:1000',
        ]);

        // ✅ Stage order (very important)
        $stages = [
            'request',
            'prepare',
            'show',
            'handling',
            'contract',
        ];

        $projectId = $request->customer_id;
        $targetStage = $request->stage;

        // validate stage in lifecycle
        if (! in_array($targetStage, $stages)) {
            return response()->json(['error' => 'Stage not valid'], 422);
        }

        $targetIndex = array_search($targetStage, $stages);

        // stages after target (need to delete)
        $stagesToDelete = array_slice($stages, $targetIndex + 1);

        // delete all stages after selected stage for this project
        MulkStages::where('customer_id', $projectId)
            ->whereIn('stage', $stagesToDelete)
            ->delete();

        // get the record of the stage we are going back to
        $stageRecord = MulkStages::firstOrNew([
            'customer_id' => $projectId,
            'stage' => $targetStage,
        ]);

        $stageRecord->start_time = $stageRecord->start_time ?? now(); // preserve existing start_time or set now
        $stageRecord->end_time = null; // reset end_time
        $stageRecord->backed_from_pricing = true; // mark it as rolled back

        $stageRecord->note = $request->note;
        $stageRecord->save();

        return redirect()->back()->with([
            'message' => "Project rolled back to stage {$targetStage}",
            'stage' => $stageRecord,
        ]);
    }

    public function show($id)
    {
        $project = Projects::select(
            'id',
            'name',
            'type_project_id',
            'customer_name',
            'phone',
            'address',
            'location',
            'content',
            'measurment',
            'xamlandn'
        )->findOrFail($id);

        $projectStages = ProjectStage::select(
            'id',
            'stage',
            'designer_name',
            'note',
            'start_time',
            'end_time'
        )
            ->where('project_id', $id)
            ->with('user:id,name') // keep user if needed
            ->get();

        return Inertia::render('ProjectStages/Detail/Index', [
            'data' => [
                'project' => $project,
                'projectStages' => $projectStages,
                'stages' => [
                    ['name' => 'Sale', 'label' => 'فرۆشتن'],
                    ['name' => 'Measurment', 'label' => 'قیاسکردن'],
                    ['name' => 'Design', 'label' => 'دیزاین'],
                    ['name' => 'Pricing', 'label' => 'نرخ پێدان'],
                    ['name' => 'Creative', 'label' => 'دروستکردن'],
                    ['name' => 'Planning', 'label' => 'دانان'],
                    ['name' => 'Accounting', 'label' => 'حسابات'],
                    ['name' => 'Reklam', 'label' => 'ڕیکلام'],
                    ['name' => 'Repair', 'label' => 'چاککردنەوە'],
                ],
            ],
        ]);
    }

    public function store(ProjectStagesRequest $request)
    {
        $request->validated();

        $customer_id = $request->customer_id;
        $project = Customers::with('mulkstages')->findOrFail($customer_id);

        // 1. Check if the project already has this stage
        $existingStage = $project->mulkstages()->where('stage', $request->stage)->first();
        if ($existingStage) {
            return redirect()->back()->withErrors([
                'stage' => 'ئەم حاڵەتە بۆ ئەم پڕۆژەیە هەیە هەڵە! 🚫',
            ]);
        }

        // 2. Close the current active stage (if exists)
        $activeStage = $project->mulkstages()->whereNull('end_time')->latest('start_time')->first();
        if ($activeStage) {
            $activeStage->update([
                'end_time' => $request->end_time ? Carbon::parse($request->end_time) : Carbon::now(),
            ]);
        }

        // 3. Create the new stage
        $newStage = MulkStages::create([
            'customer_id' => $customer_id,
            'stage' => $request->stage,
            'start_time' => $request->start_time ? Carbon::parse($request->start_time) : Carbon::now(),
            'end_time' => $request->end_time ? Carbon::parse($request->end_time) : null,
            'user_id' => Auth::id(),
            'note' => $request->note ?? null, // optional note
        ]);

        // 4. Attach mulks if provided (nullable)
        if ($request->has('mulks') && is_array($request->mulks) && count($request->mulks) > 0) {
            // 4. Sync mulks (replace old with new)
            if ($request->has('mulks') && is_array($request->mulks)) {
                $project->mulks()->sync($request->mulks);
            } else {
                // If no mulks sent → remove all
                $project->mulks()->sync([]);
            }        // syncWithoutDetaching avoids removing existing mulks
        }

        return redirect()->back()->with([
            'message' => 'زیادکرا',
        ]);
    }

    public function backToDesign(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'note' => 'required|string|max:1000',
        ]);

        // Get last stage
        $lastStage = ProjectStage::where('project_id', $request->project_id)
            ->orderByDesc('id')
            ->first();

        if (! $lastStage) {
            return redirect()->back()->withErrors(['stage' => 'هیچ قۆناخێک نییە 🚫']);
        }

        if ($lastStage->stage === 'Creative') {
            // Delete last stage (Creative)
            $lastStage->forcedelete();

            // Delete previous stage (Pricing)
            $previousStage = ProjectStage::where('project_id', $request->project_id)
                ->orderByDesc('id')
                ->first();

            if ($previousStage) {
                $previousStage->forcedelete();

                // Reopen the stage before that (Design)
                $designStage = ProjectStage::where('project_id', $request->project_id)
                    ->orderByDesc('id')
                    ->first();

                if ($designStage) {
                    $designStage->update([
                        'end_time' => null,
                        'backed_from_pricing' => true,
                        'note' => $request->note,
                    ]);
                }
            }

            return redirect()->back()->with(['message' => 'پڕۆژە گەڕایەوە بۆ دیزاین ⚡']);
        } else {
            // Delete last stage
            if ($lastStage->stage === 'Sale') {
                return redirect()->back()->withErrors(['stage' => 'ناتوانیت قۆناخی Sale بسریت 🚫']);
            }
            $lastStage->forcedelete();

            // Find previous stage
            $PreviousStage = ProjectStage::where('project_id', $request->project_id)
                ->orderByDesc('id')
                ->first();

            if ($PreviousStage) {
                $PreviousStage->update([
                    'end_time' => null, // reopen stage
                    'backed_from_pricing' => true,
                    'note' => $request->note, // save note
                ]);
            }

            return redirect()->back()->with(['message' => 'پڕۆژە گەڕایەوە بۆ قۆناخی پێشتر']);
        }
    }

    public function edit($id)
    {
        $projectStage = ProjectStage::findorfail($id);

        return redirect()->back()->with([
            'data' => $projectStage, // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }

    public function update(ProjectStagesRequest $request, $id)
    {
        $request->validated();

        $stage = ProjectStage::findOrFail($id);

        $stage->update([
            'project_id' => $request->project_id,
            'stage' => $request->stage,
            'start_time' => $request->start_time
                ? Carbon::parse($request->start_time)->timezone('Asia/Baghdad')
                : $stage->start_time,
            'end_time' => $request->has('end_time') && $request->end_time !== null
                ? Carbon::parse($request->end_time)->timezone('Asia/Baghdad')
                : ($request->end_time === null ? null : $stage->end_time),
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with([
            'message' => 'بەسەرکەوتووی نوێکرایەوە!',
        ]);
    }

    public function completePricing(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        // Find the Pricing stage of this project
        $stage = ProjectStage::where('project_id', $request->project_id)
            ->where('stage', $request->stage)
            ->firstOrFail();

        // Mark pricing as complete
        $stage->is_pricing_complete = true;
        $stage->save();

        return back()->with('success', 'Pricing stage marked as complete ✅');
    }

    public function quitStage(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stage' => 'required|string',
        ]);

        $customerId = $request->customer_id;
        $stage = $request->stage;

        // Find the latest active mulk stage for this customer
        $mulkStage = MulkStages::with(['customer', 'user', 'mulk'])
            ->where('customer_id', $customerId)
            ->where('stage', $stage)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if (! $mulkStage) {
            return response()->json([
                'error' => 'هیچ ئاستی ڕۆژنامەیەکی بەردەوام نییە!',
            ], 400);
        }

        // Update the stage to mark it finished
        $mulkStage->update([
            'end_time' => Carbon::now(),
        ]);

        return redirect()->back()->with(['message' => 'زیادکرا']);
    }

    public function destroy($id)
    {
        $projectStage = ProjectStage::findOrFail($id);
        $projectStage->forcedelete();

        return redirect()->back()->with([
            'message' => 'Deleted Success ⚡',
        ]);
    }
}
