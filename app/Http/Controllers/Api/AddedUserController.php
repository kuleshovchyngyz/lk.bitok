<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddedUserRequest;
use App\Http\Resources\AddedUserResource;
use App\Http\Resources\CountryResource;
use App\Models\AddedUser;
use App\Models\Country;
use App\Services\Search;
use Carbon\Carbon;
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
        try {
//        AddedUser::all()->map(function ($item){
//            $item->hash = md5($item['last_name'] . $item['first_name'] . $item['middle_name'] . $item['birth_date']->format('d/m/Y'));
//            $item->save();

//        });
            $addedUsers = AddedUserResource::collection($this->search->searchFromClients('AddedUser', $request)->unique('hash')->all());

            if ($request->has('date1') && $request->has('date2')) {
                $startDate = $this->parseDateString($request->date1);
                $endDate = $this->parseDateString($request->date2);
                $addedUsers = $addedUsers->filter(function ($item) use ($startDate, $endDate) {
                    return $item['created_at'] >= $startDate && $item['created_at'] <= $endDate;
                });
            }
            $addedUsers = $addedUsers->sortByDesc('created_at');

            $blackLists = AddedUserResource::collection($this->search->searchFromClients('BlackList', $request))->map(function ($item) {
                $item['hash'] = md5($item['last_name'] . $item['first_name'] . $item['middle_name'] . ((isset($item['birth_date'])) ? $item['birth_date']->format('d/m/Y') : ''));
//                \Storage::disk('local')->append('incomess.txt', ($item['last_name'] . $item['first_name'] . $item['middle_name'] . ((isset($item['birth_date'])) ? $item['birth_date']->format('d/m/Y') : '')));
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
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return $mk;
    }

    public function parseDateString($date_string)
    {
        if (str_contains($date_string, ':')) {
            return Carbon::createFromFormat('d/m/Y H:i', $date_string)->format('Y-m-d H:i');
        } else {
            return Carbon::createFromFormat('d/m/Y', $date_string)->startOfDay()->format('Y-m-d H:i');
        }
    }

    public function countries()
    {
        return CountryResource::collection(Country::all());
    }
}
