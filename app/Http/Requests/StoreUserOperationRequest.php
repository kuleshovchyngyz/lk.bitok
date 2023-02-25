<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserOperationRequest extends FormRequest
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
            'user_id'=>'required',
            'operation_date'=>'required|date_format:d/m/Y H:i',
            'operation_direction'=>'required',
            'operation_sum'=>'required',
            'wallet_id'=>'',
            'currency'=>'',
            'passport_photo.*' => 'image',
        ];
    }
}
