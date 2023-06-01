<?php

namespace App\Http\Controllers\Api;

use App\Exports\CollectionExport;
use App\Factories\UserOperationStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserOperationRequest;
use App\Http\Resources\UserOperationResource;
use App\Models\AddedUser;
use App\Models\Attachment;
use App\Models\BlackList;
use App\Models\Setting;
use App\Models\UserOperation;
use App\Services\Search;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

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
    public function index(Request $request, $id=null)
    {
        $type = $request->input('type');

        $strategy = UserOperationStrategyFactory::createStrategy($type, $id);

        return $strategy->getUserOperations();



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreUserOperationRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $userOperation = UserOperation::create(Arr::except($request->validated(), ['wallet_photo']));

            $this->checkUserForSanction($userOperation, $request);

            if ($request->has('wallet_photo') && is_array($request['wallet_photo'])) {
                $wallet_photo = $request->file('wallet_photo');
                $this->attach($wallet_photo, $userOperation, 'wallet');
            }

            return new UserOperationResource($userOperation);
        });

    }

    /**
     * @param mixed $userOperation
     */
    public function checkUserForSanction(mixed $userOperation,$request)
    {
        $settings = Setting::select('usd_to_som as usd', 'usdt_to_som as usdt', 'rub_to_som as rub', 'limit')->first()->toArray();
        $currencyRate = 1;
        if($request->has('currency')){
            $currencyRate = $settings[$request->get('currency')] ?? 1;
        }
        if ($request->has('legal_id')){
            $addedUser = $userOperation->legalEntity;
        }else{
            $addedUser = $userOperation->addedUser;
        }


        $country = $addedUser->country;
        $sanction = $country->sanction;
        $addedUser->sanction = (int)$sanction;
        $userOperation->sanction = (int)$sanction;

        if ($sanction < 1) {
            $userOperation->sanction = ((int)100*$settings['limit'] > (int)$userOperation->operation_sum * $currencyRate) ? 0 :1 ;
        }
        $inBlackList = BlackList::whereIn('type', ['pft', 'plpd'])->where('hash', $addedUser->hash)->count();

        if ($inBlackList > 0) {
            $addedUser->sanction = 1;
            $userOperation->sanction = 1;
        }

        if ($country->sanction==2){
            $userOperation->sanction = 2;
            $addedUser->sanction = 2;
        }
        $userOperation->save();
        $addedUser->save();

    }

    public function attach($photos, $user, $type)
    {
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
        return new UserOperationResource($userOperation->loadMissing(['addedUser','legalEntity']));
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
