<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddedUserRequest;
use App\Http\Resources\AddedUserResource;
use App\Models\AddedUser;
use App\Models\BlackList;
use App\Models\Country;
use App\Services\Search;
use Illuminate\Http\Request;
use Reliese\Database\Eloquent\Model;

class AddedUserController extends Controller
{
    private Search $search;

    public function __construct()
    {
        $this->authorizeResource(AddedUser::class);
        $this->search = new Search();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Country $country)
    {
        $query = AddedUser::with('country');
    
        if (isset($country['id'])) {
            $query->where('country_id', $country['id']);
        }
        
        $users = $query->paginate(100);

        return AddedUserResource::collection($users);
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
        $addedUsers = $this->search->searchFromClients('AddedUser',$request);
        $blackLists =    $this->search->searchFromClients('BlackList',$request);
        return AddedUserResource::collection($addedUsers)->merge(AddedUserResource::collection($blackLists));
    }

    public function countries()
    {
        return Country::select('id', 'name')->get();
    }
}
