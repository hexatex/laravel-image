<?php

namespace Hexatex\LaravelImage\Image\Dtos;

use Hexatex\LaravelMisc\Contracts\Enums\StorageDisk;

class ImagePathDto
{
    /**
     * String value for the disk to use for storage
     * 
     * @see config/filesystems.php
     * @see StorageDisk values
     */
    public string $disk;

    public string $original_filename;
    public string $path;
    public string $thumb_path;
}
