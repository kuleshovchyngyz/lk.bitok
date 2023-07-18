<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
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
            'name'=>'required|unique:stocks,name',
            'address'=>'required',
            'iban'=>'unique:stocks,iban',
            'bank_account'=>'unique:stocks,bank_account',
            'bank_name'=>'',
            'swift'=>'unique:stocks,swift',
            'account_code'=>'required|unique:stocks,account_code',
        ];
    }
}
