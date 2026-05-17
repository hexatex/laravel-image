<?php

namespace Hexatex\LaravelImage\Image\Strategies\Storage;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Requests\Admin\StoreRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\UpdateRequest;

interface StorageFactory
{
    /**
     * @see StoreRequest
     * @see UpdateRequest
     */
    public function create(?array $fill, ?Image $image = null): Storage;
}
