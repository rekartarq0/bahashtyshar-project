<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customers;
use App\Models\MulkStages;
use App\Models\TypeProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 10);

        $dateRange = $request->input('datefilter');
        $dateOne = null;
        $dateTwo = null;

        if (is_array($dateRange) && count($dateRange) === 2 && ! empty($dateRange[0]) && ! empty($dateRange[1])) {
            $dateOne = Carbon::parse($dateRange[0])->startOfDay();
            $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        }

        $Projects = Customers::select('id', 'name', 'user_id', 'created_at', 'is_archived', 'price_one', 'price_two', 'color') // ✅ add color
            ->with([
                'user:id,name',
                'mulkstages:id,customer_id,stage,end_time',
                'typeProjects:id,name',
                'locations:id,name',
            ])
            ->when($q, function ($query, $q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
            ->when($dateOne && $dateTwo, function ($query) use ($dateOne, $dateTwo) {
                $query->whereBetween('created_at', [$dateOne, $dateTwo]);
            })
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString();

        $typeProject = TypeProject::select('id', 'name')->get();

        return Inertia::render('Customers/Index', [
            'data' => $Projects,
            'filters' => [
                'q' => $q,
                'datefilter' => $dateOne && $dateTwo
                    ? [$dateOne->toDateString(), $dateTwo->toDateString()]
                    : null,
                'typeProjects' => $typeProject,
            ],
        ]);
    }

    public function store(CustomerStoreRequest $request)
    {
        $data = $request->validated();

        // 1️⃣ Create customer
        $customer = Auth::user()->Customer()->create([
            'name'      => $data['name'],
            'phone'     => $data['phone'],
            'price_one' => $data['price_one'],
            'price_two' => $data['price_two'],
            'note'      => $data['note'] ?? null,
            'color'     => $data['color'] ?? '#22c55e', // ✅ save color, default green
        ]);

        // 2️⃣ Attach MULTI locations
        $customer->locations()->sync($data['location_id']);

        // 3️⃣ Attach MULTI type projects
        $customer->typeProjects()->sync($data['type_project_id']);

        // 4️⃣ Mulks (optional)
        if (! empty($data['mulks'])) {
            $customer->mulks()->sync($data['mulks']);
        }

        // 5️⃣ Stage
        $stage = $data['stages'] ?? 'request';

        MulkStages::create([
            'user_id'     => Auth::id(),
            'customer_id' => $customer->id,
            'stage'       => $stage,
            'start_time'  => now(),
            'end_time'    => now(),
        ]);

        return redirect()->back()->with([
            'message' => 'کڕیارەکە دروستکرا',
        ]);
    }

    public function update(CustomerStoreRequest $request, $id)
    {
        $data = $request->validated();

        $customer = Customers::findOrFail($id);

        // 1️⃣ Update customer columns
        $customer->update([
            'name'      => $data['name'],
            'phone'     => $data['phone'],
            'price_one' => $data['price_one'],
            'price_two' => $data['price_two'],
            'note'      => $data['note'] ?? null,
            'color'     => $data['color'] ?? $customer->color, // ✅ save color, keep old if not sent
        ]);

        // 2️⃣ Sync MULTI locations
        if (isset($data['location_id'])) {
            $customer->locations()->sync($data['location_id']);
        }

        // 3️⃣ Sync MULTI type projects
        if (isset($data['type_project_id'])) {
            $customer->typeProjects()->sync($data['type_project_id']);
        }

        // 4️⃣ Sync mulks (optional)
        if (isset($data['mulks'])) {
            $customer->mulks()->sync($data['mulks']);
        }

        return redirect()->back()->with([
            'message' => 'کڕیارەکە نوێکراوە',
        ]);
    }

    public function show($id)
    {
        $customer = Customers::with([
            'typeProjects:id,name',
            'locations:id,name',
            'mulks.images',
            'mulks.typeProject:id,name',
            'mulks.location:id,name',
        ])->find($id);

        if (! $customer) {
            return redirect()->back()->withErrors([
                'message' => 'کڕیارەکە نەدۆزرایەوە',
            ]);
        }

        return redirect()->back()->with([
            'data' => $customer, // color is included automatically since it's on the model
        ]);
    }

    public function archive(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        $customer = Customers::findOrFail($data['customer_id']);
        $customer->is_archived = ! $customer->is_archived;
        $customer->save();

        return redirect()->back()->with([
            'message' => 'کڕیارەکە بە سەرکەوتوویی ئەرشیف کرا 👌',
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $customer = Customers::where('id', $id)
                ->where(function ($q) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
                })
                ->first();

            if (! $customer) {
                return;
            }

            $customer->locations()->detach();
            $customer->typeProjects()->detach();
            $customer->mulks()->detach();
            $customer->mulkstages()->delete();
            $customer->delete();
        });

        return back()->with('message', 'کڕیارەکە سڕایەوە');
    }
}