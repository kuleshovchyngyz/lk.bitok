<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOperation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['operation_date'];


    public function addedUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AddedUser::class, 'user_id', 'id');
    }

    public function setOperationSumAttribute($value)
    {
        $value = intval(str_replace(',', '', trim($value)));
        $this->attributes['operation_sum'] = $value * 100;
    }




    public function setOperationDateAttribute($value)
    {
        $this->attributes['operation_date'] = Carbon::createFromFormat('d/m/Y H:i', $value)->format('Y-m-d H:i');
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

