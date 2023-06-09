<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class DottDataImporter extends DataImporter
{
    private const PROVIDER_LIST_ID = 4;

    protected Crawler $sections;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://ridedott.com/ride-with-us/london/");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_LIST_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("li.p-small.mb-1");

        if (count($this->sections) === 0) {
            $this->createImportInfoDetails("204", self::PROVIDER_LIST_ID);

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
        $existingProviders = [];

        foreach ($this->sections as $section) {
            $cityName = trim($section->nodeValue);
            $countryName = trim($section->parentNode->previousSibling->previousSibling->nodeValue);

            $city = City::query()->where("name", $cityName)->first();
            $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

            if ($city || $alternativeCityName) {
                $cityId = $city ? $city->id : $alternativeCityName->city_id;

                $this->createProvider($cityId, self::PROVIDER_LIST_ID);
                $existingProviders[] = $cityId;
            }
            else {
                $country = Country::query()->where("name", $countryName)->orWhere("alternative_name", $countryName)->first();

                if ($country) {
                    $coordinates = $mapboxService->getCoordinatesFromApi($cityName, $countryName);

                    $countCoordinates = count($coordinates);

                    if (!$countCoordinates) {
                        $this->createImportInfoDetails("419", self::PROVIDER_LIST_ID);
                    }

                    $city = City::query()->create([
                        "name" => $cityName,
                        "latitude" => ($countCoordinates > 0) ? $coordinates[0] : null,
                        "longitude" => ($countCoordinates > 0) ? $coordinates[1] : null,
                        "country_id" => $country->id,
                    ]);

                    $this->createProvider($city->id, self::PROVIDER_LIST_ID);
                    $existingProviders[] = $city->id;
                } else {
                    $this->countryNotFound($cityName, $countryName);
                    $this->createImportInfoDetails("420", self::PROVIDER_LIST_ID);
                }
            }
        }
        $this->deleteMissingProviders(self::PROVIDER_LIST_ID, $existingProviders);
    }
}
