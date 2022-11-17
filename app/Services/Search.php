<?php

namespace App\Services;

use Illuminate\Http\Request;

class Search
{

    public function searchFromModel($model, Request $request)
    {
        $model = 'App\Models\\' . $model;
        return $model::when($request->get('pass_num_inn'), function ($q) use ($request) {
            $q->where('pass_num_inn', 'like', '%' . $request->pass_num_inn . '%');
        })->when($request->get('name'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                foreach (explode(' ', $request->name) as $name) {
                    $q->orWhere('last_name', 'like', '%' . $name . '%')
                        ->orWhere('first_name', 'like', '%' . $name . '%')
                        ->orWhere('middle_name', 'like', '%' . $name . '%');
                }
            });
        })->get();
    }
}
