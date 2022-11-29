<?php

namespace App\Models;

use App\Http\Resources\CarResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $id
 * @property integer $car_mark_id
 * @property string $name
 * @property string $name_rus
 * @property integer $date_create
 * @property integer $date_update
 * @property integer $car_type_id
 * @property boolean $is_disabled
 * @property boolean $rank
 * @property boolean $is_active
 */
class CarModel extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['car_mark_id', 'name', 'name_rus', 'date_create', 'date_update', 'car_type_id', 'is_disabled', 'rank', 'is_active'];

    public function years()
    {
        $carYears = $this->carGenerations()
            ->select(DB::raw("min(year_begin) as year_begin,max(year_end) as year_end"))
            ->groupBy('car_model_id')
            ->first();

        return
            $this->filteredItems(
                isset($carYears->year_begin) ? $carYears : (object)['year_begin' => 1950, 'year_end' => Carbon::now()->year]
            );
    }

    public function carGenerations()
    {
        return $this->hasMany(CarGeneration::class);
    }

    public function filteredItems($carYears)
    {
        if (!empty($carYears)) {
            $filteredItems = [];
            if (isset($carYears->year_begin) && isset($carYears->year_end)) {
                $currentYear = $carYears->year_end;
                while ($currentYear >= $carYears->year_begin) {
                    $filteredItems[] = (object)['name' => $currentYear, 'id' => $currentYear];
                    $currentYear--;
                }
            } elseif (isset($carYears->year_begin)) {
                $currentYear = date('Y');
                while ($currentYear >= $carYears->year_begin) {
                    $filteredItems[] = (object)['name' => $currentYear, 'id' => $currentYear];
                    $currentYear--;
                }
            } else {
                $filteredItems[] = (object)['name' => 'Год Не Указан', 'id' => 0];
            }

            return $filteredItems;
        } else {
            return (object)['name' => 'Год Не Указан', 'id' => 0];
        }
    }

    public function getCarGenerationList( $year)
    {
        $model_id = $this->attributes['id'];
        if (isset($model_id) && is_numeric($model_id)) {
            $searchFilter = [['car_model_id', $model_id]];
            if (isset($year) && is_numeric($year) && $year > 0) {
                $searchFilter[] = ['year_begin', '<=', $year];
                $searchFilter[] = ['year_end', '>=', $year];
            }
            $carGenerations = CarGeneration::where($searchFilter)
                ->select('id', 'name')
                ->get();
            return $carGenerations;
        }
    }

    public function carSeries()
    {
        return $this->hasMany(CarSerie::class);
    }

    public function carSeriesWithGenerations()
    {
        return $this->carGenerations()->with('carSeries')->get();
    }

    public function getCarSeriesList( $generation_id)
    {
        return $this->carSeries;

            $searchFilter[] = ['car_model_id', $this->attributes['id']];
            if (isset($generation_id) && is_numeric($generation_id) && $generation_id > 0) {
                $searchFilter[] = ['car_generation_id', $generation_id];
            }

            $carSeries = $this->carSeries()
                ->where($searchFilter)
                ->select('id', 'name')
                ->get();
            $returnValues = [];
            foreach ($carSeries as $singleSeries) {
                $returnValues[] = (object)['id' => $singleSeries->id, 'name' => $singleSeries->name, 'body' => $singleSeries->body_name];
            }
            return $returnValues;

    }

}
