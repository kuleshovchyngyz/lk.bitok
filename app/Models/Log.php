<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'author_id',
        'description',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'author_id');
    }
}
