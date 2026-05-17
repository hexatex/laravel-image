<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Strategies\Resizer;

use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\Strategies\Resizer\DefaultResizer;
use Hexatex\LaravelImage\Image\Strategies\Resizer\DefaultResizerFactory;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class DefaultResizerFactoryTest extends TestCase
{
    public function testCreateReturnsDefaultResizerInstance()
    {
        // Arrange
        $fill = ['alt' => 'Test Alt', 'file' => UploadedFile::fake()->image('test.jpg')];

        $imagePathDto = new ImagePathDto();
        $imagePathDto->disk = StorageDisk::local->value;
        $imagePathDto->original_filename = 'test-original_filename';
        $imagePathDto->path = 'test-path';
        $imagePathDto->thumb_path = 'test-thumb_path';

        $thumbnail = false;

        $factory = new DefaultResizerFactory();

        // Act
        $resizer = $factory->create($fill, $imagePathDto, $thumbnail);

        // Assert
        $this->assertInstanceOf(DefaultResizer::class, $resizer);
    }
}
