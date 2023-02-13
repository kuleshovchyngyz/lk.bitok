<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $car_model_id
 * @property integer $car_generation_id
 * @property string $name
 * @property integer $date_create
 * @property integer $date_update
 * @property integer $id_car_type
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarSerie extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['car_model_id', 'car_generation_id', 'name', 'date_create', 'date_update', 'id_car_type', 'rank', 'is_active'];

    public function getCarModificationList($model_id, $year)
    {
        if (isset($model_id) && is_numeric($model_id) ) {
            $yearFilter = [];
            if (isset($year) && is_numeric($year) && $year > 0) {
                $yearFilter[] = ['year_begin', '<=', $year];
                $yearFilter[] = ['year_end', '>=', $year];
            }
            return  $this->carModifications()->where('car_model_id', $model_id)
                ->where('id','>',0)
//                ->where(function ($q) use ($yearFilter) {
//                    $q->where($yearFilter)
//                        ->orWhereNull('year_begin')
//                        ->orWhereNull('year_end');
//                })
                ->select('id', 'name')
                ->get();
        }
        return [];
    }

    public function carModifications()
    {
        return $this->hasMany(CarModification::class, 'car_series_id', 'id');
    }



}
