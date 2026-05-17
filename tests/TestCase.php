<?php

namespace Hexatex\LaravelImage\Tests;

use Hexatex\LaravelImage\LaravelImageServiceProvider;
use Hexatex\LaravelMisc\AdminAuthenticatableResources;
use Hexatex\LaravelMisc\WebAuthenticatableResources;
use Hexatex\LaravelUser\LaravelUserServiceProvider;
use Hexatex\LaravelUser\User\Resources\Admin\UserResource as AdminUserResource;
use Hexatex\LaravelUser\User\Resources\Web\UserResource;
use Hexatex\LaravelUser\User\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as Orchestra;
use \ReflectionClass;

abstract class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            LaravelImageServiceProvider::class,
            LaravelUserServiceProvider::class,
            \Spatie\Permission\PermissionServiceProvider::class,
            \Laravel\Passport\PassportServiceProvider::class,
            \Intervention\Image\ImageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:jqAF7i3K4aZ+6h/jN/jl9ZQRCn1c3LPGACxe+x7FpBQ=');
    }

    protected function getInaccessibleMethod($class, string $methodName)
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    protected function setUp(): void
    {
        parent::setUp();

        AdminAuthenticatableResources::register(User::class, AdminUserResource::class);
        WebAuthenticatableResources::register(User::class, UserResource::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
