<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'iban',
        'bank_account',
        'bank_name',
        'swift',
        'account_code',
    ];
}
