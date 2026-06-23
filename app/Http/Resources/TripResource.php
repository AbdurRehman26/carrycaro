<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_city' => new CityResource($this->whenLoaded('fromCity')),
            'to_city' => new CityResource($this->whenLoaded('toCity')),
            'user' => new UserResource($this->whenLoaded('user')),
            'departure_date' => $this->departure_date?->toDateString(),
            'arrival_date' => $this->arrival_date?->toDateString(),
            'airline_id' => $this->airline_id,
            'airline' => $this->airline,
            'airline_details' => new AirlineResource($this->whenLoaded('airlineRecord')),
            'notes' => $this->notes,
            'weight_available' => $this->weight_available,
            'weight_price' => $this->weight_price,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
