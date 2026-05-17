<?php

namespace Hexatex\LaravelImage\Image;

use Hexatex\LaravelImage\Image\Builders\BuilderFactory;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageRepository as ImageRepositoryContract;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\Image as ConcreteImage;
use Hexatex\LaravelImage\Image\Requests\Admin\IndexRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\StoreRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\UpdateRequest;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ImageRepository implements ImageRepositoryContract
{
    protected $builderFactory;

    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * @see IndexRequest
     * @return Image[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator
    {
        return $this->getModel()::filter($filters)
            ->paginate($filters['rowsPerPage'] ?? config('image.default-rows-per-page'));
    }

    /**
     * @see StoreRequest
     */
    public function store(array $fill, ImagePathDto $imagePathDto, Authenticatable $author): Image
    {
        $image = $this->create();
        $builder = $this->builderFactory->create($fill, $image);
        $builder->forStore($imagePathDto, $author);
        $image->save();

        return $image;
    }

    /**
     * @see UpdateRequest
     */
    public function update(array $fill, Image $image, ?ImagePathDto $imagePathDto): void
    {
        $builder = $this->builderFactory->create($fill, $image);
        $builder->forUpdate($imagePathDto);
        $image->save();
    }

    public function destroy(Image $image): void
    {
        $image->delete();
    }

    /*
     * Protected Methods
     */
    protected function getModel(): string
    {
        return ConcreteImage::class;
    }

    protected function create(): Image
    {
        return new ($this->getModel());
    }
}
