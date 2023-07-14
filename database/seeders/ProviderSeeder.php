<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ["name" => "bird", "color" => "#26ccf0"],
            ["name" => "bitmobility", "color" => "#8da6e3"],
            ["name" => "bolt", "color" => "#24f0a0"],
            ["name" => "dott", "color" => "#f5c604"],
            ["name" => "helbiz", "color" => "#ffffff"],
            ["name" => "hulaj", "color" => "#d6213f"],
            ["name" => "lime", "color" => "#00de00"],
            ["name" => "link", "color" => "#def700"],
            ["name" => "neuron", "color" => "#445261"],
            ["name" => "quick", "color" => "#009ac7"],
            ["name" => "spin", "color" => "#ff5436"],
            ["name" => "tier", "color" => "#0e1a50"],
            ["name" => "voi", "color" => "#f46c63"],
            ["name" => "whoosh", "color" => "#ffca47"],
        ];

        foreach ($providers as $provider) {
            Provider::create([
                "name" => $provider["name"],
                "color" => $provider["color"],
            ]);
        }
    }
}
