<?php

namespace Hexatex\LaravelImage\Image\Requests\Web;

use Hexatex\LaravelMisc\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    protected $maxRowsConfig = 'image.max-rows-per-page';
}
