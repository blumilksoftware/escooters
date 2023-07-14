<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class LimeDataImporter extends DataImporter
{
    private const PROVIDER_ID = 7;

    protected Crawler $sections;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://www.li.me/locations");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter(".pb-4 > .box-content .inline-block");

        if (count($this->sections) === 0) {
            $this->createImportInfoDetails("204", self::PROVIDER_ID);

            $this->stopExecution = true;
        }

        return $this;
    }

    public function transform(): void
    {
        if ($this->stopExecution) {
            return;
        }

        $mapboxService = new MapboxGeocodingService();
        $existingCityProviders = [];

        foreach ($this->sections as $section) {
            $cityName = trim($section->nodeValue);
            $countryName = trim($section->parentNode->parentNode->previousSibling->previousSibling->nodeValue);

            $city = City::query()->where("name", $cityName)->first();
            $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

            if ($city || $alternativeCityName) {
                $cityId = $city ? $city->id : $alternativeCityName->city_id;

                $this->createProvider($cityId, self::PROVIDER_ID);
                $existingCityProviders[] = $cityId;
            }
            else {
                switch ($countryName) {
                    case str_contains($countryName, "US"):
                        $country = Country::query()->where("name", "United States")->first();

                        break;
                    default:
                        $country = Country::query()->where("name", $countryName)->orWhere("alternative_name", $countryName)->first();

                        break;
                }

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
        $this->deleteMissingProviders(self::PROVIDER_ID, $existingCityProviders);
    }
}
