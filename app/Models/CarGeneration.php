<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $car_model_id
 * @property string $name
 * @property string $year_begin
 * @property string $year_end
 * @property integer $id_car_type
 * @property boolean $is_disabled
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarGeneration extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['car_model_id', 'name', 'year_begin', 'year_end', 'id_car_type', 'is_disabled', 'rank', 'is_active'];

    public function carSeries(){
        return $this->hasMany(CarSerie::class);
    }
}
