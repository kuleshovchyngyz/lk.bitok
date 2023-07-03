<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLegalEntityRequest;
use App\Http\Resources\AddedUserResource;
use App\Http\Resources\LegalResource;
use App\Models\AddedUser;
use App\Models\Country;
use App\Models\LegalEntity;
use App\Services\Search;
use App\Traits\AttachPhotosTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LegalEntityController extends Controller
{
    use AttachPhotosTrait;

    public function __construct(private Search $search)
    {
        $this->search = new Search();
    }

    public function index(Request $request, Country $country)
    {
        $limit = 100;
    
        if (isset($country['id'])) {
            $legalEntities = $country->addedUsers()->paginate($limit);
        } elseif ($request->has('risk')) {
            $legalEntities = LegalEntity::with(['country'])
                ->where('sanction', $request->get('risk'))
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        } else {
            $legalEntities = LegalEntity::with(['country'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }
        
        $page = LegalResource::collection($legalEntities);
        
        return response()->json([
            $page->items(),
            ['previousPageUrl' => $page->previousPageUrl(),
            'nextPageUrl' => $page->nextPageUrl(),
            'totalPages' => $page->lastPage(),]
        ]);
    }

    public function store(StoreLegalEntityRequest $request)
    {
        $legalEntity = DB::transaction(function () use ($request) {
            $legalEntity = LegalEntity::create(
                Arr::except($request->validated(), ['cv_photo', 'cv_photo_bf', 'certificate_photo', 'licence_photo', 'permit_photo', 'passport_photo'])
            );
            $this->attachPhotos($request, $legalEntity);
            return $legalEntity;
        });

        return new LegalResource($legalEntity);
    }


    public function show(LegalEntity $legalEntity)
    {
        return new LegalResource($legalEntity->loadMissing('userOperations'));
    }

    public function update(StoreLegalEntityRequest $request, LegalEntity $legalEntity)
    {
        $legalEntity = DB::transaction(function () use ($request, $legalEntity) {
            $legalEntity->update(
                Arr::except($request->validated(), ['cv_photo', 'cv_photo_bf', 'certificate_photo', 'licence_photo', 'permit_photo', 'passport_photo'])
            );
            $this->attachPhotos($request, $legalEntity);
            return $legalEntity;
        });
        return new LegalResource($legalEntity);
    }


    public function destroy(LegalEntity $legalEntity)
    {
        $legalEntity->delete();
        return response()->noContent();
    }

    public function upload(StoreLegalEntityRequest $request, LegalEntity $legalEntity)
    {
        $this->attachPhotos($request, $legalEntity);
        return new LegalResource($legalEntity);
    }
    public function search(Request $request)
    {

            return $whiteListUsers = LegalResource::collection($this->search->searchFromClients('LegalEntity', $request)->unique('hash')->all());
            $whiteListUsers = $this->filterByDates($request, $whiteListUsers);
            if ($request->get('name') == null && $request->get('birth_date') == null) {
                return $whiteListUsers;
            }
            return $whiteListUsers;


            list($blackLists, $results) = $this->getBlackedListUsers($request, $whiteListUsers);

            return $this->mergeBothUsers($whiteListUsers, $blackLists, $results);

//        } catch (\Exception $e) {
//            return response()->json([
//                'error' => $e->getMessage()
//            ], 500); // HTTP status code 500 for Internal Server Error
//        }
    }
    public function filterByDates(Request $request, AnonymousResourceCollection $addedUsers)
    {
        if ($request->has('date1') && $request->has('date2')) {
            $startDate = $this->parseDateString($request->date1);
            $endDate = $this->parseDateString($request->date2);
            $addedUsers = $addedUsers->filter(function ($item) use ($startDate, $endDate) {
                return $item['created_at'] >= $startDate && $item['created_at'] <= $endDate;
            });
        }
        return $addedUsers->sortByDesc('created_at');
    }
    public function parseDateString($date_string)
    {
        if (str_contains($date_string, ':')) {
            return Carbon::createFromFormat('d/m/Y H:i', $date_string)->format('Y-m-d H:i');
        } else {
            return Carbon::createFromFormat('d/m/Y', $date_string)->startOfDay()->format('Y-m-d H:i');
        }
    }
    public function getBlackedListUsers(Request $request, $addedUsers): array
    {
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
