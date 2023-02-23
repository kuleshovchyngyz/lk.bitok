<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends FormRequest
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
        $a = ['0,1', '1,2','0,1,2', '0','1','2'];
        return [
            'name' => 'required|unique:countries',
            'sanction'=>'integer'
//            'sanction'=>['required',Rule::in($a)]
        ];
    }

    protected function prepareForValidation()
    {
        $sanction = $this->input('sanction');
//        $sanction = str_replace('и', ',', $sanction);
//        $sanction = str_replace('№', '', $sanction);
//        $sanction = str_replace(' ', '', $sanction);
        $this->merge([
            'sanction' => $sanction ? $sanction : 0,
        ]);
    }
}
