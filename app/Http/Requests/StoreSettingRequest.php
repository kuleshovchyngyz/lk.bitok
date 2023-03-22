<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'limit'=>'sometimes|required|integer',
            'usd_to_som'=>'required',
            'usdt_to_som'=>'required',
            'rub_to_som'=>'required',
            'high_risk'=>'',
            'risk'=>''
        ];
    }
}
