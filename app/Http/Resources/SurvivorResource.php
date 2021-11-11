<?php

namespace App\Http\Resources;

use App\Utils\Constants;
use Illuminate\Http\Resources\Json\JsonResource;

class SurvivorResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'lastLocation' => [
                'lat' => $this->last_location->getLat(),
                'long' => $this->last_location->getLng()
            ],
            'status' => $this->status,
            'createdAt' => $this->created_at->format(Constants::DEFAULT_DATE_SHORT_FORMAT),
            'updatedAt' => $this->updated_at->format(Constants::DEFAULT_DATE_SHORT_FORMAT)
        ];
    }
}
