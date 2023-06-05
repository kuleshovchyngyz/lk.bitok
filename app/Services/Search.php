<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;

class Search
{

    public function searchFromClients($model, Request $request)
    {
        $type = $model;
        $model = 'App\Models\\' . $model;
        return $model::when($request->get('pass_num_inn'), function ($q) use ($request) {
            $q->where('pass_num_inn', 'like', '%' . $request->pass_num_inn . '%');
        })->when($request->get('name') != null && $type == 'AddedUser', function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                foreach (explode(' ', $request->name) as $name) {
                    $q->where(function ($query) use ($name) {
                        $query->orWhere('last_name', 'like', '%' . $name . '%')
                            ->orWhere('first_name', 'like', '%' . $name . '%')
                            ->orWhere('middle_name', 'like', '%' . $name . '%');
                    });
                }
            });
        })
            ->when($request->get('name') != null && $type == 'LegalEntity', function ($q) use ($request) {
            $q->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->name . '%')
                    ->orWhere('director_full_name', 'like', '%' . $request->name . '%');
            });
        })->when($request->get('country_id'), function ($q) use ($request) {
            $q->where('country_id', $request->country_id);
        })->when($request->get('birth_date') != null, function ($q) use ($request) {
            $date = Carbon::createFromFormat('d/m/Y', $request->birth_date)->format('Y-m-d');
            $q->where('birth_date', 'like', '%' . $date . '%');
        })->when($request->get('risk'), function ($q) use ($request) {
            $q->where('sanction', $request->get('risk'));
        })->get();
    }
}
