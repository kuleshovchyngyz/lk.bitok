<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserOperationRequest;
use App\Http\Resources\UserOperationResource;
use App\Models\UserOperation;
use Illuminate\Http\Request;

class UserOperationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserOperation::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserOperationResource::collection(UserOperation::with('addedUser')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserOperationRequest $request)
    {
        return new UserOperationResource(UserOperation::create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserOperation  $userOperation
     * @return \Illuminate\Http\Response
     */
    public function show(UserOperation $userOperation)
    {
        return new UserOperationResource($userOperation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserOperation  $userOperation
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
     * @param  \App\Models\UserOperation  $userOperation
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserOperation $userOperation)
    {
        $userOperation->delete();
        return response()->noContent();
    }
}
