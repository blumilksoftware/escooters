<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "city_alternative_names" => $this->cityAlternativeName,
            "cityProviders" => $this->cityProvider,
            "country" => $this->country,
        ];
    }
}
