<?php

return [
    'default-rows-per-page' => env('IMAGE_DEFAULT_ROWS_PER_PAGE', 20),
    'max-rows-per-page' => env('IMAGE_MAX_ROWS_PER_PAGE', 500),
    'jpeg-quality' => env('JPEG_QUALITY', 70),
    'max-width' => env('MAX_WIDTH', 3840), // 3840x2160 is 4k
    'max-height' => env('MAX_HEIGHT', 2160),
    'max-thumb-width' => env('MIN_THUMB_WIDTH', 640),
    'max-thumb-height' => env('MAX_THUMB_HEIGHT', 640),
    'min-upload-size' => env('MIN_UPLOAD_SIZE', '1'), // default 1kb
    'max-upload-size' => env('MAX_UPLOAD_SIZE', '10000'), // default 10mb
    'storage-path' => env('STORAGE_PATH', 'images'),
    'thumbnail-suffix' => env('THUMBNAIL_SUFFIX', 'thumb'),
    'storage-disk' => env('IMAGE_FILESYSTEM_DRIVER', env('FILESYSTEM_DRIVER', 'local')),
];
