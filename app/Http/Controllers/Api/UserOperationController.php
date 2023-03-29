<?php

namespace App\Http\Controllers\Api;

use App\Exports\CollectionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserOperationRequest;
use App\Http\Resources\UserOperationResource;
use App\Models\AddedUser;
use App\Models\Attachment;
use App\Models\Setting;
use App\Models\UserOperation;
use App\Services\Search;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
    public function index(Request $request, AddedUser $addedUser)
    {
        if (isset($addedUser['id'])) {
            if ($addedUser->userOperations()->count() == 0) {
                abort(404);
            }
            return UserOperationResource::collection($addedUser->userOperations()->orderBy('operation_date', 'desc')->get());
        }
        if ($request->has('risk')) {
            return UserOperationResource::collection(UserOperation::where('sanction', $request->get('risk'))->orderBy('operation_date', 'desc')->get());
        }
        return UserOperationResource::collection(UserOperation::orderBy('operation_date', 'desc')->with('addedUser')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreUserOperationRequest $request)
    {
        return $this->checkUserForSanction(UserOperation::find($request->user_id)->userOperations()->first());

        if (isset($addedUser['id'])) {
            $userOperation = ($addedUser->userOperations()->create(Arr::except($request->validated(), ['wallet_photo'])));
        } else {
            $userOperation = (UserOperation::create(Arr::except($request->validated(), ['wallet_photo'])));
        }
        $this->checkUserForSanction($userOperation);

        if ($request->has('wallet_photo') && is_array($request['wallet_photo'])) {
            $wallet_photo = $request->file('wallet_photo');
            $this->attach($wallet_photo, $userOperation, 'wallet');
        }
        return new UserOperationResource($userOperation);
    }

    /**
     * @param mixed $userOperation
     */
    public function checkUserForSanction(mixed $userOperation)
    {
        $settings = Setting::select('usd_to_som as usd', 'usdt_to_som as usdt', 'rub_to_som as rub', 'limit')->first()->toArray();
        $settings = array_merge($settings, ['som' => 1]);
        $currentMonth = Carbon::now()->month;
        $addedUser = $userOperation->addedUser;
        $totals = $addedUser->userOperations()
            ->whereMonth('operation_date', $currentMonth)
            ->groupBy('currency')
            ->selectRaw('currency, SUM(operation_sum) as total_sum')
            ->get();
        $sum = 0;
        foreach ($totals as $total) {
            if (in_array($total->currency, $settings)) {
                $sum += $settings[$total->currency] * $total->total_sum;
                continue;
            }
            $sum += $total->total_sum;
        }
        return $sum;
        $check = $settings['limit'] < number_format($sum / 100, 2);
        if ($addedUser->sanction==0 && $check){
            $addedUser->sanction = 1;
            $addedUser->save();
        }

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
