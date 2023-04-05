<?php

namespace App\Http\Resources;

use App\Models\BlackList;
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
            'sanction' => $this->sanction,
            'verification' => $this->verification,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'birth_date' => $this->birth_date->format('d/m/Y'),
            'registration_date' => $this->created_at->format('d/m/Y H:i'),
            'verification_date' => $this->when(isset($this->verification_date), function () {
                return $this->verification_date->format('d/m/Y');
            }),
            'country_id' => $this->country_id,
            'country' => $this->when(isset($this->country), function () {
                return $this->country->name;
            }),
            'pass_num_inn' => $this->pass_num_inn,
            'black_list' => class_basename($this->resource) == 'BlackList' || BlackList::where('hash', $this->hash)->whereIn('type' ,['pft', 'plpd'])->count() > 0,
            'user_operations' => UserOperationWalletResource::collection($this->whenLoaded('userOperations', function () {
                return $this->userOperations->unique('wallet_id');
            })),
            'type' => $this->when(isset($this->type), function () {
                return $this->type;
            }),
            'hash' => $this->when(isset($this->hash), function () {
                return $this->hash;
            }),
            'passport_id' => $this->passport_id,
            'passport_authority' => $this->passport_authority,
            'passport_authority_code' => $this->passport_authority_code,
            'passport_issued_at' =>$this->passport_issued_at,
            'passport_expires_at' => $this->passport_expires_at,
            'attachments' => $this->attachments,
        ];
    }

}
