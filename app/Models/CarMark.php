<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $name_rus
 * @property integer $date_create
 * @property integer $date_update
 * @property integer $car_type_id
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarMark extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'name_rus', 'date_create', 'date_update', 'car_type_id', 'rank', 'is_active'];

    public function carModels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CarModel::class);
    }
    public function getCarModelList()
    {

            $carModels = $this->carModels()
                ->select('id', 'name')
                ->orderBy('rank', 'asc')->orderBy('name', 'ASC')->get();

            if (count($carModels) > 0) {
                return $carModels;
            } else {
                return ['id' => 0, 'name' => __('Unknown Model')];
            }

    }
//    public function scopePopular($query)
//    {
//        return $query->where('votes', '>', 100);
//    }
}
