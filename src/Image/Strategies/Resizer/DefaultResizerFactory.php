<?php

namespace Hexatex\LaravelImage\Image\Strategies\Resizer;

use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\Requests\Admin\StoreRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\UpdateRequest;

class DefaultResizerFactory implements ResizerFactory
{
    /**
     * @see StoreRequest
     * @see UpdateRequest
     */
    public function create(array $fill, ImagePathDto $imagePathDto, bool $thumbnail): Resizer
    {
        return resolve(DefaultResizer::class);
    }
}
