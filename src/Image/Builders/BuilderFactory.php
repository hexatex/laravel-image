<?php

namespace Hexatex\LaravelImage\Image\Builders;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;

interface BuilderFactory
{
    public function create(array $fill, Image $image): Builder;
}
