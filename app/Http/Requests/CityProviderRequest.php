<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityProviderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "providerIds" => ["array"],
        ];
    }
}
