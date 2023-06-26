<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LegalResource extends JsonResource
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
            'id' => $this->id,
            'sanction' => $this->sanction,
            'name' => $this->name,
            'director_full_name' => $this->director_full_name,
            'address' => $this->address,
            'birth_date' => $this->birth_date->format('d/m/Y'),
            'verification' => $this->verification,
            'registration_date' => $this->created_at->format('d/m/Y H:i'),
            'verification_date' => $this->when(isset($this->verification_date), function () {
                return $this->verification_date->format('d/m/Y');
            }),
            'country_id' => $this->country_id,
            'country' => $this->when(isset($this->country), function () {
                return $this->country->name;
            }),
            'user_operations' => UserOperationWalletResource::collection($this->whenLoaded('userOperations', function () {
                return $this->userOperations->unique('wallet_id');
            })),
            'attachments' => $this->attachments,
        ];
    }
}
