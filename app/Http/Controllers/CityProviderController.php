<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CityProviderRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\ProviderResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Provider;
use App\Services\CityProviderService;
use App\Services\DataImporterService;

class CityProviderController extends Controller
{
    public function index(): array
    {
        $cities = CityResource::collection(
            City::with("cityAlternativeName", "cityProvider", "country")
                ->has("cityProvider")
                ->get()
                ->sortByDesc(fn($city): int => $city->cityProvider->count()),
        );

        $providers = ProviderResource::collection(Provider::all());

        $countries = Country::query()->whereHas("city.cityProvider")
            ->with(["city" => function ($query): void {
                $query->whereHas("cityProvider")
                    ->with("cityAlternativeName", "cityProvider");
            }])
            ->get();

        $countries = CountryResource::collection($countries);

        return [
            "cities" => $cities,
            "providers" => $providers,
            "countries" => $countries,
        ];
    }

    public function update(CityProviderService $service, CityProviderRequest $request, City $city): void
    {
        $service->updateProvider($request->providerIds, $city);
    }

    public function runImporters(DataImporterService $service): void
    {
        $service->run();
    }
}
