<?php

namespace App\Http\Controllers;

use App\Jobs\SyncCollectionsJob;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->collections()->paginate(10);
    }

    public function sync(Request $request)
    {
        // Run sync synchronously so that redirected pages see fresh data.
        SyncCollectionsJob::dispatchSync($request->user());

        return back();
    }
}
