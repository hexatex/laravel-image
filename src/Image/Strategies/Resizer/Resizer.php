<?php

namespace Hexatex\LaravelImage\Image\Strategies\Resizer;

use Illuminate\Http\UploadedFile;

interface Resizer
{
    /** UploadedFile to resized image as a string */
    public function resize(UploadedFile $uploadedFile, int $maxWidth, int $maxHeight): string;
}
