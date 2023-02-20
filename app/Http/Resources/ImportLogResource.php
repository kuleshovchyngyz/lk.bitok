<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportLogResource extends JsonResource
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
            'file' => $this->file_name,
            'black_list_name' => $this->bl_name,
            'code' => $this->bl_name_code,
            'status' => $this->status,
            'date' => $this->created_at->timezone('GMT+6')->format('d/m/Y H:i'),
        ];
    }
}
