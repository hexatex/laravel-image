<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageRepository;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\ImageService;
use Hexatex\LaravelImage\Image\Strategies\Storage\Storage;
use Hexatex\LaravelImage\Image\Strategies\Storage\StorageFactory;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use \Mockery;

class ImageServiceTest extends TestCase
{
    protected $imageRepository;
    protected $storage;
    protected $storageFactory;
    protected $imageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->imageRepository = $this->mock(ImageRepository::class);
        $this->storageFactory = $this->mock(StorageFactory::class);
        $this->storage = $this->mock(Storage::class);

        $this->imageService = new ImageService($this->imageRepository, $this->storageFactory);
    }

    public function testIndex()
    {
        // Arrange
        $filters = [
            'alt' => 'Test Alt',
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => 10,
            'sortBy' => 'created_at',
        ];

        $imageMock = $this->mockImage();
        $collection = new Collection([$imageMock]);

        // Act
        $this->imageRepository
            ->shouldReceive('index')
            ->once()
            ->with($filters)
            ->andReturn($collection);

        $result = $this->imageService->index($filters);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($imageMock));
        $this->assertEquals($imageMock, $result->first());
    }

    public function testStore()
    {
        // Arrange
        $fill = ['alt' => 'Test Alt', 'file' => UploadedFile::fake()->image('test-service.jpg')];
        $imagePathDto = $this->createImagePathDto();
        $author = Mockery::mock(Authenticatable::class);

        // Act
        $this->storageFactory
            ->shouldReceive('create')
            ->with($fill)
            ->andReturn($this->storage);

        $this->storage
            ->shouldReceive('saveImageFiles')
            ->once()
            ->with($fill)
            ->andReturn($imagePathDto);

        $this->imageRepository
            ->shouldReceive('store')
            ->once()
            ->with($fill, $imagePathDto, $author)
            ->andReturn($this->mockImage());

        $result = $this->imageService->store($fill, $author);

        // Assert
        $this->assertInstanceOf(Image::class, $result);
    }

    public function testUpdateWithFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt', 'file' => UploadedFile::fake()->image('test-service-updated.jpg')];
        $image = $this->mockImage();
        $imagePathDto = $this->createImagePathDto();

        // Act
        $this->storageFactory
            ->shouldReceive('create')
            ->with($fill)
            ->andReturn($this->storage);

        $this->storage
            ->shouldReceive('saveImageFiles')
            ->once()
            ->with($fill)
            ->andReturn($imagePathDto);

        $this->imageRepository
            ->shouldReceive('update')
            ->once()
            ->with($fill, $image, $imagePathDto);

        $result = $this->imageService->update($fill, $image);

        // Assert
        $this->assertNull($result);
    }

    public function testUpdateWithOutFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt'];
        $image = $this->mockImage();
        $imagePathDto = $this->createImagePathDto();

        // Act
        $this->storageFactory
            ->shouldReceive('create')
            ->with($fill)
            ->andReturn($this->storage);

        $this->storage
            ->shouldNotReceive('saveImageFiles');

        $this->imageRepository
            ->shouldReceive('update')
            ->once()
            ->with($fill, $image, null);

        $result = $this->imageService->update($fill, $image);

        // Assert
        $this->assertNull($result);
    }

    public function testUpdateWithNullFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt', 'file' => null];
        $image = $this->mockImage();
        $imagePathDto = $this->createImagePathDto();

        // Act
        $this->storageFactory
            ->shouldReceive('create')
            ->with($fill)
            ->andReturn($this->storage);

        $this->storage
            ->shouldNotReceive('saveImageFiles');

        $this->imageRepository
            ->shouldReceive('update')
            ->once()
            ->with($fill, $image, null);

        $result = $this->imageService->update($fill, $image);

        // Assert
        $this->assertNull($result);
    }

    public function testDestroy()
    {
        // Arrange
        $image = $this->mockImage();

        // Act
        $this->storageFactory
            ->shouldReceive('create')
            ->with(null, $image)
            ->andReturn($this->storage);

        $this->storage
            ->shouldReceive('deleteImageFiles')
            ->with($image)
            ->once()
            ->andReturnNull();

        $this->imageRepository
            ->shouldReceive('destroy')
            ->once()
            ->with($image)
            ->andReturnNull();

        $result = $this->imageService->destroy($image);

        // Assert
        $this->assertNull($result);
    }

    protected function mockImage()
    {
        return $this->mock(Image::class);
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
}
