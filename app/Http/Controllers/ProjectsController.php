<?php

namespace App\Http\Controllers;

use App\Http\Requests\MulkStoreRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Models\Customers;
use App\Models\Locations;
use App\Models\Mulks;
use App\Models\Projects;
use App\Models\ProjectStage;
use App\Models\TypeProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 10);

        // ✅ handle datefilter
        $dateRange = $request->input('datefilter');

        $dateOne = null;
        $dateTwo = null;

        if (is_array($dateRange) && count($dateRange) === 2 && ! empty($dateRange[0]) && ! empty($dateRange[1])) {
            $dateOne = Carbon::parse($dateRange[0])->startOfDay();
            $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        }

        $Projects = Mulks::select('id', 'name', 'user_id', 'created_at','is_archived', 'phone', 'location_id', 'price', 'Rwbar', 'note', 'number_mulk', 'link_location', 'type_project_id')
            ->with([
                'user:id,name',
                'typeProject:id,name', // ✅ FIXED CASE
                'location:id,name',
            ])
          ->when($q, function ($query, $q) {
    $query->where(function ($sub) use ($q) {
        $sub->where('name', 'LIKE', "%{$q}%")->orwhere('phone','LIKE',"%{$q}%")

            // 🔍 search by location name
            ->orWhereHas('location', function ($l) use ($q) {
                $l->where('name', 'LIKE', "%{$q}%");
            })

            // 🔍 search by type project name
            ->orWhereHas('typeProject', function ($t) use ($q) {
                $t->where('name', 'LIKE', "%{$q}%");
            });
    });
})

            ->when($dateOne && $dateTwo, function ($query) use ($dateOne, $dateTwo) {
                $query->whereBetween('created_at', [$dateOne, $dateTwo]);
            })
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString();

        $typeProject = TypeProject::select('id', 'name')->get();
        $locations = Locations::select('id', 'name')->get();

        return Inertia::render('Projects/Index', [
            'data' => $Projects,
            'filters' => [
                'q' => $q,
                // ✅ return datefilter only if exists
                'datefilter' => $dateOne && $dateTwo
                    ? [$dateOne->toDateString(), $dateTwo->toDateString()]
                    : null,
                    'perPage' => $perpage,
                'typeProjects' => $typeProject,
                'locations' => $locations,
            ],
        ]);
    }

    public function store(MulkStoreRequest $request)
    {
        $data = $request->validated();

        $project = Auth::user()->Mulk()->create($data);

        $publicFolder = public_path('storage/mulks');

        if (! file_exists($publicFolder)) {
            mkdir($publicFolder, 0755, true);
        }

        collect($request->file('desgin_img'))->filter()->each(function ($file) use ($project, $publicFolder) {
            $filename = uniqid().'_'.$file->getClientOriginalName();
            $filePath = $publicFolder.'/'.$filename;

            $extension = strtolower($file->getClientOriginalExtension());
            $fileSizeKB = $file->getSize() / 1024; // size in KB

            // Default quality
            $jpgQuality = 50;
            $pngCompression = 6; // middle compression for PNG

            if ($fileSizeKB > (5 * 1024)) {
                // If > 5MB
                $jpgQuality = 30;
                $pngCompression = 8;
            }

            if ($extension === 'jpg' || $extension === 'jpeg') {
                $img = imagecreatefromjpeg($file->getPathname());
                imagejpeg($img, $filePath, $jpgQuality);
                imagedestroy($img);
            } elseif ($extension === 'png') {
                $img = imagecreatefrompng($file->getPathname());
                imagepng($img, $filePath, $pngCompression);
                imagedestroy($img);
            } else {
                $file->move($publicFolder, $filename);
            }

            $project->images()->create([
                'path' => 'mulks/'.$filename,
            ]);
        });

        return redirect()->back()->with(['message' => 'زیادکرا']);
    }

    public function update(MulkStoreRequest $request, $id)
    {
        $project = Mulks::findOrFail($id);
        $data = $request->validated();

        // Update project fields
        $project->update($data);

        // --- Delete removed images ---
        $existingIds = json_decode($request->input('existing_images', '[]'), true);
        if (! is_array($existingIds)) {
            $existingIds = [];
        }

        $project->images()->whereNotIn('id', $existingIds)->get()->each(function ($img) {
            if (Storage::disk('public')->exists($img->path)) {
                Storage::disk('public')->delete($img->path);
            }
            $img->delete();
        });

        // --- Add new images with compression, same as store ---
        $files = $request->file('desgin_img', []); // default empty array
        $publicFolder = public_path('storage/mulks');

        if (! file_exists($publicFolder)) {
            mkdir($publicFolder, 0755, true);
        }

        collect($files)->filter()->each(function ($file) use ($project, $publicFolder) {
            $filename = uniqid().'_'.$file->getClientOriginalName();
            $filePath = $publicFolder.'/'.$filename;

            $extension = strtolower($file->getClientOriginalExtension());
            $fileSizeKB = $file->getSize() / 1024; // size in KB

            // Default quality
            $jpgQuality = 50;
            $pngCompression = 6;

            if ($fileSizeKB > (5 * 1024)) {
                $jpgQuality = 30;
                $pngCompression = 8;
            }

            if ($extension === 'jpg' || $extension === 'jpeg') {
                $img = imagecreatefromjpeg($file->getPathname());
                imagejpeg($img, $filePath, $jpgQuality);
                imagedestroy($img);
            } elseif ($extension === 'png') {
                $img = imagecreatefrompng($file->getPathname());
                imagepng($img, $filePath, $pngCompression);
                imagedestroy($img);
            } else {
                $file->move($publicFolder, $filename);
            }

            $project->images()->create([
                'path' => 'mulks/'.$filename,
            ]);
        });

        return redirect()->back()->with(['message' => 'نوێکرایەوە']);
    }

    public function dashboardstore(ProjectStoreRequest $request)
    {
        $data = $request->validated();

        $project = Auth::user()->Projects()->create($data);

        ProjectStage::create([
            'project_id' => $project->id,
            'stage' => 'Sale',
            'start_time' => Carbon::now(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with([
            'message' => 'زیادکرا',
        ]);
    }

    public function show($id)
    {
        $mulk = Mulks::select(
            'id',
            'name',
            'phone',
            'location_id',
            'price',
            'Rwbar',
            'note',
            'number_mulk',
            'link_location',
            'type_project_id',
            'emara',
            'qat',
            'zhmarai_shwqa',
            'facebook_link',
        )
            ->with([
                'images:id,mulk_id,path',
                'typeProject:id,name',
                'location:id,name',
            ])
            ->find($id);

        if (! $mulk) {
            return redirect()->back()->with([
                'message' => 'موڵکەکە نەدۆزراییەوە',
            ], 404);
        }

        return redirect()->back()->with([
            'data' => $mulk,
        ]);
    }

    public function preview($id)
    {
        $project = Projects::findorfail($id);
        if (! $project) {
            return redirect()->back()->with(['message' => 'پڕۆژەکە نەدۆزراییەوە'], 404);
        }

        return redirect()->back()->with([
            'data' => $project, // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }

    public function destroy($id)
    {
        $Item = Mulks::findOrFail($id);
        // unlink images
        $Item->delete(); // Soft delete the Projects

        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }

   
public function archive(Request $request){
    $data = $request->validate([
        'id'=>'required|integer',

    ]);
    $customer = Mulks::findOrFail($data['id']);
    $customer->is_archived = ! $customer->is_archived;
    $customer->save();
    return redirect()->back()->with([
        'message' => 'کڕیارەکە بە سەرکەوتوویی ئەرشیف کرا 👌',
    ]);
}

public function archivedwithmulks(Request $request)
{
    $data = $request->validate([
        'customer_id' => 'required|integer|exists:customers,id',
    ]);

    DB::transaction(function () use ($data) {

        $customer = Customers::with('mulks')->findOrFail($data['customer_id']);

        // 1️⃣ Archive customer
        $customer->update([
            'is_archived' => true,
        ]);

        // 2️⃣ Archive related mulks (if any)
        if ($customer->mulks->isNotEmpty()) {
            $customer->mulks()->update([
                'is_archived' => true,
            ]);
        }
    });

    return redirect()->back()->with([
        'message' => 'کڕیارەکە و موڵکەکانی بە سەرکەوتوویی ئەرشیف کران 👌',
    ]);
}

}
