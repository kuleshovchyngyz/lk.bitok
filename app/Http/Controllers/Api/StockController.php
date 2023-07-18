<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\LegalEntity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Http\Resources\StockResource;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stock::latest()->paginate(100);

        $page = StockResource::collection($stocks);

        return response()->json([
            $page->items(),
            ['previousPageUrl' => $page->previousPageUrl(),
            'nextPageUrl' => $page->nextPageUrl(),
            'totalPages' => $page->lastPage(),]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStockRequest $request)
    {
        $this->authorize('create',Stock::class);

        $country = Stock::create($request->validated());
        return response()->json(new StockResource($country), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new StockResource(Stock::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $this->authorize('update', $stock);

        $stock->update($request->validated());

        return new StockResource($stock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $this->authorize('delete', $stock);

        $stock->delete();

        return response()->noContent();
    }


    // Search in legal-entities and stocks
    public function search(Request $request)
    {
        $name = $request->get('name');
        $address = $request->get('address');
        
        if ($name && $address) 
        {
            $foundStocks = Stock::where('name', 'like', '%'.$name.'%')
                ->orWhere('address', 'like', '%'.$address.'%')
                ->get();

            $foundLegals = LegalEntity::where('name', 'like', '%'.$name.'%')
                ->orWhere('address', 'like', '%'.$address.'%')
                ->get();
        } 
        elseif ($name && !$address) 
        {
            $foundStocks = Stock::where('name', 'like', '%'.$name.'%')->get();

            $foundLegals = LegalEntity::where('name', 'like', '%'.$name.'%')->get();
        } 
        else 
        {
            $foundStocks = Stock::where('address', 'like', '%'.$address.'%')->get();

            $foundLegals = LegalEntity::where('address', 'like', '%'.$address.'%')->get();
        }

        $allFound = $foundStocks->concat($foundLegals);

        $perPage = 100;
        $currentPage = Paginator::resolveCurrentPage('page');

        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = $allFound->slice($offset, $perPage)->values();

        $paginatedResults = new LengthAwarePaginator(
            $paginatedItems,
            $allFound->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return response()->json([
            $paginatedItems,
            [
                'previousPageUrl' => $paginatedResults->previousPageUrl(),
                'nextPageUrl' => $paginatedResults->nextPageUrl(),
                'totalPages' => $paginatedResults->lastPage(),
            ],
        ]);
    }
}
