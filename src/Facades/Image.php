<?php

namespace Hexatex\LaravelImage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hexatex\LaravelImage\LaravelImage
 */
class LaravelImage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hexatex\LaravelImage\ImageService::class;
    }
}
