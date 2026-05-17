<?php

namespace Hexatex\LaravelImage\Image\Builders;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Patterns\Builder as BaseBuilder;

class DefaultBuilder extends BaseBuilder implements Builder
{
    public function __construct(array $fill, Image $model)
    {
        $this->fill = $fill;
        $this->model = $model;
    }

    public function forStore(ImagePathDto $imagePathDto, Authenticatable $author): void
    {
        $this->fill();
        $this->associate('author', $author);
        $this->setImagePaths($imagePathDto);
    }

    public function forUpdate(?ImagePathDto $imagePathDto): void
    {
        $this->fill();

        if ($imagePathDto) {
            $this->setImagePaths($imagePathDto);
        }
    }

    /*
     * Protected Methods
     */
    protected function setImagePaths(ImagePathDto $imagePathDto): void
    {
        $this->model->disk = $imagePathDto->disk;
        $this->model->original_filename = $imagePathDto->original_filename;
        $this->model->path = $imagePathDto->path;
        $this->model->thumb_path = $imagePathDto->thumb_path;
    }
}
