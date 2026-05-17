<?php

namespace Hexatex\LaravelImage\Image;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageRepository;
use Hexatex\LaravelImage\Image\Contracts\ImageService as ImageServiceContract;
use Hexatex\LaravelImage\Image\Requests\Admin\IndexRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\StoreRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\UpdateRequest;
use Hexatex\LaravelImage\Image\Strategies\Storage\StorageFactory;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelService\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ImageService extends Service implements ImageServiceContract
{
    protected $storageFactory;

    public function __construct(ImageRepository $repository, StorageFactory $storageFactory)
    {
        $this->repository = $repository;
        $this->storageFactory = $storageFactory;
    }

    /**
     * @see IndexRequest
     * @return Image[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator
    {
        return $this->repository->index($filters);
    }

    /**
     * @see StoreRequest
     */
    public function store(array $fill, Authenticatable $author): Image
    {
        $imagePathDto = $this->storageFactory->create($fill)->saveImageFiles($fill);

        return $this->repository->store($fill, $imagePathDto, $author);
    }

    /**
     * @see UpdateRequest
     */
    public function update(array $fill, Image $image): void
    {
        $imagePathDto = isset($fill['file'])
            ? $this->storageFactory->create($fill)->saveImageFiles($fill)
            : null;

        $this->repository->update($fill, $image, $imagePathDto);
    }

    public function destroy(Image $image): void
    {
        $this->storageFactory->create(fill: null, image: $image)->deleteImageFiles($image);

        $this->repository->destroy($image);
    }
}
