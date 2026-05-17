<?php

namespace Hexatex\LaravelImage\Image\Builders;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;

class DefaultBuilderFactory implements BuilderFactory
{
    public function create(array $fill, Image $image): Builder
    {
        return new DefaultBuilder($fill, $image);
    }
}
