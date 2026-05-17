<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image;

use Hexatex\LaravelImage\Image\Builders\BuilderFactory;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Image as ConcreteImage;
use Hexatex\LaravelImage\Image\Dtos\ImagePathDto;
use Hexatex\LaravelImage\Image\ImageRepository;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;
use \Mockery;

class ImageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $imageRepository;
    protected $builderFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builderFactory = $this->mock(BuilderFactory::class);
        $this->imageRepository = $this->getMockBuilder(ImageRepository::class)
            ->onlyMethods(['getModel', 'create'])
            ->setConstructorArgs([$this->builderFactory])
            ->getMock();
    }

    public function testIndex_hasRowsPerPage()
    {
        // Arrange
        $rowsPerPage = 10;
        $filters = [
            'alt' => 'Test Alt',
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => $rowsPerPage,
            'sortBy' => 'created_at',
        ];

        // Act and Assert
        $this->testIndex($filters);
    }

    public function testIndex_nullRowsPerPage()
    {
        // Arrange
        $filters = [
            'alt' => 'Test Alt',
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => null,
            'sortBy' => 'created_at',
        ];

        // Act and Assert
        $this->testIndex($filters);
    }

    public function testIndex_noRowsPerPage()
    {
        // Arrange
        $filters = [
            'alt' => 'Test Alt',
            'descending' => true,
            'page' => 1,
            'sortBy' => 'created_at',
        ];

        // Act and Assert
        $this->testIndex($filters);
    }

    public function testStore()
    {
        // Arrange
        $fill = ['alt' => 'Test Alt', 'file' => UploadedFile::fake()->image('test-repository.jpg')];
        $imagePathDto = $this->createImagePathDto();
        $image = $this->mockImage();
        $builder = $this->mockImageBuilder();
        $author = Mockery::mock(Authenticatable::class);

        // Act
        $this->imageRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($image);

        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $image)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forStore')
            ->once()
            ->with($imagePathDto, $author);

        $image
            ->shouldReceive('save')
            ->once();

        $result = $this->imageRepository->store($fill, $imagePathDto, $author);

        // Assert
        $this->assertInstanceOf(Image::class, $result);
    }

    public function testUpdate_withFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt', 'file' => UploadedFile::fake()->image('test-repository-updated.jpg')];

        // Act and Assert
        $this->testUpdate($fill);
    }

    public function testUpdate_nullFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt', 'file' => null];

        // Act and Assert
        $this->testUpdate($fill);
    }

    public function testUpdate_noFile()
    {
        // Arrange
        $fill = ['alt' => 'Updated Alt', 'file' => null];

        // Act and Assert
        $this->testUpdate($fill);
    }

    public function testDestroy()
    {
        // Arrange
        $image = $this->mockImage();

        // Act
        $image
            ->shouldReceive('delete')
            ->once();

        $result = $this->imageRepository->destroy($image);

        // Assert
        $this->assertNull($result);
    }

    public function testGetModel()
    {
        // Arrange
        $repository = new ImageRepository($this->builderFactory);
        $method = $this->getInaccessibleMethod($repository, 'getModel');

        // Act
        $result = $method->invoke($repository);

        // Assert
        $this->assertEquals(ConcreteImage::class, $result);
    }

    public function testCreate()
    {
        // Arrange
        $repository = $this->getMockBuilder(ImageRepository::class)
            ->onlyMethods(['getModel'])
            ->setConstructorArgs([$this->builderFactory])
            ->getMock();

        $method = $this->getInaccessibleMethod($repository, 'create');

        // Act
        $repository
            ->expects($this->once())
            ->method('getModel')
            ->willReturn(ConcreteImage::class);

        $result = $method->invoke($repository);

        // Assert
        $this->assertInstanceOf(Image::class, $result);
    }

    /*
     * Protected Methods
     */
    protected function testIndex(array $filters)
    {
        // Arrange
        $image = $this->mockImage();
        $queryBuilder = $this->mock(\Illuminate\Database\Eloquent\Builder::class);
        $expectedCollection = new Collection([$image]);
        $expectedLengthAwarePaginator = new ConcreteLengthAwarePaginator(
            $expectedCollection,
            $expectedCollection->count(),
            $filters['rowsPerPage'] ?? config('image.default-rows-per-page'),
            $filters['page']
        );

        // Act
        $this->imageRepository
            ->expects($this->once())
            ->method('getModel')
            ->willReturn(get_class($image));

        $image
            ->shouldReceive('filter')
            ->once()
            ->with($filters)
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('paginate')
            ->once()
            ->with($filters['rowsPerPage'] ?? config('image.default-rows-per-page'))
            ->andReturn($expectedLengthAwarePaginator);

        $result = $this->imageRepository->index($filters);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($image));
        $this->assertEquals($image, $result->first());
    }

    protected function testUpdate(array $fill)
    {
        // Arrange
        $image = $this->mockImage();
        $imagePathDto = $this->createImagePathDto();
        $builder = $this->mockImageBuilder();

        // Act
        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $image)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forUpdate')
            ->once()
            ->with($imagePathDto);

        $image
            ->shouldReceive('save')
            ->once();

        $result = $this->imageRepository->update($fill, $image, $imagePathDto);

        // Assert
        $this->assertNull($result);
    }

    protected function mockImage()
    {
        return $this->mock(Image::class);
    }

    protected function mockImageBuilder()
    {
        return $this->mock(\Hexatex\LaravelImage\Image\Builders\Builder::class);
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
