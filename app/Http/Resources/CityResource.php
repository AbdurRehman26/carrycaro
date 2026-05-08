<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city_type' => $this->city_type,
            'country' => [
                'id' => $this->whenLoaded('country', fn () => $this->country->id),
                'name' => $this->whenLoaded('country', fn () => $this->country->name),
                'code' => $this->whenLoaded('country', fn () => $this->country->code),
            ],
        ];
    }
}
