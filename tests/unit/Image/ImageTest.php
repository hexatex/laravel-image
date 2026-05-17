<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Hexatex\LaravelUser\User\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use \Mockery;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function testImageTableExists()
    {
        // Act & Assert
        $this->assertTrue(Schema::hasTable('images'));
    }

    public function testImageTableExistsWithCorrectColumns()
    {
        // Act
        $tableColumns = Schema::getColumnListing('images');

        // Assert
        $expectedColumns = ['id', 'alt', 'disk', 'original_filename', 'path', 'thumb_path', 'created_at', 'updated_at', 'author_type', 'author_id'];
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $tableColumns);
        }
    }

    public function testFillableAttributes()
    {
        // Arrange
        $image = new Image();

        // Act
        $fillableAttributes = $image->getFillable();

        // Assert
        $expectedFillable = ['alt'];
        $this->assertEquals($expectedFillable, $fillableAttributes);
    }

    public function testImageUrlAttribute()
    {
        // Arrange
        $image = $this->makeImage();
        $image->id = 12345;

        // Act
        $url = $image->url;

        // Assert
        $this->assertNotNull($url);
        $this->assertStringContainsString($image->id, $url);
    }

    public function testThumbnailUrlAttribute()
    {
        // Arrange
        $image = $this->makeImage();
        $image->id = 12345;

        // Act
        $thumbnailUrl = $image->thumbnailUrl;

        // Assert
        $this->assertNotNull($thumbnailUrl);
        $this->assertStringContainsString($image->id, $thumbnailUrl);
    }

    public function testAuthorRelation()
    {
        // Arrange
        $image = new Image();

        // Act
        $relation = $image->author();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $relation);
    }

    public function testCreateImage()
    {
        // Arrange
        $fill = [
            'alt' => 'test-image alt',
        ];
        $image = $this->makeImage($fill);

        // Act
        $image->save();

        // Assert
        $this->assertNotNull($image);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('test-image alt', $image->alt);
        $this->assertDatabaseHas('images', ['id' => $image->id]);
    }

    public function testUpdateImage()
    {
        // Arrange
        $fill =[
            'alt' => 'test-image alt',
        ];
        $image = $this->makeImage($fill);
        $image->save();

        $updatedData = [
            'alt' => 'updated-image alt',
        ];

        // Act
        $image->update($updatedData);

        // Assert
        $this->assertEquals('updated-image alt', $image->fresh()->alt);
        $this->assertDatabaseHas('images', ['id' => $image->id]);
    }

    public function testDeleteImage()
    {
        // Arrange
        $fill = [
            'alt' => 'image-to-be-deleted alt',
        ];
        $image = $this->makeImage($fill);
        $image->save();

        // Act
        $image->delete();

        // Assert
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    /*
     * Protected Methods
     */
    protected function makeImage(array $fill = [])
    {
        $author = new User;
        $author->id = 12345;
        $image = ImageFactory::new()->make($fill);
        $image->disk = StorageDisk::local->value;
        $image->author()->associate($author);

        return $image;
    }
}
