<?php

namespace App\Http\Resources;

use App\Models\BlackList;
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
        if (!$this->stock) {
            return [
                'id' => class_basename($this->resource) == 'BlackListsLegalEntity' ? '9999999' . $this->id : $this->id,
                'black_list' => class_basename($this->resource) == 'BlackListsLegalEntity' || BlackList::where('hash', $this->hash)->count() > 0,
                'sanction' => $this->sanction,
                'name' => $this->name,
                'hash' => $this->hash,
                'director_full_name' => $this->director_full_name,
                'address' => $this->address,
                'birth_date' => $this->birth_date?->format('d/m/Y'),
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
        } else {
            return [
                'id' => class_basename($this->resource) == 'BlackListsLegalEntity' ? '9999999' . $this->id : $this->id,
                'black_list' => class_basename($this->resource) == 'BlackListsLegalEntity' || BlackList::where('hash', $this->hash)->count() > 0,
                'sanction' => $this->sanction,
                'name' => $this->name,
                'hash' => $this->hash,
                'address' => $this->address,
                'stock' => $this->stock,
                'iban' => $this->iban,
                'bank_account' => $this->bank_account,
                'bank_name' => $this->bank_name,
                'swift' => $this->swift,
                'account_code' => $this->account_code,
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
}