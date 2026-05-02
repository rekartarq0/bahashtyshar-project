<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationsRequest;
use App\Models\Locations;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationsController extends Controller
{
 public function index(Request $request)
    {
        $q = $request->input('q');

        $locations = Locations::where('name', 'LIKE', '%' . $q . '%')
            ->orderByDesc('id')
            ->get();
        return Inertia::render('Locations/Index', [
            'data' => $locations // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function store(LocationsRequest $request)
    {
        $data = $request->validated();
        locations::create($data);
        return redirect()->back()->with([
            'message' => 'زیادکرا'
        ]);
    }

    public function show($id)
    {
        $locations = locations::findorfail($id);
        if (!$locations) {
            return redirect()->back()->with(['message' => 'پۆلێنەکە نەدۆزراییەوە'], 404);
        }
        return redirect()->back()->with([
            'data' => $locations // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function update(LocationsRequest $request, $id)
    {
        $data = $request->validated();
        $Item = locations::find($id);
        $Item->update($data);
        return redirect()->back()->with([
            'message' => 'نوێکرایەوە',
        ]);
    }

    public function destroy($id)
    {
        $Item = locations::findOrFail($id);
        $Item->delete(); // Soft delete the user
        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }}
