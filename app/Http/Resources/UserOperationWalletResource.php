<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOperationWalletResource extends JsonResource
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
            'operation_id' => $this->id,
            'wallet_id' => $this->wallet_id,
            'thumbnail' => $this->when(count($this->attachments) > 0, function () {
                return $this->attachments->first()->thumbnail_url;
            }),
            'url' => $this->when(count($this->attachments) > 0, function () {
                return $this->attachments->first()->url;
            }),

        ];
    }
}
