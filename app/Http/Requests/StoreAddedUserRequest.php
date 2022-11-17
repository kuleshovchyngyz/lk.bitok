<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddedUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'last_name'=>'required',
            'first_name'=>'required',
            'middle_name'=>'required',
            'birth_date'=>'required|date_format:d/m/Y',
            'country_id'=>'required',
            'pass_num_inn'=>'required|unique:added_users|check_in_black_list',
        ];
    }
}
