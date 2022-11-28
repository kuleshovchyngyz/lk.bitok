<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $car_series_id
 * @property integer $car_model_id
 * @property string $name
 * @property integer $year_begin
 * @property integer $year_end
 * @property integer $date_create
 * @property integer $date_update
 * @property integer $id_car_type
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarModification extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['car_series_id', 'car_model_id', 'name', 'year_begin', 'year_end', 'date_create', 'date_update', 'id_car_type', 'rank', 'is_active'];

    public function getCarEngineList(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->carCharacteristicValues()->where(
            'car_characteristic_id', 12)
            ->select('id', 'value as name')
            ->get();
    }

    public function carCharacteristicValues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CarCharacteristicValue::class);
    }

    public function getCarTransmissionList()
    {
        return $this->carCharacteristicValues()->where([
                ['car_characteristic_id', 24]
            ])
                ->select('id', 'value as name')
                ->get();

    }
    public function getCarGearList()
    {
        return $this->carCharacteristicValues()->where([
                ['car_characteristic_id', 27]
            ])
                ->select('id', 'value as name')
                ->get();
    }
}
