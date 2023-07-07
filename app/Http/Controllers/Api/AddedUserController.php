<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Country;
use App\Services\Search;
use App\Models\AddedUser;
use App\Models\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Events\LogActionEvent;
use App\Services\ActionLogger;
use App\Traits\AttachPhotosTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CountryResource;
use App\Http\Resources\AddedUserResource;
use App\Http\Requests\StoreAddedUserRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AddedUserController extends Controller
{
    use AttachPhotosTrait;
    public function __construct(private Search $search)
    {
        // $this->authorizeResource(AddedUser::class);
        $this->search = new Search();
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */

    public function index(Request $request, Country $country)
    {
        $this->authorize('viewAny', AddedUser::class);
        
        $limit = 100;
    
        if (isset($country['id'])) {
            $addedUsers = $country->addedUsers()->paginate($limit);
        } elseif ($request->has('risk')) {
            $addedUsers = AddedUser::with(['country'])
                ->where('sanction', $request->get('risk'))
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        } else {
            $addedUsers = AddedUser::with(['country'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }
        
        $page = AddedUserResource::collection($addedUsers);

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddedUserRequest $request)
    {
        $this->authorize('create',AddedUser::class);

        $user = DB::transaction(function () use ($request) {
            $user = AddedUser::create(
                Arr::except($request->validated(), ['passport_photo', 'cv_photo'])
            );
            $this->attachPhotos($request, $user);
            return $user;
        });

        // sending this event to logs in database
        ActionLogger::log($user, 'AddedUserController', 'store');
        // end of sending event

        return new AddedUserResource($user);
    }



    public function delete(Attachment $attachment)
    {
        $this->authorize('delete',AddedUser::class);

        $attachment->delete();
        return response()->json([], 204);
    }

    public function upload(Request $request, AddedUser $addedUser)
    {
        $this->authorize('create',$addedUser);

        $request->validate([
            'passport_photo.*' => 'image',
            'cv_photo.*' => 'image',
        ]);

        if ($request->has('passport_photo') && is_array($request['passport_photo'])) {
            $passport_photo = $request->file('passport_photo');
            $this->attach($passport_photo, $addedUser, 'passport');
        }
        if ($request->has('cv_photo') && is_array($request['cv_photo'])) {
            $cv_photo = $request->file('cv_photo');
            $this->attach($cv_photo, $addedUser, 'cv');
        }


        return new AddedUserResource($addedUser);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Http\Response
     */
    public function show(AddedUser $addedUser)
    {
        $this->authorize('view',$addedUser);

        return new AddedUserResource($addedUser->loadMissing('userOperations'));
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
        $this->authorize('update',$addedUser);

        $addedUser->update(
            Arr::except($request->validated(), ['passport_photo', 'cv_photo'])
        );
        $hash = md5(($addedUser['last_name'] ?? null) . ($addedUser['first_name'] ?? null) . ($addedUser['middle_name'] ?? null) . ($addedUser['birth_date'] ?? null));
        $addedUser->hash = $hash;
        $addedUser->save();
        $this->attachPhotos($request, $addedUser);

        // sending this event to logs in database
        ActionLogger::log($addedUser, 'AddedUserController', 'update');
        // end of sending event

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
        $this->authorize('delete',$addedUser);

        $addedUser->delete();

        // sending this event to logs in database
        ActionLogger::log($addedUser, 'AddedUserController', 'destroy');
        // end of sending event

        return response()->noContent();
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', AddedUser::class);
        
        if (!$request->all()) {

            $country = Country::all();

            $limit = 100;
    
            if (isset($country['id'])) {
                $addedUsers = $country->addedUsers()->paginate($limit);
            } elseif ($request->has('risk')) {
                $addedUsers = AddedUser::with(['country'])
                    ->where('sanction', $request->get('risk'))
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit);
            } else {
                $addedUsers = AddedUser::with(['country'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit);
            }
            
            $page = AddedUserResource::collection($addedUsers);

            return response()->json([
                $page->items(),
                ['previousPageUrl' => $page->previousPageUrl(),
                'nextPageUrl' => $page->nextPageUrl(),
                'totalPages' => $page->lastPage(),]
            ]); 
        }

        try {
            $whiteListUsers = AddedUserResource::collection($this->search->searchFromClients('AddedUser', $request)->unique('hash')->all());
            
            if ($request->get('name') == null && $request->get('birth_date') == null) {
                return $whiteListUsers->paginate(100);
            }
            
            $whiteListUsers = $this->filterByDates($request, $whiteListUsers);
            list($blackLists, $results) = $this->getBlackedListUsers($request, $whiteListUsers);
            
            $mergedUsers = $this->mergeBothUsers($whiteListUsers, $blackLists, $results);
            $mergedUsers = new Collection($mergedUsers); // Convert array to collection
            
            $perPage = 100; // Number of items per page
            $currentPage = Paginator::resolveCurrentPage('page');
            $sliced = $mergedUsers->slice(($currentPage - 1) * $perPage, $perPage);
            
            $pagination = new LengthAwarePaginator(
                $sliced,
                $mergedUsers->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );

            return response()->json([
                $pagination->items(),
                ['previousPageUrl' => $pagination->previousPageUrl(),
                'nextPageUrl' => $pagination->nextPageUrl(),
                'totalPages' => $pagination->lastPage(),]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500); // HTTP status code 500 for Internal Server Error
        }
    }

    public function parseDateString($date_string)
    {
        $this->authorize('create', AddedUser::class);

        if (str_contains($date_string, ':')) {
            return Carbon::createFromFormat('d/m/Y H:i', $date_string)->format('Y-m-d H:i');
        } else {
            return Carbon::createFromFormat('d/m/Y', $date_string)->startOfDay()->format('Y-m-d H:i');
        }
    }

    public function countries()
    {
        $this->authorize('viewAny', AddedUser::class);

        return CountryResource::collection(Country::all());
    }

    public function filterByDates(Request $request, AnonymousResourceCollection $addedUsers)
    {
        $this->authorize('viewAny', AddedUser::class);

        if ($request->has('date1') && $request->has('date2')) {
            $startDate = $this->parseDateString($request->date1);
            $endDate = $this->parseDateString($request->date2);
            $addedUsers = $addedUsers->filter(function ($item) use ($startDate, $endDate) {
                return $item['created_at'] >= $startDate && $item['created_at'] <= $endDate;
            });
        }
        return $addedUsers->sortByDesc('created_at');
    }

    public function getBlackedListUsers(Request $request, $addedUsers)
    {
        $this->authorize('viewAny', AddedUser::class);

        $blackLists = AddedUserResource::collection($this->search->searchFromClients('BlackList', $request))->map(function ($item) {
            $item['hash'] = md5($item['last_name'] . $item['first_name'] . $item['middle_name'] . ((isset($item['birth_date'])) ? $item['birth_date']->format('d/m/Y') : ''));
//                \Storage::disk('local')->append('incomess.txt', ($item['last_name'] . $item['first_name'] . $item['middle_name'] . ((isset($item['birth_date'])) ? $item['birth_date']->format('d/m/Y') : '')));
            return $item;
        });
        $results = $addedUsers->merge($blackLists)->toJson();
        $results = collect((array)json_decode($results, true));
        return array($blackLists, $results);
    }

    /**
     * @param $whiteListUsers
     * @param mixed $blackLists
     * @param mixed $results
     * @return array
     */
    public function mergeBothUsers($whiteListUsers, mixed $blackLists, mixed $results): array
    {
        $this->authorize('create', AddedUser::class);

        $counted = $whiteListUsers->merge($blackLists)
            ->countBy(function ($item) {
                return $item['hash'];
            });

        $mk = [];
        $r = $results->reject(function ($items) use ($counted, &$mk) {
            return $counted[$items['hash']] > 1 && $items['black_list'] == false;
        });

        $counted->map(function ($key, $item) use ($r, &$mk) {
            $r = $r->where('hash', $item);
            $type = implode(',', $r->pluck('type')->toArray());
            $data = $r->first();
            $data['type'] = $type;
            $mk[] = $data;
        });
        return $mk;
    }
}
