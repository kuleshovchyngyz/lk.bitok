<?php

namespace App\Providers;

use App\Models\BlackList;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Validator::extend('check_in_black_list', function ($attribute, $value, $parameters) {
            return !BlackList::where('pass_num_inn',$value)->count()>0;
        });

    }
}
