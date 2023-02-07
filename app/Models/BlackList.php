<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlackList extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['birth_date'];
    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }


}
