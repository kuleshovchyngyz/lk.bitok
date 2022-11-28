<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $car_modification_id
 * @property integer $car_characteristic_id
 * @property string $value
 * @property string $unit
 * @property integer $id_car_type
 * @property boolean $rank
 * @property integer $is_active
 */
class CarCharacteristicValue extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['car_modification_id', 'car_characteristic_id', 'value', 'unit', 'id_car_type', 'rank', 'is_active'];
}
