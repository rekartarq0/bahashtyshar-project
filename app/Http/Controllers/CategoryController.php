<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read companies')->only('index');
        $this->middleware('permission:write companies')->only('store');
        $this->middleware('permission:edit companies')->only(['show', 'update']);
        $this->middleware('permission:delete companies')->only('destroy');
    }
    public function index(Request $request)
    {
        $q = $request->input('q');

        $category = Category::with(['user:id,name'])
            ->where('name', 'LIKE', '%' . $q . '%')
            ->orderByDesc('id')
            ->get();
        return Inertia::render('Category/Index', [
            'data' => $category // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        Auth::user()->Category()->create($data);
        return redirect()->back()->with([
            'message' => 'زیادکرا'
        ]);
    }

    public function show($id)
    {
        $category = Category::findorfail($id);
        if (!$category) {
            return redirect()->back()->with(['message' => 'پۆلێنەکە نەدۆزراییەوە'], 404);
        }
        return redirect()->back()->with([
            'data' => $category // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
    public function update(CategoryStoreRequest $request, $id)
    {
        $data = $request->validated();
        $Item = Category::find($id);
        $Item->update($data);
        return redirect()->back()->with([
            'message' => 'نوێکرایەوە',
        ]);
    }

    public function destroy($id)
    {
        $Item = Category::findOrFail($id);
        $Item->delete(); // Soft delete the user
        return redirect()->back()->with(['message' => 'سڕایەوە']);
    }
}
