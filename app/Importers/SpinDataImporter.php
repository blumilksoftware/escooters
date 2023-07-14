<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class SpinDataImporter extends DataImporter
{
    private const PROVIDER_ID = 11;

    protected Crawler $sections;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://www.spin.app/");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter(".locations-container .w-dyn-item");

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
            $cityName = $section->nodeValue;

            if (str_contains($cityName, "University")) {
                continue;
            }

            $nodeClasses = $section->parentNode->parentNode->parentNode->getAttribute("class");
            $countryName = match (true) {
                str_contains($nodeClasses, "locations-list-") => ucfirst(str_replace(
                    "locations-list-",
                    "",
                    implode("", array_filter(explode(" ", $nodeClasses), fn(string $c): bool => $c !== "locations-list-wrapper")),
                )),
                default => "United States",
            };

            if ($countryName === "United States") {
                $cityName = explode(", ", $cityName)[0];
            }

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
