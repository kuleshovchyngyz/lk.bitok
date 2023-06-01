<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLegalEntityRequest;
use App\Http\Resources\LegalResource;
use App\Models\Country;
use App\Models\LegalEntity;
use App\Traits\AttachPhotosTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LegalEntityController extends Controller
{
    use AttachPhotosTrait;

    public function index(Request $request, Country $country)
    {
        if (isset($country['id'])) {
            return LegalResource::collection($country->legalEntities);
        }
        if ($request->has('risk')) {
            return LegalResource::collection(LegalEntity::with(['country'])->where('sanction', $request->get('risk'))->orderBy('created_at', 'desc')->get());
        }
        return LegalResource::collection(LegalEntity::with(['country'])->orderBy('created_at', 'desc')->get());
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
}
