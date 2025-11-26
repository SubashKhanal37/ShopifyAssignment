<?php

namespace App\Http\Controllers;

use App\Jobs\SyncOrdersJob;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function sync(Request $request)
    {
        SyncOrdersJob::dispatchSync($request->user());

        return back();
    }
}


