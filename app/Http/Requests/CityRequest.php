<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "regex:/^[A-Z\s]/", "max:100", $this->uniqueRuleForCity("name")],
            "latitude" => ["required", "numeric"],
            "longitude" => ["required", "numeric"],
            "country_id" => ["exists:countries,id"],
        ];
    }

    protected function uniqueRuleForCity(string $column): Unique
    {
        $currentCityId = $this->route(param: "city");

        return Rule::unique(table: "cities", column: $column)->ignore($currentCityId);
    }
}
