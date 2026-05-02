<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceAsaishRequest;
use App\Models\InvoicesAsaish;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoicesAsaishController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 25);

        // ✅ handle datefilter
        $dateRange = $request->input('datefilter');

        $dateOne = null;
        $dateTwo = null;

        if (is_array($dateRange) && count($dateRange) === 2 && ! empty($dateRange[0]) && ! empty($dateRange[1])) {
            $dateOne = Carbon::parse($dateRange[0])->startOfDay();
            $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        }

        $query = InvoicesAsaish::with(['user:id,name'])
            ->when($dateOne && $dateTwo, function ($q) use ($dateOne, $dateTwo) {
                $q->whereBetween('created_at', [$dateOne, $dateTwo]); // ✅ only filter if dates exist
            })
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString();

        return Inertia::render('InvoicesAsaish/Index', [
            'data' => $query,
            'filters' => [
                'q' => $q,
                'datefilter' => $dateOne && $dateTwo
                    ? [$dateOne->toDateString(), $dateTwo->toDateString()]
                    : null, // ✅ null if no filter
                // 'projects' => $projects,
            ],
        ]);
    }

    public function store(InvoiceAsaishRequest $request)
    {
        DB::transaction(function () use ($request) {
            InvoicesAsaish::create([
                'user_id' => auth()->id(), // 👈 HERE
                ...$request->validated()['invoices'],
            ]);
        });

        return back()->with('message', 'دەرخستە بە سەرکەوتوویی دروستکرا');
    }
    public function update(InvoiceAsaishRequest $request, InvoicesAsaish $invoiceAsaish)
{
    $invoiceAsaish->update(
        $request->validated()['invoices']
    );

    return back()->with('message', 'دەرخستە بە سەرکەوتوویی نوێکرایەوە');
}

public function show($id)
    {
        $invoice = InvoicesAsaish::with([ 'user'  ]) // eager load relations
            ->findOrFail($id);

        return redirect()->back()->with([
            'data' => $invoice // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
}
