<?php

namespace App\Http\Controllers;

use App\Models\Mulks;
use App\Models\Locations;
use App\Models\TypeProject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MulksDashboardController extends Controller
{
    public function index(Request $request)
    {
        $datefilter = $request->input('datefilter');

        // ── Build base query: non-archived mulks only ──────────────────────────
        $query = Mulks::query()
->where(function ($q) {
        $q->where('is_archived', false)
          ->orWhereNull('is_archived'); // ✅ NULL ش cover دەکات
    })            ->with([
                'location',
                'typeProject',
                'images',

                // Customers with their latest stage info
                'customers' => function ($q) {
                    $q->select(
                        'customers.id',
                        'customers.name',
                        'customers.color',
                        'customers.price_one',
                        'customers.price_two',
                    )
                    ->with([
                        'mulkstages' => function ($sq) {
                            $sq->select(
                                'id',
                                'mulk_id',
                                'customer_id',
                                'stage',
                                'start_time',
                                'end_time',
                                'note',
                                'backed_from_pricing',
                            )
                            ->orderByDesc('created_at');
                        }
                    ]);
                },
            ])
            ->withCount(['customers as customer_count']); // ✅ alias so column is named customer_count

        // ── Date filter ───────────────────────────────────────────────────────
        if (!empty($datefilter) && is_array($datefilter) && count($datefilter) === 2) {
            $query->whereBetween('created_at', [$datefilter[0], $datefilter[1] . ' 23:59:59']);
        }

        $mulks = $query->orderByDesc('customer_count')->get(); // ✅ matches alias

        // ── Annotate each customer with their current_stage ───────────────────
        $mulks->each(function ($mulk) {
            $mulk->customers->each(function ($customer) use ($mulk) {
                // Find the latest stage for this customer scoped to this mulk
                $latestStage = $customer->mulkstages
                    ->where('mulk_id', $mulk->id)
                    ->first(); // already sorted desc

                // Fallback to any latest stage if no mulk-scoped one found
                if (!$latestStage) {
                    $latestStage = $customer->mulkstages->first();
                }

                $customer->current_stage       = $latestStage?->stage ?? 'request';
                $customer->stage_start_time    = $latestStage?->start_time;
                $customer->stage_end_time      = $latestStage?->end_time;
                $customer->stage_note          = $latestStage?->note;
                $customer->backed_from_pricing = $latestStage?->backed_from_pricing ?? false;

                // Remove raw mulkstages from response to keep payload lean
                unset($customer->mulkstages);
            });
        });

        return Inertia::render('Mulksdashboard', [
            'data' => [
                'mulks'        => $mulks,
                'locations'    => Locations::orderBy('name')->get(['id', 'name']),
                'typeProjects' => TypeProject::orderBy('name')->get(['id', 'name']),
                'datefilter'   => $datefilter,
            ],
        ]);
    }
}