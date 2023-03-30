<?php

namespace App\Providers;

use App\Models\AddedUser;
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

        Validator::extend('check_in_black_list', function ($attribute, $value, $parameters,$validator) {
            $input = $validator->getData();
            $birth_date = $input['birth_date'] ?? null;
            $hash = md5(trim($input['last_name'] ?? null) . trim($input['first_name'] ?? null) . trim($input['middle_name'] ?? null) . $birth_date);
            return !BlackList::where('hash', $hash)->whereNotIn('type' ,['pft', 'plpd'])->count() > 0;
        });
        Validator::extend('unique_fio_dob', function ($attribute, $value, $parameters, $validator) {
            $input = $validator->getData();
            $birth_date = $input['birth_date'] ?? null;
            $hash = md5(($input['last_name'] ?? null) . ($input['first_name'] ?? null) . ($input['middle_name'] ?? null) . $birth_date);
            return !AddedUser::where('hash', $hash)->count() > 0;
        });

    }
}
