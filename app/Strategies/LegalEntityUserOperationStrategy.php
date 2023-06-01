<?php

namespace App\Strategies;

use App\Http\Resources\UserOperationResource;
use App\Models\LegalEntity;
use App\Interfaces\UserOperationStrategy;

class LegalEntityUserOperationStrategy implements UserOperationStrategy
{
    private $legalEntity;

    public function __construct(LegalEntity $legalEntity)
    {
        $this->legalEntity = $legalEntity;
    }

    public function getUserOperations()
    {
        if ($this->legalEntity->userOperations()->count() == 0) {
            abort(404);
        }
        return UserOperationResource::collection($this->legalEntity->userOperations->load('legalEntity'));
    }
}
