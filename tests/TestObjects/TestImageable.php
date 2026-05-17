<?php

namespace Hexatex\LaravelImage\Tests\TestObjects;

use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelImage\Image\Contracts\Imageable;
use Hexatex\LaravelMisc\Model;
use Illuminate\Contracts\Database\Query\Builder;

class TestImageable extends Model implements Imageable
{
    protected $fillable = [];

    /*
     * Imageable
     */
    public function images(): Builder
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
}
