<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\CityProvider;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class CityProviderSeeder extends Seeder
{
    public function run(): void
    {
        $cities = City::all();
        $providers = Provider::all();

        foreach ($cities as $city) {
            foreach ($providers as $provider) {
                CityProvider::query()->create([
                    "provider_id" => $provider->id,
                    "city_id" => $city->id,
                    "created_by" => "admin",
                ]);
            }
        }
    }
}
