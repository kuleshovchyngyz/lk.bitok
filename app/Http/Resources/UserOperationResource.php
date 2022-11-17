<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOperationResource extends JsonResource
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
            'operation_id'=>$this->id,
            'operation_date' => $this->operation_date->format('d/m/Y H:i'),
            'operation_sum' => number_format($this->operation_sum / 100, 2),
            'operation_direction' => $this->operation_direction,
            'fullname' => $this->addedUser->last_name.' '.$this->addedUser->first_name.' '.$this->addedUser->middle_name,
            'pass_num_inn'=>$this->addedUser->pass_num_inn,
            'birth_date'=>$this->addedUser->birth_date->format('d/m/Y'),
            'user_id'=>$this->user_id
        ];
    }
}
