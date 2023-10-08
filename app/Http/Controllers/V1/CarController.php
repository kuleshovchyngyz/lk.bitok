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
    public function upload(Request $request)
    {

//        return $request->all();
        $mX = intval($_REQUEST['x']);
        $mY = intval($_REQUEST['y']);
        $mW = intval($_REQUEST['w']);
        $mH = intval($_REQUEST['h']);


        header("Content-Type: image/jpg");
        @sleep(1);
        @error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
        @ini_set('display_errors', true);
        @ini_set('html_errors', false);
        @ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

        define('CMSCORE', true);
        define('ROOT_DIR', substr(dirname(__FILE__), 0, -10));
//        define( 'ENGINE_DIR', ROOT_DIR . '/core' );
        define('UPLOAD_DIR', public_path() . '/uploads/');


        function upload_images_resize_preview($max_width, $max_height, $source_file, $dst_dir, $quality = 90)
        {
            $imgsize = getimagesize($source_file);
            $width = $imgsize[0];
            $height = $imgsize[1];
            $mime = $imgsize['mime'];

            switch ($mime) {
                case 'image/gif':
                    $image_create = "imagecreatefromgif";
                    $image = "imagegif";
                    break;

                case 'image/png':
                    $image_create = "imagecreatefrompng";
                    $image = "imagepng";
                    $quality = 7;
                    break;

                case 'image/jpeg':
                    $image_create = "imagecreatefromjpeg";
                    $image = "imagejpeg";
                    break;

                default:
                    return false;
                    break;
            }

            $dst_img = imagecreatetruecolor($max_width, $max_height);
            ///////////////

            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
            $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
            imagefilledrectangle($dst_img, 0, 0, $max_width, $max_height, $transparent);

            /////////////
            $src_img = $image_create($source_file);

            $width_new = $height * $max_width / $max_height;
            $height_new = $width * $max_height / $max_width;
            //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
            if ($width_new > $width) {
                //cut point by height
                $h_point = (($height - $height_new) / 2);
                //copy image
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
            } else {
                //cut point by width
                $w_point = (($width - $width_new) / 2);
                imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
            }

            $image($dst_img, $dst_dir, $quality);

            if ($dst_img)
                imagedestroy($dst_img);
            if ($src_img)
                imagedestroy($src_img);
        }

//usage example

        function upload_images_random_name($length = 10)
        {
            $string = '';
            $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";

            for ($p = 0; $p < $length; $p++) {
                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
            }

            return $string;
        }

        $response = array();

        $filePath = UPLOAD_DIR . '' . $_REQUEST['filename'];

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $new_filename = upload_images_random_name(20) . '.' . $extension;
        $preview_width = 320; //ширина превью
        $preview_height = 240; //высота превью

        $fileNewPath = UPLOAD_DIR . '' . $new_filename;
        $fileNewThumbPath = UPLOAD_DIR . 'thumb_' . $new_filename;


        $fileUrlThumb = $config['site_url'] . 'uploads/thumb_' . $new_filename;

        $mX = intval($_REQUEST['x']);
        $mY = intval($_REQUEST['y']);
        $mW = intval($_REQUEST['w']);
        $mH = intval($_REQUEST['h']);

        $imgsize = getimagesize($filePath);
        $mime = $imgsize['mime'];
        $quality = 90;

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image_save = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image_save = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image_save = "imagejpeg";
                break;

            default:
                die();
                break;
        }


        $img1 = $image_create($filePath);

        $x = 200;
        $y = 200;

        $gd = $img1;

        $corners[0] = array(50, 25);
        $corners[1] = array(25, 75);
        $corners[2] = array(100, 10);
        $corners[3] = array(150, 100);

        $values = array(
            25, 75, // Point 2 (x, y)
            50, 25,  // Point 1 (x, y)
            100, 10,  // Point 3 (x, y)
            150, 100,  // Point 4 (x, y)
        );

        $red = imagecolorallocate($gd, 255, 0, 0);
//        imagepolygon($gd, $corners, 4, $red);
        imagefilledpolygon($gd, $values, 4, $red);
//        for ($i = 0; $i < 100000; $i++) {
//            imagesetpixel($gd, round($x),round($y), $red);
//            $a = rand(0, 2);
//            $x = ($x + $corners[$a]['x']) / 2;
//            $y = ($y + $corners[$a]['y']) / 2;
//        }

        header('Content-Type: image/png');
//        imagepng($gd);
        $image_save($gd, $fileNewPath, $quality);


        $img2 = imagecreatetruecolor($mW, $mH); // create img2 for selection

        imagecopy($img2, $img1, 0, 0, $mX, $mY, $mW, $mH); // copy selection to img2

        $gaussian = array(
            array(1.0, 2.0, 1.0),
            array(2.0, 4.0, 2.0),
            array(1.0, 2.0, 1.0)
        );
        for ($i = 0; $i <= 100; $i++) {
            if ($i % 5 == 0) {//each 10th time apply 'IMG_FILTER_SMOOTH' with 'level of smoothness' set to -7
                imagefilter($img2, IMG_FILTER_SMOOTH, -7);
            }
            imagefilter($img2, IMG_FILTER_GAUSSIAN_BLUR);
            //imageconvolution($img2, $gaussian, 16, 0); // apply convolution to img2
        }


        imagecopymerge($img1, $img2, $mX, $mY, 0, 0, $mW, $mH, 100); // merge img2 in img1

        $image_save($img1, $fileNewPath, $quality);

        upload_images_resize_preview($preview_width, $preview_height, $fileNewPath, $fileNewThumbPath); //resize

        imagedestroy($img1);
        imagedestroy($img2);

        $response = array('name' => $new_filename, 'url' => $fileUrl, 'thumb_url' => $fileUrlThumb);

        @header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        die();

    }

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
        } elseif ($carModel) {
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
            'modelId' => optional($application)->car_model_id,
            'car_mark_id' => optional($application)->car_mark_id,
            'modificationId' => optional($application)->car_modification_id,
            'year' => optional($application)->year
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
