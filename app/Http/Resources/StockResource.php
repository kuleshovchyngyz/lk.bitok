<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'address'=>$this->address,
            'iban'=>$this->iban,
            'bank_account'=>$this->bank_account,
            'bank_name'=>$this->bank_name,
            'swift'=>$this->swift,
            'account_code'=>$this->account_code,
        ];
    }
}
