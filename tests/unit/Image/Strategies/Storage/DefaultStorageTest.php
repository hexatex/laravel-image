<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image\Strategies\Storage;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Strategies\Resizer\ResizerFactory;
use Hexatex\LaravelImage\Image\Strategies\Storage\DefaultStorage;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use \InvalidArgumentException;

class DefaultStorageTest extends TestCase
{
    protected $localStorage;

    protected function setUp(): void
    {
        parent::setUp();

        $resizerFactory = $this->createMock(ResizerFactory::class);

        $this->localStorage = new DefaultStorage($resizerFactory);
        $this->mockStorage = Storage::fake(StorageDisk::local->value);
    }

    public function testSetDisk()
    {
        $newDisk = 'new-disk-name';

        // Act
        $this->localStorage->setDisk($newDisk);

        // Assert
        $this->assertEquals($newDisk, $this->localStorage->getDisk());
        $this->localStorage->setDisk('local');
    }

    public function testDeleteImageFiles()
    {
        // Arrange
        $image = ImageFactory::new()->make();
        $image->path = 'test-image.jpg'; // Assuming the path is 'test-image.jpg'
        $image->thumb_path = 'test-image-thumb.jpg'; // Assuming the thumb_path is 'test-image-thumb.jpg'

        // Mock the StorageFacade for the 'local' disk
        $this->mockStorage->put($image->path, 'contents'); // Add a dummy file to be deleted
        $this->mockStorage->put($image->thumb_path, 'contents'); // Add a dummy thumb file to be deleted
        Storage::shouldReceive('disk')->with('local')->andReturn($this->mockStorage);

        // Act
        $this->localStorage->deleteImageFiles($image);

        // Assert
        $this->assertFalse($this->mockStorage->exists($image->path));
        $this->assertFalse($this->mockStorage->exists($image->thumb_path));
    }

    public function testDeleteFile()
    {
        // Arrange
        $filePath = 'test-file.jpg'; // Replace with the actual file path

        $this->mockStorage->put($filePath, 'contents'); // Add a dummy file to be deleted
        Storage::shouldReceive('disk')->with('local')->andReturn($this->mockStorage);
        $method = $this->getInaccessibleMethod($this->localStorage, 'deleteFile');

        // Act
        $method->invoke($this->localStorage, $filePath);

        // Assert
        $this->assertFalse($this->mockStorage->exists($filePath));
    }

    public function testSaveImageFileWithInvalidFile()
    {
        // Arrange
        $fill = ['file' => 'invalid-file']; // Invalid file data

        // Expect an InvalidArgumentException to be thrown with the specified message
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The $fill array must contain a valid \'file\' key of type UploadedFile.');

        // Act
        $this->localStorage->saveImageFiles($fill);

        // Assert (This code should not be reached due to the exception)
        $this->fail('An exception should have been thrown.');
    }

    public function testSaveImageFileWithEmptyFile()
    {
        // Arrange
        $fill = ['file' => null]; // Empty file data

        // Expect an InvalidArgumentException to be thrown with the specified message
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The $fill array must contain a valid \'file\' key of type UploadedFile.');

        // Act
        $this->localStorage->saveImageFiles($fill);

        // Assert (This code should not be reached due to the exception)
        $this->fail('An exception should have been thrown.');
    }

    public function testSaveImageFile()
    {
        // Arrange
        $fill = ['alt' => 'Test Alt', 'file' => UploadedFile::fake()->image('test.jpg')];
        $uploadedFile = UploadedFile::fake()->image('test-image.jpg');
        $expectedPath = 'images/test-image.jpg';

        // Act
        $result = $this->localStorage->saveImageFiles($fill, $uploadedFile);

        // Assert
        $this->assertNotNull($result);
        $this->assertNotNull($result->path);
    }

    // public function testDeleteImageFile()
    // {
    //     // Arrange
    //     $image = ImageFactory::new()->make();
    //     Storage::disk('local')->put($image->path, UploadedFile::fake()->image('test.jpg'));
    //     Storage::disk('local')->put($image->thumb_path, UploadedFile::fake()->image('test.jpg'));

    //     // Act
    //     $this->localStorage->deleteImageFiles($image);

    //     // Assert
    //     $this->assertFalse(Storage::disk('local')->exists($image->path));
    //     $this->assertFalse(Storage::disk('local')->exists($image->thumb_path));
    // }
}
