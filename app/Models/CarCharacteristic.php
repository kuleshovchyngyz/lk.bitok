<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property integer $id_car_type
 * @property boolean $is_active
 * @property integer $rank
 */
class CarCharacteristic extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'id_car_type', 'is_active', 'rank'];
}
