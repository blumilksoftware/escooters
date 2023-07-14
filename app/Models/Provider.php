<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $color
 */
class Provider extends Model
{
    public function cityProvider()
    {
        return $this->belongsTo(CityProvider::class);
    }
}
