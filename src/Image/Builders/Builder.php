<?php

namespace Hexatex\LaravelImage\Image\Builders;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelMisc\Contracts\Authenticatable;

interface Builder
{
    public function __construct(array $fill, Image $model);
    public function forStore(ImagePathDto $imagePathDto, Authenticatable $author): void;
    public function forUpdate(?ImagePathDto $imagePathDto): void;
}
