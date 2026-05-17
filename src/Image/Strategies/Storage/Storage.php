<?php

namespace Hexatex\LaravelImage\Image\Strategies\Storage;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;

interface Storage
{
    public function saveImageFiles(array $fill): ImagePathDto;
    public function deleteImageFiles(Image $image): void;
}
