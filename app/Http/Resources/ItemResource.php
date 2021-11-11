<?php

namespace App\Http\Resources;

use App\Utils\Constants;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'points' => $this->points,
            'createdAt' => $this->created_at->format(Constants::DEFAULT_DATE_SHORT_FORMAT),
            'updatedAt' => $this->updated_at->format(Constants::DEFAULT_DATE_SHORT_FORMAT)
        ];
    }
}
