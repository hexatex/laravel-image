<?php

namespace Hexatex\LaravelImage\Image\Contracts;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ImageRepository
{
    /**
     * @return Image[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator;
    public function store(array $fill, ImagePathDto $imagePathDto, Authenticatable $author): Image;
    public function update(array $fill, Image $image, ?ImagePathDto $imagePathDto): void;
    public function destroy(Image $image): void;
}
