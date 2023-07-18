<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
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
            'name'=>(!$this->route('stock')) ? 'unique:stocks,name' : '',
            'address'=>'',
            'iban'=>(!$this->route('stock')) ?'unique:stocks,iban' : '',
            'bank_account'=>(!$this->route('stock')) ?'unique:stocks,bank_account' : '',
            'bank_name'=>'',
            'swift'=>(!$this->route('stock')) ?'unique:stocks,swift' : '',
            'account_code'=>(!$this->route('stock')) ?'unique:stocks,account_code' : '',
        ];
    }
}
