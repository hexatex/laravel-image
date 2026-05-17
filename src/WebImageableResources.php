<?php

namespace Hexatex\LaravelImage;

use Hexatex\LaravelImage\Image\Contracts\Imageable;
use Hexatex\LaravelMisc\Managers\ResourceManager;

/**
 * @see Imageable
 * @see ResourceManager
 */
class WebImageableResources extends ResourceManager
{
    protected static array $resources = [];
}
