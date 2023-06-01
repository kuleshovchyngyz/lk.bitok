<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function addedUsers()
    {
        return $this->hasMany(AddedUser::class);
    }
    public function legalEntities()
    {
        return $this->hasMany(LegalEntity::class);
    }
}
