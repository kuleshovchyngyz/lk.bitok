<?php

namespace App\Http\Controllers\Api;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\LogResource;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index (Request $request) 
    {
        $this->authorize('viewAny', Log::class);
        
        $query = Log::with('user')->latest();
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }
        
        $logs = $query->paginate(100);
        
        $page = LogResource::collection($logs);

        return response()->json([
            $page->items(),
            ['previousPageUrl' => $page->previousPageUrl(),
            'nextPageUrl' => $page->nextPageUrl(),
            'totalPages' => $page->lastPage(),]
        ]);
    }

    

}
