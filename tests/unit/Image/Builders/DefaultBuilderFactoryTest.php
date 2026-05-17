<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Builders;

use Hexatex\LaravelImage\Image\Builders\DefaultBuilder;
use Hexatex\LaravelImage\Image\Builders\DefaultBuilderFactory;
use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DefaultBuilderFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        // Arrange
        $fill = ImageFactory::new()->make()->toArray();
        $image = ImageFactory::new()->make();

        $builderFactory = new DefaultBuilderFactory();

        // Act
        $resizer = $builderFactory->create($fill, $image);

        // Assert
        $this->assertInstanceOf(DefaultBuilder::class, $resizer);
    }
}
