<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $shop = $request->user();

        $lastSync = collect([
            $shop->products_last_synced_at,
            $shop->collections_last_synced_at,
            $shop->orders_last_synced_at,
        ])->filter()->max();
        
        return Inertia::render('Dashboard', [
            'stats' => [
                'productsCount' => $shop->products()->count(),
                'collectionsCount' => $shop->collections()->count(),
                'ordersCount' => $shop->orders()->count(),
                'lastSync' => $lastSync ? $lastSync->toIso8601String() : null,
            ]
        ]);
    }
}
