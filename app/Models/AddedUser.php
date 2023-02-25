<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddedUser extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['birth_date','verification_date'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->userOperations()->delete();
        });
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }

    public function setVerificationDateAttribute($value)
    {
        if ($value!=null){
            $this->attributes['verification_date'] = Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
        }else{
            $this->attributes['verification_date'] = now()->format('Y-m-d');
        }

    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }

    public function userOperations(){
        return $this->hasMany(UserOperation::class,'user_id','id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
