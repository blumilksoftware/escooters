<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BoltDataImporter extends DataImporter
{
    private const PROVIDER_ID = 3;

    protected array $fetchedCities = [];
    protected array $fetchedCityDictionary = [];
    protected array $fetchedCountriesDictionary = [];

    public function extract(): static
    {
        try {
            $client = new Client();
            $response = $client->get("https://bolt.eu/page-data/en/scooters/page-data.json");
            $content = json_decode($response->getBody()->getContents(), true);

            $this->fetchedCountriesDictionary = json_decode($content["result"]["data"]["countries"]["edges"][0]["node"]["data"], true)["countries"];
            $this->fetchedCityDictionary = $content["result"]["data"]["cities"]["nodes"];
            $this->fetchedCities = $content["result"]["data"]["scooterCities"]["nodes"];

            if (empty($this->fetchedCountriesDictionary) || empty($this->fetchedCityDictionary) || empty($this->fetchedCities)) {
                $this->createImportInfoDetails("204", self::PROVIDER_ID);
                $this->stopExecution = true;
            }
        } catch (GuzzleException) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);
            $this->stopExecution = true;
        }

        return $this;
    }

    public function transform(): void
    {
        if ($this->stopExecution) {
            return;
        }

        $fetchedCityDictionary = [];

        foreach ($this->fetchedCityDictionary as $city) {
            $fetchedCityDictionary[$city["slug"]] = $city;
        }

        $mapboxService = new MapboxGeocodingService();
        $existingCityProviders = [];

        foreach ($this->fetchedCities as $city) {
            if ($city["city"]) {
                $fetched = $fetchedCityDictionary[$city["city"]] ?? null;

                if ($fetched === null) {
                    continue;
                }

                $countryName = $this->fetchedCountriesDictionary[$fetched["country"]["countryCode"]]["name"] ?? "Poland";
                $cityName = $fetched["name"] ?? $city["city"];
            }

            $city = City::query()->where("name", $cityName)->first();
            $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

            if ($city || $alternativeCityName) {
                $cityId = $city ? $city->id : $alternativeCityName->city_id;

                $this->createProvider($cityId, self::PROVIDER_ID);
                $existingCityProviders[] = $cityId;
            }
            else {
                $country = Country::query()->where("name", $countryName)->orWhere("alternative_name", $countryName)->first();

                if ($country) {
                    $coordinates = $mapboxService->getCoordinatesFromApi($cityName, $countryName);

                    $countCoordinates = count($coordinates);

                    if (!$countCoordinates) {
                        $this->createImportInfoDetails("419", self::PROVIDER_ID);
                    }

                    $city = City::query()->create([
                        "name" => $cityName,
                        "latitude" => ($countCoordinates > 0) ? $coordinates[0] : null,
                        "longitude" => ($countCoordinates > 0) ? $coordinates[1] : null,
                        "country_id" => $country->id,
                    ]);

                    $this->createProvider($city->id, self::PROVIDER_ID);
                    $existingCityProviders[] = $city->id;
                } else {
                    $this->countryNotFound($cityName, $countryName);
                    $this->createImportInfoDetails("420", self::PROVIDER_ID);
                }
            }
        }

        unset($fetchedCities);
        unset($fetchedCityDictionary);
        unset($fetchedCountriesDictionary);

        $this->deleteMissingProviders(self::PROVIDER_ID, $existingCityProviders);
    }
}
