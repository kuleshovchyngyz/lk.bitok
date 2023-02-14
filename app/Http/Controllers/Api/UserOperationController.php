<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserOperationRequest;
use App\Http\Resources\UserOperationResource;
use App\Models\AddedUser;
use App\Models\UserOperation;
use App\Services\Search;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserOperationController extends Controller
{
    private Search $search;

    public function __construct()
    {
        $this->authorizeResource(UserOperation::class);
        $this->search = new Search();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AddedUser $addedUser)
    {
        if (isset($addedUser['id'])) {
            if($addedUser->userOperations()->count()==0){
                abort(404);
            }
            return UserOperationResource::collection($addedUser->userOperations);
        }
        return UserOperationResource::collection(UserOperation::with('addedUser')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserOperationRequest $request, AddedUser $addedUser)
    {
        if (isset($addedUser['id'])) {
            return new UserOperationResource($addedUser->userOperations()->create($request->validated()));
        }
        return new UserOperationResource(UserOperation::create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\UserOperation $userOperation
     * @return \Illuminate\Http\Response
     */
    public function show(UserOperation $userOperation)
    {
        return new UserOperationResource($userOperation->loadMissing('addedUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\UserOperation $userOperation
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserOperationRequest $request, UserOperation $userOperation)
    {
        $userOperation->update($request->validated());
        return new UserOperationResource($userOperation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\UserOperation $userOperation
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserOperation $userOperation)
    {
        $userOperation->delete();
        return response()->noContent();
    }

    public function search(Request $request)
    {

        $addedUsers = $this->search->searchFromClients('AddedUser', $request)->pluck('id');

        $userOperation = UserOperation::whereIn('user_id', $addedUsers)
            ->with('addedUser')
            ->when($request->get('from') && $request->get('to'), function ($q) use ($request) {
                $q->whereBetween('operation_date', [
                    Carbon::createFromFormat('d/m/Y', $request->get('from'))->format('Y-m-d'),
                    Carbon::createFromFormat('d/m/Y', $request->get('to'))->addDay()->format('Y-m-d')
                ]);
            })->get();

        return UserOperationResource::collection($userOperation);
    }
}
