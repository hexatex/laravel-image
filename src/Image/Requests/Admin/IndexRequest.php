<?php

namespace Hexatex\LaravelImage\Image\Requests\Admin;

use Hexatex\LaravelMisc\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    protected $maxRowsConfig = 'image.max-rows-per-page';
}
