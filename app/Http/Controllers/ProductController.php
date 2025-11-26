<?php

namespace App\Http\Controllers;

use App\Jobs\SyncProductsJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->products();
        
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->input('search')}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $products = $query->latest()->paginate(10)->withQueryString();
        
        return Inertia::render('Products', [
            'products' => $products,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function sync(Request $request)
    {
        // Run sync synchronously so that when we redirect back, the dashboard
        // (or products page) sees the up-to-date counts immediately.
        SyncProductsJob::dispatchSync($request->user());

        return back();
    }
}
