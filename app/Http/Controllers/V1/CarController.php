<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\CarGeneration;
use App\Models\CarMark;
use App\Models\CarModel;
use App\Models\CarModification;
use App\Models\CarSerie;
use App\Models\CarType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CarController extends Controller
{
    public function carId(Request $request)
    {
        $type = CarType::where('name', $request->name)->firstOrFail();
        return $type->id;

    }

    public function carTypes(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CarResource::collection(CarType::all());
    }

    public function carType(CarType $carType)
    {
        return $carType;
        return new CarResource($carType);
    }

    public function carMarks(CarType $carType)
    {
        return CarResource::collection($carType->getCarMarkList());
    }

    public function carMark(CarMark $carMark)
    {
        return new CarResource($carMark);
    }

    public function carModels(CarMark $carMark)
    {
        return CarResource::collection($carMark->carModels);
    }

    public function carModel(CarModel $carModel)
    {
        return new CarResource($carModel);
    }

    public function years(CarModel $carModel)
    {
        return $carModel->years();
    }

    public function generation(CarGeneration $carGeneration)
    {
        return $carGeneration;
    }

    public function series(CarGeneration $carGeneration)
    {
        return CarResource::collection($carGeneration->carSeries);
    }

    public function getSeriesModel(CarModel $carModel)
    {
        return CarResource::collection($carModel->carSeries);
    }

    public function serie(CarSerie $carSeries)
    {
        return new CarResource($carSeries);
    }

    public function modification(CarModification $carModification)
    {
        return CarResource::collection($carModification);
    }

    public function carTitleData(Request $request)
    {
        $carTitleData = DB::table('car_types')
            ->select('car_types.name as car_type', 'car_marks.name as car_mark', 'car_models.name as car_model', 'car_generations.name as car_generation')
            ->leftJoin('car_marks', 'car_types.id', '=', 'car_marks.car_type_id')
            ->leftJoin('car_models', 'car_marks.id', '=', 'car_models.car_mark_id')
            ->leftJoin('car_generations', 'car_models.id', '=', 'car_generations.car_model_id')
            ->where($request->all())
            ->first();
        return $carTitleData;
    }

    public function getCars(Request $request)
    {
        $arr = $request->toArray();
        $response['types'] = CarType::whereIn('id', $arr['types'])->pluck('name', 'id');
        $response['marks'] = CarMark::whereIn('id', $arr['marks'])->pluck('name', 'id');
        $response['models'] = CarModel::whereIn('id', $arr['models'])->pluck('name', 'id');
        return $response;
    }

    protected function applicationUpdateData(Request $request)
    {

        $application = $request;


        $carMarks = null;
        $carModels = null;
        $carYears = null;
        $carGenerations = null;
        $carSeriess = null;
        $carModifications = null;
        $carEngines = null;
        $carTransmissions = null;
        $carGears = null;


        $carModel = CarModel::find($application->car_model_id);


        if ($application->car_type_id) {
            $carMarks = CarType::find($application->car_type_id)->getCarMarkList();
//            $carMarks = $this->getCarMarkList($application->car_type_id);
        }

        if ($application->car_mark_id) {
            $carModels = CarMark::find($application->car_mark_id)->carModels;
//            $carModels = $this->getCarModelList($application->car_mark_id);
        }
        if ($application->car_model_id) {
            $carYears = $carModel->years();
//            $carYears = $this->filteredItems($this->getCarYearList($application->car_model_id));
        }
        if ($application->year) {
            $carGenerations = $this->generations($carModel, $application->year);
//            $carGenerations = $carModels->getCarGenerationList($application->year);
//            $carGenerations = $this->getCarGenerationList($application->car_model_id, $application->year);
        }
        if ($application->car_generation_id) {
            $carSeriess = $carModel->getCarSeriesList($application->car_generation_id);
//            $carSeriess = $this->getCarSeriesList(CarGeneration::find($application->car_generation_id));
        } else {
            $carSeriess = CarResource::collection($carModel->carSeries);
        }
        if ($application->car_series_id) {
            $carModifications = $this->modifications($application->car_model_id, CarSerie::find($application->car_series_id), $application->year);
//            $carModifications = $this->getCarModificationList($application->car_model_id, $application->car_series_id, $application->year);
        }
        if ($application->car_modification_id) {
            $carEngines = $this->engines(CarModification::find($application->car_modification_id));
//            $carEngines = $this->getCarEngineList($application->car_modification_id);
        }
        if ($application->car_engine_id) {
            $carTransmissions = $this->transmissions(CarModification::find($application->car_modification_id));
//            $carTransmissions = $this->getCarTransmissionList($application->car_modification_id);
        }
        if ($application->car_gear_id) {
            $carGears = $this->gears(CarModification::find($application->car_modification_id));
//            $carGears = $this->getCarGearList($application->car_modification_id);
        }

//        $attachments = $application->attachments()->select('id', 'thumbnail_url', 'url')->get();
        $dataApplication = [
            'modelId' => $application->car_model_id,
            'car_mark_id' => $application->car_mark_id,
            'modificationId' => $application->car_modification_id,
            'year' => $application->year
        ];


        return json_encode(compact(
//            'attachments',
            'dataApplication',
            'carMarks',
            'carModels',
            'carYears',
            'carGenerations',
            'carSeriess',
            'carModifications',
            'carEngines',
            'carTransmissions',
            'carGears'
        ));
    }

    public function generations(CarModel $carModel, $year)
    {
        return $carModel->getCarGenerationList($year);
    }

    public function modifications($model, CarSerie $carSeries, $year)
    {
        return CarResource::collection($carSeries->getCarModificationList($model, $year));
    }

    public function engines(CarModification $carModification)
    {
        return $carModification->getCarEngineList();
    }

    public function transmissions(CarModification $carModification)
    {
        return $carModification->getCarTransmissionList();
    }

    public function gears(CarModification $carModification)
    {
        return $carModification->getCarGearList();
    }

}
