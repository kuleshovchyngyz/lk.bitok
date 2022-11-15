<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddedUserRequest;
use App\Http\Resources\AddedUserResource;
use App\Models\AddedUser;
use App\Models\Country;
use Illuminate\Http\Request;

class AddedUserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AddedUser::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AddedUserResource::collection(AddedUser::with('country')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddedUserRequest $request)
    {
        return new AddedUserResource(AddedUser::create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Http\Response
     */
    public function show(AddedUser $addedUser)
    {
        return new AddedUserResource($addedUser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAddedUserRequest $request, AddedUser $addedUser)
    {

        $addedUser->update($request->validated());
        return new AddedUserResource($addedUser);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddedUser $addedUser)
    {

        $addedUser->delete();
        return response()->noContent();
    }

    public function search(Request $request)
    {
        return AddedUser::when($request->has('pass_num_inn'), function ($q) use ($request) {
            return $q->where('pass_num_inn', 'like', '%' . $request->pass_num_inn . '%');
        })->when($request->has('name'), function ($q) use ($request) {
            return $q->orWhere('last_name', 'like', '%' . $request->name . '%')
                ->orWhere('first_name', 'like', '%' . $request->name . '%')
                ->orWhere('middle_name', 'like', '%' . $request->name . '%');
        })->get();
    }

    public function countries()
    {
        return Country::select('id', 'name')->get();
    }
}
