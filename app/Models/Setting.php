<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['high_risk','risk'];

    public function setHighRiskAttribute($value)
    {
        $this->attributes['high_risk'] = Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }
    public function setRiskAttribute($value)
    {
        $this->attributes['risk'] = Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }
}
