<?php

namespace App\Factories;

use App\Interfaces\UserOperationStrategy;
use App\Models\AddedUser;
use App\Models\LegalEntity;
use App\Strategies\AddedUserUserOperationStrategy;
use App\Strategies\AllUserOperationStrategy;
use App\Strategies\LegalEntityUserOperationStrategy;
use Illuminate\Http\Request;

class UserOperationStrategyFactory
{
    public static function createStrategy($type, $id = null): ?UserOperationStrategy
    {
        if ($id == null) {
            return new AllUserOperationStrategy();
        }
        if ($type === 'legal') {
            $legalEntity = LegalEntity::findOrFail($id);
            return new LegalEntityUserOperationStrategy($legalEntity);
        }
        $addedUser = AddedUser::findOrFail($id);
        return new AddedUserUserOperationStrategy($addedUser);

    }
}

