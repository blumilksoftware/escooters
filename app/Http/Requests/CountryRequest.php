<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CountryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "regex:/^[A-Z\s]/", "max:100", $this->uniqueRuleForCountry("name")],
            "alternative_name" => ["string", "regex:/^[A-Z\s]/", "nullable", "max:100"],
            "latitude" => ["required", "numeric"],
            "longitude" => ["required", "numeric"],
            "iso" => ["required", "string", "lowercase", "alpha", "max:20", $this->uniqueRuleForCountry("iso")],
        ];
    }

    protected function uniqueRuleForCountry(string $column): Unique
    {
        $currentCountryId = $this->route(param: "country");

        return Rule::unique(table: "countries", column: $column)->ignore($currentCountryId);
    }
}
