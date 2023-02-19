<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddedUserRequest extends FormRequest
{
    private $rules = [];
    public function all($keys = null)
    {
        $data = parent::all($keys);
        $hash = md5(($data['last_name'] ?? '') . ($data['first_name'] ?? '') . ($data['middle_name'] ?? '') . ($data['birth_date'] ?? ''));
        $data['hash'] = $hash;
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
            'last_name' => 'required',
            'first_name' => 'required',
            'middle_name' => 'required',
            'birth_date' => 'required|date_format:d/m/Y',
            'country_id' => 'required',
            'pass_num_inn' => 'required|unique:added_users|check_in_black_list',
            'hash' => 'sometimes|unique_fio_dob:last_name,first_name,middle_name,birth_date',

            'passport_id' => 'required_unless:country_id,1',
            'passport_authority' => 'required_unless:country_id,1',
            'passport_authority_code' => 'required_unless:country_id,1',
            'passport_issued_at' => 'required_unless:country_id,1',
            'passport_expires_at' => 'required_unless:country_id,1',

        ];
    }
}
