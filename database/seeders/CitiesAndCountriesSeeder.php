<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CitiesAndCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $items = Storage::json("public/countries.json");

        $mapboxService = new MapboxGeocodingService();

        foreach ($items as $item) {
            $country = Country::query()->create([
                "name" => $item["name"],
                "latitude" => $item["latitude"],
                "longitude" => $item["longitude"],
                "iso" => strtolower($item["iso2"]),
            ]);

            $coordinates = [];

            if ($item["capital"]) {
                $coordinates = $mapboxService->getCoordinatesFromApi(cityName: $item["capital"], countryName: $item["name"]);
            }

            $countCoordinates = count($coordinates);

            if ($countCoordinates) {
                City::query()->updateOrCreate(
                    [
                        "name" => $item["capital"],
                    ],
                    [
                        "name" => $item["capital"],
                        "country_id" => $country->id,
                        "latitude" => $coordinates[0],
                        "longitude" => $coordinates[1],
                    ],
                );
            } else {
                echo $item["name"] . " capital has not been seeded." . PHP_EOL;
            }
        }
    }
}
