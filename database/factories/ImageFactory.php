<?php

namespace Hexatex\LaravelImage\Factories;

use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition()
    {
        $filename = 'test-image';

        $disk = StorageDisk::local->value;
        Storage::fake($disk);
        $file = UploadedFile::fake()->image($filename);

        return [
            'alt' => $this->faker->sentence,
            'disk' => $disk,
            'original_filename' => $this->faker->word,
            'path' => Storage::disk($disk)->put(config('image.storage-path'), $file),
            'thumb_path' => Storage::disk($disk)->put(config('image.storage-path'), $file),
        ];
    }

    /*
     * States
     */
    public function author(Authenticatable $author)
    {
        return $this->afterMaking(function (Image $image) use ($author) {
            $image->author()->associate($author);
        });
    }

    public function local()
    {
        return $this->state(function (array $attributes) {
            return [
                'disk' => StorageDisk::local->value,
            ];
        });
    }

    public function s3()
    {
        return $this->state(function (array $attributes) {
            return [
                'disk' => StorageDisk::s3->value,
            ];
        });
    }
}
