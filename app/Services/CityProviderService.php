<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\City;
use App\Models\CityProvider;

class CityProviderService
{
    public function updateProvider(array $providerIds, City $city): void
    {
        $existingCityProviderIds = [];

        foreach ($providerIds as $providerId) {
            $provider = CityProvider::query()
                ->updateOrCreate([
                    "city_id" => $city->id,
                    "provider_id" => $providerId,
                ]);

            if ($provider->created_by !== "scrapper") {
                CityProvider::query()->where([
                    "city_id" => $city->id,
                    "provider_id" => $providerId,
                ])->update([
                    "created_by" => "admin",
                ]);
            }

            $existingCityProviderIds[] = $providerId;
        }

        $cityProvidersToDelete = CityProvider::query()
            ->where("city_id", $city->id)
            ->whereNotIn("provider_id", $existingCityProviderIds)
            ->get();

        $cityProvidersToDelete->each(fn($cityProvider) => $cityProvider->delete());
    }
}
