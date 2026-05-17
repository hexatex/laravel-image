<?php

namespace Hexatex\LaravelImage\Tests\Feature\Image;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Factories\TestImageableFactory;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelImage\Image\Resources\Admin\ImageResource;
use Hexatex\LaravelImage\Tests\FeatureTestCase;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageAdminApiTest extends FeatureTestCase
{
    use RefreshDatabase;

    public function testGetImage()
    {
        $image = ImageFactory::new()->author($this->user)->create();

        $response = $this->getJson("admin/api/image/{$image->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'alt',
                    'disk',
                    'original_filename',
                    'created_at',
                    'updated_at',

                    /* Accessors & Mutators */
                    'url',
                    'thumbnail_url',

                    'author' => [
                        'id',
                        'morph_class',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])->assertJsonFragment([
                'id' => $this->user->id,
                'morph_class' => $this->user->getMorphClass(),
                'name' => $this->user->name,
                'email' => $this->user->email,
                'created_at' => $this->user->created_at,
                'updated_at' => $this->user->updated_at,
            ])->assertJsonFragment([
                'id' => $image->id,
                'alt' => $image->alt,
                'disk' => $image->disk,
                'original_filename' => $image->original_filename,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,

                /* Accessors & Mutators */
                'url' => $image->url,
                'thumbnail_url' => $image->thumbnail_url,
            ]);
    }

    public function testStoreImage()
    {
        $disk = StorageDisk::local->value; // Todo test other storage methods?
        $originalFilename = 'photo1.jpg';

        Storage::fake($disk);

        $fill = ImageFactory::new()->make()->toArray();
        $fill['file'] = UploadedFile::fake()->image($originalFilename)->size(500);

        $response = $this->postJson("admin/api/image", $fill);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'alt',
                    'disk',
                    'original_filename',
                    'created_at',
                    'updated_at',

                    /* Accessors & Mutators */
                    'url',
                    'thumbnail_url',
                    'author' => [
                        'id',
                        'morph_class',
                        'name',
                        'created_at',
                    ],
                ],
            ])->assertJsonFragment([
                'id' => $this->user->id,
                'morph_class' => $this->user->getMorphClass(),
                'name' => $this->user->name,
                'created_at' => $this->user->created_at,
            ])->assertJsonFragment([
                'alt' => $fill['alt'],
                'disk' => $disk,
                'original_filename' => $originalFilename,
            ]);

        $data = $response->getData()->data;
        $image = Image::find($data->id);

        $this->assertEquals($image->disk, $disk);

        $this->assertTrue(Storage::disk($disk)->exists($image->path));
        $this->assertTrue(Storage::disk($disk)->exists($image->thumb_path));
    }

    public function testUpdateImage(bool $hasFile = true)
    {
        $disk = StorageDisk::local->value; // Todo test other storage methods?
        $originalFilename = 'photo1.jpg';

        Storage::fake($disk);

        $image = ImageFactory::new()->author($this->user)->create();

        $fill = ImageFactory::new()->make()->toArray();

        if ($hasFile) {
            $fill['file'] = UploadedFile::fake()->image($originalFilename)->size(500);
        }

        $response = $this->putJson("admin/api/image/{$image->id}", $fill);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'alt',
                    'disk',
                    'original_filename',
                    'created_at',
                    'updated_at',

                    /* Accessors & Mutators */
                    'url',
                    'thumbnail_url',
                    'author' => [
                        'id',
                        'morph_class',
                        'name',
                        'created_at',
                    ],
                ],
            ])->assertJsonFragment([
                'id' => $this->user->id,
                'morph_class' => $this->user->getMorphClass(),
                'name' => $this->user->name,
                'created_at' => $this->user->created_at,
            ])->assertJsonFragment([
                'alt' => $fill['alt'],
                'disk' => $hasFile ? $disk : $image->disk,
                'original_filename' => $hasFile ? $originalFilename : $image->original_filename,
            ]);

        if (!$hasFile) {
            Storage::disk($disk)->exists($image->path);
            Storage::disk($disk)->exists($image->thumb_path);
        }

        $originalPath = $image->path;
        $originalThumbPath = $image->thumb_path;

        $image->refresh();

        if ($hasFile) {
            $this->assertNotEquals($originalPath, $image->path);
            $this->assertNotEquals($originalThumbPath, $image->thumb_path);
            $this->assertTrue(Storage::disk($disk)->exists($image->path));
            $this->assertTrue(Storage::disk($disk)->exists($image->thumb_path));
        }

        $this->assertEquals($image->disk, $disk);
    }

    public function testUpdateImage_noFile()
    {
        $this->testUpdateImage(hasFile: false);
    }

    public function testDestroyImage()
    {
        Storage::fake('local');
        $image = ImageFactory::new()->author($this->user)->create();

        $this->assertTrue(Storage::disk($image->disk)->exists($image->path));
        $this->assertTrue(Storage::disk($image->disk)->exists($image->thumb_path));

        $response = $this->deleteJson("admin/api/image/{$image->id}");

        $response->assertSuccessful()->assertJsonStructure([]);

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        $this->assertFalse(Storage::disk($image->disk)->exists($image->path));
        $this->assertFalse(Storage::disk($image->disk)->exists($image->thumb_path));
    }
}
