<?php

namespace Hexatex\LaravelImage\Image\Contracts;

use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelService\Contracts\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\Image as InterventionImage;

interface ImageService extends Service
{
    /**
     * @return Image[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator;
    public function store(array $fill, Authenticatable $author): Image;
    public function update(array $fill, Image $image): void;
    public function destroy(Image $image): void;
}
