<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvocieProjectsRequest;
use App\Http\Requests\InvoiceStoreRequest;
use App\Models\InvoiceProjects;
use App\Models\Invoices;
use App\Models\Projects;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceProjectsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perpage = $request->input('perPage', 25);

        // ✅ handle datefilter
        $dateRange = $request->input('datefilter');

        $dateOne = null;
        $dateTwo = null;

        if (is_array($dateRange) && count($dateRange) === 2 && !empty($dateRange[0]) && !empty($dateRange[1])) {
            $dateOne = Carbon::parse($dateRange[0])->startOfDay();
            $dateTwo = Carbon::parse($dateRange[1])->endOfDay();
        }

        $query = Invoices::with(['user:id,name'])
            ->when($dateOne && $dateTwo, function ($q) use ($dateOne, $dateTwo) {
                $q->whereBetween('created_at', [$dateOne, $dateTwo]); // ✅ only filter if dates exist
            })
            ->orderByDesc('id')
            ->paginate($perpage)
            ->withQueryString();

        $projects = Projects::select('id', 'name')
            ->where('is_archived', 0)
            ->orderByDesc('id')
            ->get();

        return Inertia::render('InvoiceProjects/Index', [
            'data' => $query,
            'filters' => [
                'q' => $q,
                'datefilter' => $dateOne && $dateTwo
                    ? [$dateOne->toDateString(), $dateTwo->toDateString()]
                    : null, // ✅ null if no filter
                'projects' => $projects,
            ]
        ]);
    }

    public function store(InvoiceStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Create invoice
            $invoice = Invoices::create([
                'user_id' => Auth::id(),
                'to' => $request->input('invoices.to'),
                'matter' => $request->input('invoices.matter'),
                'content' => $request->input('invoices.content'),
                'projects_id' => $request->input('invoices.projects_id'),
                'total_price' => 0,
                'datenow'     => \Carbon\Carbon::parse($request->input('invoices.datenow'))
                    ->setTimezone('Asia/Baghdad'),
                            ]);
            $total = 0;

            // Create items
            foreach ($request->input('selectedItems') as $item) {
                $createdItem = $invoice->invoiceItems()->create([
                    'user_id' => Auth::id(),
                    'name' => $item['name'],
                    'measure' => $item['measure'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $total += $createdItem->price;
            }
            $invoice->update(['total_price' => $total]);
        });

        return redirect()->back()->with('message', '📄 فاکتور بە سەرکەوتووی دروستکرا.');
    }
    public function update(InvoiceStoreRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            // Load invoice with current items
            $invoice = Invoices::with('invoiceItems')->findOrFail($id);

            // 1) Update invoice main fields
            $invoice->update([
                'to'         => $request->input('invoices.to'),
                'matter'     => $request->input('invoices.matter'),
                'content'    => $request->input('invoices.content'),
                'projects_id' => $request->input('invoices.projects_id'),
                'datenow'     => \Carbon\Carbon::parse($request->input('invoices.datenow'))
                    ->setTimezone('Asia/Baghdad'),            ]);

            // 2) Normalize incoming items
            $incomingItems = collect($request->input('selectedItems', []))->map(function ($i) {
                return [
                    'id'        => isset($i['id']) && $i['id'] !== '' ? (int) $i['id'] : null,
                    'name'      => $i['name'] ?? null,
                    'measure'   => $i['measure'] ?? null,
                    'unit_price' => $i['unit_price'] ?? 0,
                    'quantity'  => $i['quantity'] ?? 0,
                    'price'     => $i['price'] ?? 0,
                ];
            });

            // 3) Determine which IDs are incoming (existing items)
            $incomingIds = $incomingItems->pluck('id')->filter()->all();

            // 4) Determine current DB ids and compute to-delete
            $existingIds = $invoice->invoiceItems()->pluck('id')->all();
            $toDelete = array_diff($existingIds, $incomingIds);

            if (!empty($toDelete)) {
                $invoice->invoiceItems()->whereIn('id', $toDelete)->delete();
            }

            // 5) Update existing items or create new ones
            foreach ($incomingItems as $item) {
                if ($item['id']) {
                    $model = $invoice->invoiceItems()->where('id', $item['id'])->first();
                    if ($model) {
                        $model->update([
                            'name'       => $item['name'],
                            'measure'    => $item['measure'],
                            'price'      => $item['price'],
                            'unit_price' => $item['unit_price'],
                            'quantity'   => $item['quantity'],
                            'user_id'    => auth()->id(),
                        ]);
                    } else {
                        $invoice->invoiceItems()->create([
                            'name'       => $item['name'],
                            'measure'    => $item['measure'],
                            'price'      => $item['price'],
                            'unit_price' => $item['unit_price'],
                            'quantity'   => $item['quantity'],
                            'user_id'    => auth()->id(),
                        ]);
                    }
                } else {
                    $invoice->invoiceItems()->create([
                        'name'       => $item['name'],
                        'measure'    => $item['measure'],
                        'price'      => $item['price'],
                        'unit_price' => $item['unit_price'],
                        'quantity'   => $item['quantity'],
                        'user_id'    => auth()->id(),
                    ]);
                }
            }

            // 6) Recalculate total price after all changes
            $total = $invoice->invoiceItems()->sum('price');
            $invoice->update(['total_price' => $total]);
        });

        return redirect()->back()->with('message', '✏️ فاکتور بە سەرکەوتووی نوێکرایەوە.');
    }



    public function show($id)
    {
        $invoice = Invoices::with(['invoiceItems', 'user',
            'projects.images'
        ]) // eager load relations
            ->findOrFail($id);

        return redirect()->back()->with([
            'data' => $invoice // Ensure 'data' key matches exactly what is expected in Vue
        ]);
    }
}
