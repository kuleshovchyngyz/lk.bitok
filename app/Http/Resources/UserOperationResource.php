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

        $addedUser = null;
        if (class_basename($this->whenLoaded('addedUser')) != 'MissingValue') {
            $addedUser = $this->addedUser;
        }
        $legalEntity = null;
        if (class_basename($this->whenLoaded('legalEntity')) != 'MissingValue') {
            $legalEntity = $this->legalEntity;
        }

        return [
            'operation_id' => $this->id,
            'sanction' => $this->sanction,
            'checked' => $this->checked,
            'operation_date' => $this->operation_date->format('d/m/Y H:i'),
            'operation_sum' => number_format($this->operation_sum / 100, 2),
            'operation_direction' => $this->operation_direction,
            'user_id' => $this->user_id,
            'wallet_id' => $this->wallet_id,
            'currency' => $this->currency,
            'fullname' => $addedUser ? $addedUser->last_name . ' ' . $addedUser->first_name . ' ' . $addedUser->middle_name : $this->whenLoaded('addedUser'),
            'director_full_name' => $legalEntity ? $legalEntity->director_full_name: $this->whenLoaded('legalEntity'),
            'address' => $legalEntity ? $legalEntity->address: $this->whenLoaded('legalEntity'),
            'country' => $legalEntity ? $legalEntity->country->name: $this->whenLoaded('legalEntity'),
            'pass_num_inn' => $addedUser ? $addedUser->pass_num_inn : $this->whenLoaded('addedUser'),
            'birth_date' => $addedUser ? $addedUser->birth_date->format('d/m/Y') : $this->whenLoaded('addedUser'),
            'attachments' => $this->attachments,
//            'type'=> request()->get('type') ?? 'all'
        ];
    }
}
