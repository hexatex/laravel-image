<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Resources\Web;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelImage\Image\Resources\Web\ImageResource;
use Hexatex\LaravelImage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class ImageResourceTest extends TestCase
{
    use RefreshDatabase;

    public function testImageResource()
    {
        // Arrange
        $image = ImageFactory::new()->make();
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
            'original_filename' => $image->original_filename,

            /* Accessors & Mutators */
            'url' => $image->url,
            'thumbnail_url' => $image->thumbnail_url,
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
