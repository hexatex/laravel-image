<?php

namespace Hexatex\LaravelImage;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class ImageService
{
    public function index(array $filters): LengthAwarePaginator|Image[]
    {
        return Image::filter($filters)->paginate($filters['rowsPerPage'] ?? config('image.max-rows-per-page'));
    }

    public function store(array $fill): Image
    {
        $image = new Image($fill);
        $this->saveUploadedFile($fill['file'], $image);
        $image->save();

        return $image;
    }

    public function update(array $fill, Image $image): void
    {
        $image->fill($fill);

        if (isset($fill['file'])) {
            $this->saveUploadedFile($fill['file'], $image);
        }

        $image->save();
    }

    /**
     * Destroy
     * @throws \Exception
     */
    public function destroy(Image $image): void
    {
        Storage::delete($image->path);
        $image->delete();
    }

    /*
     * Private Methods
     */
    /**
     * Save to disk
     */
    private function saveUploadedFile(UploadedFile $uploadedFile, Image $image): void
    {
        $image->original_filename = $uploadedFile->getClientOriginalName();

        $intervention = $this->resizedIntervention($uploadedFile, (int)config('media.image.max-length'));
        $image->path = 'image/' . $uploadedFile->hashName();
        Storage::put($image->path, (string)$intervention);

        $intervention = $this->resizedIntervention($uploadedFile, (int)config('media.image.max-thumb-length'));
        $image->thumb_path = 'image/thumb.' . $uploadedFile->hashName();
        Storage::put($image->thumb_path, (string)$intervention);
    }

    /**
     * UploadedFile to resized Intervention Image
     */
    private function resizedIntervention(UploadedFile $uploadedFile, int $maxLength): \Intervention\Image\Image
    {
        $intervention = InterventionImage::make($uploadedFile);
        $intervention->orientate(); // Otherwise some mobile device photos will be orientated wrong when uploaded.
        $intervention->encode('jpg', config('image.jpeg-quality'));

        if ($intervention->width() < $intervention->height()) {
            if ($intervention->width() > $maxLength) {
                $this->resizeImage($intervention, $maxLength, null);
            }
        } else {
            if ($intervention->height() > $maxLength) {
                $this->resizeImage($intervention, null, $maxLength);
            }
        }

        return $intervention;
    }

    /**
     * Resize an Intervention Image
     */
    private function resizeImage($intervention, ?int $width, ?int $height): void
    {
        $intervention->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }
}
