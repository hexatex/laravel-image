<?php

namespace Hexatex\LaravelImage\Tests\Unit\Image;

use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\ImagePolicy;
use Hexatex\LaravelImage\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelUser\Factories\UserFactory;
use Hexatex\LaravelUser\User\Contracts\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Mockery;

class ImagePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $unownedImage;
    protected $ownedImage;
    protected $userWrongType;
    protected $wrongUserTypeImage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->make();
        $this->user = Mockery::mock($this->user)->makePartial();
        $this->user->id = 1000;

        $this->otherUser = UserFactory::new()->make();
        $this->otherUser = Mockery::mock($this->otherUser)->makePartial();
        $this->otherUser->id = $this->user->id + 20;

        $this->unownedImage = ImageFactory::new()->make();
        $this->unownedImage->author()->associate($this->otherUser);
        $this->unownedImage = Mockery::mock($this->unownedImage)->makePartial();

        $this->ownedImage = ImageFactory::new()->make();
        $this->ownedImage->author()->associate($this->user);
        $this->ownedImage = Mockery::mock($this->ownedImage)->makePartial();

        $this->userWrongType = UserFactory::new()->make();
        $this->userWrongType = Mockery::mock($this->userWrongType)->makePartial();
        $this->userWrongType->id = $this->user->id;
        $this->userWrongType->type = $this->user->type . '-unknown-type';

        $this->wrongUserTypeImage = ImageFactory::new()->make();
        $this->wrongUserTypeImage->author()->associate($this->userWrongType);
        $this->wrongUserTypeImage = Mockery::mock($this->wrongUserTypeImage)->makePartial();        
    }

    public function testView_withUser()
    {
        // Arrange
        $policy = new ImagePolicy($this->user, $this->unownedImage);

        // Act
        $result = $policy->view();

        // Assert
        $this->assertTrue($result);
    }

    public function testView_withoutUser()
    {
        // Arrange
        $policy = new ImagePolicy(null, $this->unownedImage);

        // Act
        $result = $policy->view();

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_withoutUser()
    {
        $this->testUpdateOrDestroy_withoutUser('update');
    }

    public function testUpdate_withPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: true,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_noPermission_hasOwnership_hasOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: true,
            userTypeMatches: true,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_noPermission_hasOwnership_noOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: true,
            userTypeMatches: true,
            hasPermission: false,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_noOwnership_hasOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_noOwnership_noOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_hasOwnershipPermission_userTypeNotMatching()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'update',
            userIdMatches: true,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_withoutUser()
    {
        $this->testUpdateOrDestroy_withoutUser('destroy');
    }

    public function testDestroy_withPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: true,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_noPermission_hasOwnership_hasOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: true,
            userTypeMatches: true,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_noPermission_hasOwnership_noOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: true,
            userTypeMatches: true,
            hasPermission: false,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_noOwnership_hasOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_noOwnership_noOwnershipPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: false,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_hasOwnershipPermission_userTypeNotMatching()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            'destroy',
            userIdMatches: true,
            userTypeMatches: false,
            hasPermission: false,
            hasOwnershipPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy()
    {
        // Arrange
        $policy = new ImagePolicy(null, $this->unownedImage);

        // Act
        $result = $policy->download();

        // Assert
        $this->assertTrue($result);
    }

    /*
     * Protected Methods
     */
    protected function testUpdateOrDestroy_withoutUser(string $permission)
    {
        // Arrange
        $policy = new ImagePolicy(null, $this->ownedImage);
        $policy = Mockery::mock($policy)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Act
        $policy->shouldReceive($permission)
            ->once()
            ->passthru();

        $policy->shouldReceive('hasPermissionOrOwnership')
            ->with($permission)
            ->once()
            ->passthru();

        $policy->shouldNotReceive('userOwnsImage');

        $result = $policy->{$permission}();

        // Assert
        $this->assertFalse($result);
    }

    protected function testUpdateOrDestroy(
        string $permission,
        bool $userIdMatches,
        bool $userTypeMatches,
        bool $hasPermission,
        bool $hasOwnershipPermission,
    ) {
        // Arrange
        $ownsImage = $userIdMatches && $userTypeMatches;
        $image = $ownsImage ? $this->ownedImage : $this->unownedImage;

        if ($userIdMatches && !$userTypeMatches) {
            $image = $this->wrongUserTypeImage; // Use an image owned by a user of the same id but a different type, not the User model.
        }

        $policy = Mockery::mock(ImagePolicy::class, [$this->user, $image])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Act
        $policy
            ->shouldReceive($permission)
            ->once()
            ->passthru();

        $policy
            ->shouldReceive('hasPermissionOrOwnership')
            ->with($permission)
            ->once()
            ->passthru();

        $this->user
            ->shouldReceive('hasPermissionTo')
            ->with('image.' . $permission)
            ->once()
            ->andReturn($hasPermission);

        if ($hasPermission) {
            $policy->shouldNotReceive('userOwnsImage');
            $this->user->shouldNotReceive('getMorphClass');
        } else {
            $policy
                ->shouldReceive('userOwnsImage')
                ->once()
                ->passthru();

            if ($userTypeMatches) {
                $this->user
                    ->shouldReceive('getMorphClass')
                    ->twice()
                    ->andReturn('laravel-user');
            } else {
                $this->user
                    ->shouldReceive('getMorphClass')
                    ->andReturn('laravel-user');
                $this->userWrongType
                    ->shouldReceive('getMorphClass')
                    ->andReturn('laravel-user-unknown-type');
            }

            if ($ownsImage) {
                $this->user
                    ->shouldReceive('hasPermissionTo')
                    ->with("image.{$permission}-owned")
                    ->once()
                    ->andReturn($hasOwnershipPermission);
            }
        }

        $result = $policy->{$permission}();

        return $result;
    }
}
