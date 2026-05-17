<?php

namespace Hexatex\LaravelImage\Image\Strategies\Storage;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\Strategies\Resizer\ResizerFactory;
use Hexatex\LaravelImage\Image\Strategies\Storage\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Intervention\Image\Image as InterventionImage;
use InvalidArgumentException;

class DefaultStorage implements Storage
{
    protected string $disk;
    protected ResizerFactory $resizerFactory;

    public function __construct(ResizerFactory $resizerFactory)
    {
        $this->resizerFactory = $resizerFactory;
        $this->disk = config('image.storage-disk');
    }

    /*
     * Accessors & Mutators
     */
    public function getDisk(): string
    {
        return $this->disk;
    }

    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    /*
     * Public Methods
     */
    /**
     * Expects the 'file' key in the $fill array to contain an UploadedFile instance.
     *
     * @see StoreRequest
     * @see UpdateRequest
     *
     * @throws InvalidArgumentException
     */
    public function saveImageFiles(array $fill): ImagePathDto
    {
        if (empty($fill['file']) || !($fill['file'] instanceof UploadedFile)) {
            throw new InvalidArgumentException('The $fill array must contain a valid \'file\' key of the type UploadedFile.');
        }

        /** @var UploadedFile */
        $uploadedFile = $fill['file'];

        $imagePathDto = new ImagePathDto;
        $imagePathDto->disk = $this->disk;
        $imagePathDto->original_filename = $uploadedFile->getClientOriginalName();
        $imagePathDto->path = config('image.storage-path') . '/' . $uploadedFile->hashName();
        $imagePathDto->thumb_path = $imagePathDto->path . config('image.thumbnail-suffix');

        $this->resizeAndSave($fill, $imagePathDto, $imagePathDto->path, 'image.max-width', 'image.max-height', thumbnail: false);
        $this->resizeAndSave($fill, $imagePathDto, $imagePathDto->thumb_path, 'image.max-thumb-width', 'image.max-thumb-height', thumbnail: true);

        return $imagePathDto;
    }

    public function deleteImageFiles(Image $image): void
    {
        $this->deleteFile($image->path);
        $this->deleteFile($image->thumb_path);
    }

    /*
     * Protected Methods
     */
    protected function resizeAndSave(array $fill, ImagePathDto $imagePathDto, string $path, string $maxWidthConfig, string $maxHeightConfig, bool $thumbnail): void
    {
        $resizer = $this->resizerFactory->create($fill, $imagePathDto, $thumbnail);

        $file = $resizer->resize($fill['file'], config($maxWidthConfig), config($maxHeightConfig));

        $this->putFile($path, $file);
    }

    protected function putFile(string $path, string $file)
    {
        StorageFacade::disk($this->disk)->put($path, (string)$file);
    }

    protected function deleteFile(string $path)
    {
        StorageFacade::disk($this->disk)->delete($path);
    }
}
