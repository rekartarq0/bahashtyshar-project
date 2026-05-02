<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeProjectRequest;
use App\Models\TypeProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; 

class TypeProjectController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $typeProject = TypeProject::
            where('name', 'LIKE', '%' . $q . '%')
            ->orderByDesc('id')
            ->get();
        return Inertia::render('TypeProject/Index', [
            'data' => $typeProject // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function store(TypeProjectRequest $request)
    {
        $data = $request->validated();
        TypeProject::create($data);
        return redirect()->back()->with([
            'message' => 'زیادکرا'
        ]);
    }

    public function show($id)
    {
        $typeProject = typeProject::findorfail($id);
        if (!$typeProject) {
            return redirect()->back()->with(['message' => 'پۆلێنەکە نەدۆزراییەوە'], 404);
        }
        return redirect()->back()->with([
            'data' => $typeProject // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function update(TypeProjectRequest $request, $id)
    {
        $data = $request->validated();
        $Item = typeProject::find($id);
        $Item->update($data);
        return redirect()->back()->with([
            'message' => 'نوێکرایەوە',
        ]);
    }

    public function destroy($id)
    {
        $Item = typeProject::findOrFail($id);
        $Item->delete(); // Soft delete the user
        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }}
