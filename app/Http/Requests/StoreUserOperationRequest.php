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
            'operation_date'=>(!$this->route('user_operation')) ? 'required|date_format:d/m/Y H:i' : '',
            'operation_direction'=>(!$this->route('user_operation')) ? 'required' : '',
            'operation_sum'=>(!$this->route('user_operation')) ? 'required' : '',
            'wallet_id'=>'',
            'currency'=>'',
            'passport_photo.*' => 'image',
            'sanction'=>'',
            'checked'=>'boolean',
            'user_id' => (!$this->route('user_operation'))?'required_without:legal_id|user_or_legal_id':'',
            'legal_id' => (!$this->route('user_operation'))?'required_without:user_id|user_or_legal_id':''
        ];
    }
}
