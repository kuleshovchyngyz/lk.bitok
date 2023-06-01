<?php

namespace App\Strategies;

use App\Http\Resources\UserOperationResource;
use App\Interfaces\UserOperationStrategy;
use App\Models\AddedUser;
use App\Models\UserOperation;

class AddedUserUserOperationStrategy implements UserOperationStrategy
{
    private $addedUser;

    public function __construct(AddedUser $addedUser)
    {
        $this->addedUser = $addedUser;
    }

    public function getUserOperations()
    {
        if ($this->addedUser->userOperations()->count() == 0) {
            abort(404);
        }
        return UserOperationResource::collection($this->addedUser->userOperations()->orderBy('operation_date', 'desc')->with('addedUser')->get());

    }
}
