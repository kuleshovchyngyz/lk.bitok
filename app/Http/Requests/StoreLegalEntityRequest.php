<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class StoreLegalEntityRequest extends FormRequest
{
    private $rules = [];
    public function all($keys = null)
    {
        $data = parent::all($keys);
        if(!$this->route('legal_entity')){
            $hash = md5(trim($data['name'] ?? '') . trim($data['director_full_name'] ?? '') . trim($data['birth_date'] ?? ''));
            $data['hash'] = $hash;
        }
        return $data;
    }

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
            'name' => (!$this->route('legal_entity')) ? 'required' : '',
            'director_full_name' => (!$this->route('legal_entity')) ? 'required' : '',
            'address' => (!$this->route('legal_entity')) ? 'required' : '',
            'birth_date' => (!$this->route('legal_entity')) ? 'required|date_format:d/m/Y' : '',
            'verification_date' => 'nullable|date_format:d/m/Y',
            'verification' => 'nullable',
            'country_id' => (!$this->route('legal_entity')) ? 'required' : '',
            'hash' => 'required|sometimes|unique_director_full_name_dob:name,director_full_name,birth_date',
            'cv_photo.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',
            'cv_photo_bf.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',
            'licence_photo.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',
            'certificate_photo.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',
            'permit_photo.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',
            'passport_photo.*' => 'nullable|sometimes|mimes:doc,docx,xls,xlsx,pdf,csv,jpg,jpeg,png,bmp',

            'sanction'=> 'integer',

        ];
    }
    protected function prepareForValidation()
    {
        $sanction = $this->input('sanction');
        $country_id = $this->input('country_id');
        $country = Country::find($country_id);
        $value = 0;
        if($country){
            $value = $country->sanction;
        }
        $this->merge([
            'sanction' => $sanction ? $sanction : $value,
        ]);

    }
}
