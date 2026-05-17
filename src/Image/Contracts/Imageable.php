<?php

namespace Hexatex\LaravelImage\Image\Contracts;

use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Contracts\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Relationships
 * @property-read Image[]|Collection $images
 */
interface Imageable extends Model
{
    public function images(): Builder;
}
