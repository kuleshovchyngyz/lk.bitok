<?php

namespace App\Strategies;

use App\Http\Resources\UserOperationResource;
use App\Interfaces\UserOperationStrategy;
use App\Models\UserOperation;

class AllUserOperationStrategy implements UserOperationStrategy
{

    public function getUserOperations()
    {
        $query = UserOperation::orderBy('operation_date', 'desc')->take(1000);

        if (request()->has('risk')) {
            $query->where('sanction', request()->get('risk'));
        }
        if (request()->has('type') && request()->get('type') == 'legal') {
            $query->where('legal_id', '!=',null)->where('user_id',null);
        }
        if (request()->has('type') && request()->get('type') == 'user') {
            $query->where('user_id', '!=' ,null)->where('legal_id',null);
        }

        return UserOperationResource::collection( $query->with(['addedUser','legalEntity'])->get());
    }
}
