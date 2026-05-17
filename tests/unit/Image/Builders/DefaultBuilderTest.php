<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Builders;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\ImageType\ImageType;
use Hexatex\LaravelImage\Image\Builders\DefaultBuilder;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Eloquent\Relations\MorphTo;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Mockery;

class DefaultBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function testForStore()
    {
        // Arrange
        $fill = $this->getFill();
        $image = $this->mockImage();
        $builder = $this->createDefaultBuilder($fill, $image);
        $author = Mockery::mock(Authenticatable::class);
        $authorMorphTo = Mockery::mock(MorphTo::class);
        $imagePathDto = $this->createImagePathDto();

        // Act
        $image
            ->shouldReceive('fill')
            ->with($fill);

        $image
            ->shouldReceive('author')
            ->once()
            ->andReturn($authorMorphTo);

        $authorMorphTo
            ->shouldReceive('associate')
            ->with($author);

        $builder->forStore($imagePathDto, $author);

        // Assert
        $this->assertEquals($image->disk, $imagePathDto->disk);
        $this->assertEquals($image->original_filename, $imagePathDto->original_filename);
        $this->assertEquals($image->path, $imagePathDto->path);
        $this->assertEquals($image->thumb_path, $imagePathDto->thumb_path);
    }

    public function testForUpdate_noImagePathDto()
    {
        $this->testForUpdate(hasImagePathDto: false);
    }

    public function testForUpdate(bool $hasImagePathDto = true)
    {
        // Arrange
        $fill = $this->getFill();
        $image = $this->mockImage();
        $builder = $this->createDefaultBuilder($fill, $image);
        $imagePathDto = $hasImagePathDto ? $this->createImagePathDto() : null;

        // Arrange and Act
        $image
            ->shouldReceive('fill')
            ->with($fill);

        // Act
        $result = $builder->forUpdate($imagePathDto);

        // Assert
        if ($hasImagePathDto) {
            $this->assertEquals($image->disk, $imagePathDto->disk);
            $this->assertEquals($image->original_filename, $imagePathDto->original_filename);
            $this->assertEquals($image->path, $imagePathDto->path);
            $this->assertEquals($image->thumb_path, $imagePathDto->thumb_path);
        } else {
            $this->addToAssertionCount(1);
        }
    }

    /*
     * Protected Methods
     */
    protected function createDefaultBuilder(array $fill, Image $image)
    {
        return new DefaultBuilder($fill, $image);
    }

    protected function mockBelongsTo()
    {
        return Mockery::mock(BelongsTo::class);
    }

    protected function getFill()
    {
        return ImageFactory::new()->make()->toArray();
    }

    protected function createImagePathDto()
    {
        $imagePathDto = new ImagePathDto();
        $imagePathDto->disk = StorageDisk::local->value;
        $imagePathDto->original_filename = 'test-original_filename';
        $imagePathDto->path = 'test-path';
        $imagePathDto->thumb_path = 'test-thumb_path';

        return $imagePathDto;
    }

    protected function mockImage()
    {
        $image = ImageFactory::new()->make();
        $image = Mockery::mock($image);

        return $image;
    }
}
