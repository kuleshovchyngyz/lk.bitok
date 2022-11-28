<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property float $parking_price
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'parking_price', 'rank', 'is_active'];

    public function carMarks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CarMark::class);
    }

    public function getCarMarkList()
    {

        $type_id = $this->attributes['id'];
        $carMarks = [];
        if ($type_id == 1) {
             $carMarks = CarMark::where([
                ['car_marks.is_active', 1],
                ['car_marks.car_type_id', $type_id],
                ['car_generations.year_begin', '>=', 1990],
                ['car_generations.year_end', '<=', 2022],
            ])
                ->leftJoin('car_models', 'car_marks.id', '=', 'car_models.car_mark_id')
                ->leftJoin('car_generations', 'car_models.id', '=', 'car_generations.car_model_id')
                ->select('car_marks.id as id', 'car_marks.name as name')
                ->groupBy('car_marks.id', 'car_marks.name')
                ->orderBy('car_marks.rank', 'asc')->orderBy('car_marks.name', 'ASC')
                ->get();
        } elseif ($type_id != 27) {
            $carMarks = CarMark::where([
                ['car_marks.is_active', 1],
                ['car_marks.car_type_id', $type_id],
            ])
                ->select('car_marks.id as id', 'car_marks.name as name')
                ->groupBy('car_marks.id', 'car_marks.name')
                ->orderBy('car_marks.rank', 'asc')->orderBy('car_marks.name', 'ASC')
                ->get();
        }

        if (count($carMarks) > 0 && $type_id != 27) {
//            $carMarks = CarMark::setLogo($carMarks);
            return $carMarks;
        }

    }


}
