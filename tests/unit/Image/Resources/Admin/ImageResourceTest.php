<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Resources\Admin;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelImage\Image\Resources\Admin\ImageResource;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelUser\Factories\UserFactory;
use Hexatex\LaravelUser\User\Resources\Admin\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class ImageResourceTest extends TestCase
{
    use RefreshDatabase;

    public function testImageResource()
    {
        // Arrange
        $user = UserFactory::new()->make();
        $image = ImageFactory::new()->author($user)->make();
        $image->id = 12345;
        $image->created_at = Carbon::yesterday();
        $image->updated_at = Carbon::now();
        $resource = new ImageResource($image);

        // Act
        $data = $resource->toArray(request());

        // Assert
        $this->assertEquals([
            'id' => $image->id,
            'alt' => $image->alt,
            'disk' => $image->disk,
            'original_filename' => $image->original_filename,
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at,

            /* Accessors & Mutators */
            'url' => $image->url,
            'thumbnail_url' => $image->thumbnail_url,

            /* Relationships */
            'author' => new UserResource($user),
        ], $data);

        $this->assertStringContainsString(
            'http://localhost/image/' . $image->id,
            $data['url']
        );

        $this->assertStringContainsString(
            'http://localhost/image/' . $image->id . '/thumbnail',
            $data['thumbnail_url']
        );
    }
}
