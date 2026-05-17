<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Strategies\Storage;

use Hexatex\LaravelImage\Image\Strategies\Storage\DefaultStorage;
use Hexatex\LaravelImage\Image\Strategies\Storage\DefaultStorageFactory;
use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DefaultStorageFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate_noFill_noImage()
    {
        $this->testCreate(withFill: false, withImage: false);
    }

    public function testCreate_noFill_hasImage()
    {
        $this->testCreate(withFill: false, withImage: true);
    }

    public function testCreate_hasFill_noImage()
    {
        $this->testCreate(withFill: true, withImage: false);
    }

    public function testCreate(bool $withFill = true, bool $withImage = true)
    {
        // Arrange
        $fill = $withFill ? ImageFactory::new()->make()->toArray() : null;
        $image = $withImage ? ImageFactory::new()->make() : null;

        $builderFactory = new DefaultStorageFactory();

        // Act
        $resizer = $builderFactory->create($fill, $image);

        // Assert
        $this->assertInstanceOf(DefaultStorage::class, $resizer);
    }
}
