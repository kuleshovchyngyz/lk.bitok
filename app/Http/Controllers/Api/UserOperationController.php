<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Setting;
use App\Services\Search;
use App\Models\BlackList;
use App\Models\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\UserOperation;
use App\Services\ActionLogger;
use App\Exports\CollectionExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserOperationResource;
use App\Factories\UserOperationStrategyFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\StoreUserOperationRequest;

class UserOperationController extends Controller
{
    private Search $search;

    public function __construct()
    {
        // $this->authorizeResource(UserOperation::class);
        $this->search = new Search();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request, $id = null)
    {
        $this->authorize('viewAny', UserOperation::class);

        $type = $request->input('type');
        $strategy = UserOperationStrategyFactory::createStrategy($type, $id);

        $data = $strategy->getUserOperations();

        // Get the pagination limit from the request, default to 10 if not provided
        $perPage = 200;

        // Manually create a LengthAwarePaginator instance
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $slicedData = $data->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator($slicedData, $data->count(), $perPage, $currentPage);

        return response()->json([
            $paginator->items(),
            ['previousPageUrl' => $paginator->previousPageUrl(),
            'nextPageUrl' => $paginator->nextPageUrl(),
            'totalPages' => $paginator->lastPage(),]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreUserOperationRequest $request)
    {
        $this->authorize('create', UserOperation::class);

        return DB::transaction(function () use ($request) {
            $userOperation = UserOperation::create(Arr::except($request->validated(), ['wallet_photo']));

            $this->checkUserForSanction($userOperation, $request);

            if ($request->has('wallet_photo') && is_array($request['wallet_photo'])) {
                $wallet_photo = $request->file('wallet_photo');
                $this->attach($wallet_photo, $userOperation, 'wallet');
            }

            // sending this event to logs in database
            ActionLogger::log($userOperation, 'UserOperationController', 'store');
            // end of sending event

            return new UserOperationResource($userOperation);
        });

    }

    /**
     * @param mixed $userOperation
     */
    public function checkUserForSanction(mixed $userOperation, $request)
    {
        $this->authorize('viewAny', UserOperation::class);

        $settings = Setting::select('usd_to_som as usd', 'usdt_to_som as usdt', 'rub_to_som as rub', 'limit')->first()->toArray();
        $currencyRate = 1;
        if ($request->has('currency')) {
            $currencyRate = $settings[$request->get('currency')] ?? 1;
        }
        if ($request->has('legal_id')) {
            $addedUser = $userOperation->legalEntity;
        } else {
            $addedUser = $userOperation->addedUser;
        }


        $country = $addedUser->country;
        $sanction = $country->sanction;
        $addedUser->sanction = (int)$sanction;
        $userOperation->sanction = (int)$sanction;

        if ($sanction < 1) {
            $userOperation->sanction = ((int)100 * $settings['limit'] > (int)$userOperation->operation_sum * $currencyRate) ? 0 : 1;
        }
        $inBlackList = BlackList::whereIn('type', ['pft', 'plpd'])->where('hash', $addedUser->hash)->count();

        if ($inBlackList > 0) {
            $addedUser->sanction = 1;
            $userOperation->sanction = 1;
        }

        if ($country->sanction == 2) {
            $userOperation->sanction = 2;
            $addedUser->sanction = 2;
        }
        $userOperation->save();
        $addedUser->save();

    }

    public function attach($photos, $user, $type)
    {
        $this->authorize('create', UserOperation::class);

        $thumbnail_url = null;
        foreach ($photos as $key => $singleFile) {

            $fileName = uniqid() . $singleFile->getClientOriginalName();
            Storage::disk('uploads')->put($fileName, file_get_contents($singleFile));
            if (in_array(strtolower($singleFile->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'bmp'])) {
                Image::make($singleFile->path())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads') . '/thumbnails/' . $fileName);
                $thumbnail_url = Storage::disk('uploads')->url('thumbnails/' . $fileName);
            }

            $attachments = new Attachment([
                'type' => $type,
                'url' => Storage::disk('uploads')->url($fileName),
                'thumbnail_url' => $thumbnail_url
            ]);
            $user->attachments()->save($attachments);
        }

    }

    public function range(Request $request)
    {
        $this->authorize('create', UserOperation::class);

        $date1 = Carbon::createFromFormat('d/m/Y H:i', $request->date1)->format('Y-m-d H:i');
        $date2 = Carbon::createFromFormat('d/m/Y H:i', $request->date2)->format('Y-m-d H:i');
        $time1 = Carbon::createFromFormat('d/m/Y H:i', $request->date1)->format('Y-m-d');
        $time2 = Carbon::createFromFormat('d/m/Y H:i', $request->date2)->format('Y-m-d');

        $records = UserOperationResource::collection(UserOperation::with('addedUser')->whereBetween('operation_date', [$date1, $date2])->get());
        $path = 'public/exports/' . $time1 . '---' . $time2 . '.xlsx'; // Set the storage path for the Excel file
        Excel::store(new CollectionExport($records), $path);
        return URL::to('/') . Storage::url($path);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\UserOperation $userOperation
     * @return \Illuminate\Http\Response
     */
    public function show(UserOperation $userOperation)
    {
        $this->authorize('viewAny', $userOperation);

        return new UserOperationResource($userOperation->loadMissing(['addedUser', 'legalEntity']));
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
        $this->authorize('update', $userOperation);

        $userOperation->update($request->validated());

        // sending this event to logs in database
        ActionLogger::log($userOperation, 'UserOperationController', 'update');
        // end of sending event

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
        $this->authorize('delete', $userOperation);

        $userOperation->delete();

        // sending this event to logs in database
        ActionLogger::log($userOperation, 'UserOperationController', 'destroy');
        // end of sending event

        return response()->noContent();
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', UserOperation::class);

        $userOperation = UserOperation::with(['addedUser', 'legalEntity'])
            ->when($request->get('type') == 'user' || $request->get('type') == null, function ($q) use ($request) {
                $addedUsers = $this->search->searchFromClients('AddedUser', $request)->pluck('id');
                $q->whereIn('user_id', $addedUsers);
            })
            ->when($request->get('type') == 'legal', function ($q) use ($request) {
                $addedUsers = $this->search->searchFromClients('LegalEntity', $request)->pluck('id');
                $q->whereIn('legal_id', $addedUsers);
            })
            ->when($request->get('from') && $request->get('to'), function ($q) use ($request) {
                $q->whereBetween('operation_date', [
                    Carbon::createFromFormat('d/m/Y', $request->get('from'))->format('Y-m-d'),
                    Carbon::createFromFormat('d/m/Y', $request->get('to'))->addDay()->format('Y-m-d')
                ]);
            })
            ->get()
            ->filter(function ($item) {
                if ($item->user_id !== null && $item->addedUser==null){
                    return false;
                }
                if ($item->legal_id !== null && $item->legalEntity==null){
                    return false;
                }
                return true;

            });

            $perPage = 200; // Number of items per page
            $currentPage = Paginator::resolveCurrentPage('page');
            $sliced = $userOperation->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $pagination = new LengthAwarePaginator(
                $sliced,
                $userOperation->count(),
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
    }
}
