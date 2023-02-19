<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddedUserRequest;
use App\Http\Resources\AddedUserResource;
use App\Models\AddedUser;
use App\Models\Country;
use App\Services\Search;
use Illuminate\Http\Request;

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
        if (isset($country['id'])) {
            return AddedUserResource::collection($country->addedUsers);
        }
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
//        AddedUser::all()->map(function ($item){
//            $item->hash = md5($item['last_name'] . $item['first_name'] . $item['middle_name'] . $item['birth_date']->format('d/m/Y'));
//            $item->save();
//            \Storage::disk('local')->append('incomes.txt', ($item['last_name'] . $item['first_name'] . $item['middle_name'] . $item['birth_date']->format('d/m/Y')));
//        });
        $addedUsers = AddedUserResource::collection($this->search->searchFromClients('AddedUser', $request)->unique('hash')->all());
        $blackLists = AddedUserResource::collection($this->search->searchFromClients('BlackList', $request))->map(function ($item) {
            $item['hash'] = md5($item['last_name'] . $item['first_name'] . $item['middle_name'] . $item['birth_date']);
            return $item;
        });
        $results = $addedUsers->merge($blackLists)->toJson();
        $results = collect((array)json_decode($results, true));

        $counted = $addedUsers->merge($blackLists)
            ->countBy(function ($item) {
                return $item['hash'];
            });

        $mk = [];
        $r = $results->reject(function ($items) use ($counted, &$mk) {
            return $counted[$items['hash']] > 1 && $items['black_list'] == false;
        });
//        return $results;
        $counted->map(function ($key, $item) use ($r, &$mk) {
            $r = $r->where('hash', $item);
            $type = implode(',', $r->pluck('type')->toArray());
            $data = $r->first();
            $data['type'] = $type;
            $mk[] = $data;
        });

        return $mk;
    }

    public function countries()
    {
        return Country::select('id', 'name')->get();
    }
}
