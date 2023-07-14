<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\ProviderResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Provider;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function index(): Response
    {
        $cities = City::query()->with("cityAlternativeName", "cityProvider", "country")
            ->orderBy("country_id")
            ->paginate(15);

        $providers = Provider::all();
        $countries = Country::all();

        return Inertia::render("Cities/Index", [
            "cities" => CityResource::collection($cities),
            "providers" => ProviderResource::collection($providers),
            "countries" => CountryResource::collection($countries),
            "pagination" => $cities->links(),
        ]);
    }

    public function store(CityRequest $request): void
    {
        City::query()->create($request->validated());
    }

    public function update(CityRequest $request, City $city): void
    {
        $city->update($request->validated());
    }

    public function destroy(City $city): void
    {
        $city->delete();
    }
}
