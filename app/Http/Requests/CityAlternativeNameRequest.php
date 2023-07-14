<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityAlternativeNameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "regex:/^[A-Z\s]/", "max:100", "unique:city_alternative_names"],
            "city_id" => ["exists:cities,id"],
        ];
    }
}
