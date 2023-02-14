<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => class_basename($this->resource) == 'BlackList' ? '999999' . $this->id : $this->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'birth_date' => $this->birth_date->format('d/m/Y'),
            'registration_date' => $this->created_at->format('d/m/Y H:i'),
            'country_id' => $this->country_id,
            'country' => $this->country->name,
            'pass_num_inn' => $this->pass_num_inn,
            'black_list' => class_basename($this->resource) == 'BlackList',
            'user_operations' => UserOperationResource::collection($this->whenLoaded('userOperations')),
            'type' => $this->when(isset($this->type), function () {
                return $this->type;
            }),
            'passport_id' => $this->passport_id,

            'passport_authority' =>
                 $this->passport_authority,
            'passport_authority_code' => $this->passport_authority_code,
            'passport_issued_at' =>$this->passport_issued_at,
            'passport_expires_at' => $this->passport_expires_at,
        ];
    }

}
