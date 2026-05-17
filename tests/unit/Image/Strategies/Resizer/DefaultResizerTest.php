<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Strategies\ResizerStrategy;

use Hexatex\LaravelImage\Image\Strategies\Resizer\DefaultResizer;
use Hexatex\LaravelImage\Image\Strategies\Resizer\ResizerFactory;
use Hexatex\LaravelImage\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image as InterventionFacade;
use Intervention\Image\Image as InterventionImage;

class DefaultResizerTest extends TestCase
{
    protected $resizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resizer = resolve(DefaultResizer::class);
    }

    public function testResize_resizeLandscapeImage()
    {
        // Arrange
        $imageWidth = 1000; // Landscape image
        $imageHeight = 600;
        $maxWidth = $imageWidth;
        $maxHeight = $imageHeight - 1; // Too tall

        $this->testResize($maxWidth, $maxHeight, $imageWidth, $imageHeight);
    }

    public function testResize_resizePortraitImage()
    {
        // Arrange
        $imageWidth = 800;
        $imageHeight = 1000; // Portrait image
        $maxWidth = $imageWidth - 1; // Too wide
        $maxHeight = $imageHeight;

        $this->testResize($maxWidth, $maxHeight, $imageWidth, $imageHeight);
    }

    public function testResize_dontResizeLandscapeImage()
    {
        // Arrange
        $imageWidth = 1000; // Landscape image
        $imageHeight = 600;
        $maxWidth = $imageWidth; // Not too wide
        $maxHeight = $imageHeight; // Not too tall

        $this->testResize($maxWidth, $maxHeight, $imageWidth, $imageHeight);
    }

    public function testResize_dontResizePortraitImage()
    {
        // Arrange
        $imageWidth = 800;
        $imageHeight = 1000; // Portrait image
        $maxWidth = $imageWidth; // Not too wide
        $maxHeight = $imageHeight; // Not too tall

        $this->testResize($maxWidth, $maxHeight, $imageWidth, $imageHeight);
    }

    /*
     * Protected Methods
     */
    protected function testResize(int $maxWidth, int $maxHeight, int $imageWidth, int $imageHeight)
    {
        // Arrange
        $portrait = $imageWidth < $imageHeight;
        $uploadedFile = UploadedFile::fake()->image('test.jpg');
        $jpegQuality = config('image.jpeg-quality');

        $imageMock = $this->mock(InterventionImage::class);

        // Act
        InterventionFacade::shouldReceive('make')
            ->once()
            ->with($uploadedFile)
            ->andReturnUsing(function () use ($portrait, $jpegQuality, $maxWidth, $maxHeight, $imageWidth, $imageHeight, $imageMock) {
                $imageMock
                    ->shouldReceive('orientate')
                    ->once();

                $imageMock
                    ->shouldReceive('encode')
                    ->once()
                    ->with('jpg', $jpegQuality);

                if ($portrait) {
                    $imageMock
                        ->shouldReceive('width')
                        ->twice()
                        ->andReturn($imageWidth);

                    $imageMock
                        ->shouldReceive('height')
                        ->once()
                        ->andReturn($imageHeight);
                } else { // Landscape
                    $imageMock
                        ->shouldReceive('width')
                        ->once()
                        ->andReturn($imageWidth);

                    $imageMock
                        ->shouldReceive('height')
                        ->twice()
                        ->andReturn($imageHeight);
                }

                $resize = false;
                if ($portrait && $imageWidth > $maxWidth) {
                    $resize = true;
                } else if ($imageHeight > $maxHeight) {
                    $resize = true;
                }

                if (!$resize) {
                    $imageMock->shouldNotReceive('resize');

                    return $imageMock;
                }

                $mockInterface = $imageMock->shouldReceive('resize');

                if ($portrait) {
                    $mockInterface->with(null, $maxHeight, \Mockery::type('closure'));
                } else {
                    $mockInterface->with($maxWidth, null, \Mockery::type('closure'));
                }

                $mockInterface->once()
                    ->withArgs(function ($widthArg, $heightArg, $closure) use ($maxWidth, $maxHeight) {
                        if (is_callable($closure)) {
                            $constraint = $this->mock(\Intervention\Image\Constraint::class);
                            $constraint->shouldReceive('aspectRatio')
                                ->once()
                                ->andReturnNull();

                            $closure($constraint);

                            return true;
                        }

                        return false;
                    })
                    ->andReturn($imageMock);

                return $imageMock;
            });

        $result = $this->resizer->resize($uploadedFile, $maxWidth, $maxHeight);

        // Assert
        $this->assertEquals((string)$imageMock, $result);
    }
}
