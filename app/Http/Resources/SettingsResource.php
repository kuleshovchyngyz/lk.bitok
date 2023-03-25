<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
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
            "limit" => $this->limit,
            "usd_to_som" => $this->usd_to_som,
            "usdt_to_som" => $this->usdt_to_som,
            "rub_to_som" => $this->rub_to_som,
            "high_risk" => $this->high_risk,
            "risk" => $this->risk,
            "id" => $this->id
        ];
    }
}
