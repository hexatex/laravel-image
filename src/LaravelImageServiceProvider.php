<?php

namespace Hexatex\LaravelImage;

use Hexatex\LaravelImage\Image\Builders\Builder;
use Hexatex\LaravelImage\Image\Builders\BuilderFactory;
use Hexatex\LaravelImage\Image\Builders\DefaultBuilder;
use Hexatex\LaravelImage\Image\Builders\DefaultBuilderFactory;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageRepository;
use Hexatex\LaravelImage\Image\Contracts\ImageService;
use Hexatex\LaravelImage\Image\Image as ConcreteImage;
use Hexatex\LaravelImage\Image\ImageRepository as ConcreteImageRepository;
use Hexatex\LaravelImage\Image\ImageService as ConcreteImageService;
use Hexatex\LaravelImage\Image\Strategies\Resizer\DefaultResizer;
use Hexatex\LaravelImage\Image\Strategies\Resizer\DefaultResizerFactory;
use Hexatex\LaravelImage\Image\Strategies\Resizer\Resizer;
use Hexatex\LaravelImage\Image\Strategies\Resizer\ResizerFactory;
use Hexatex\LaravelImage\Image\Strategies\Storage\DefaultStorage;
use Hexatex\LaravelImage\Image\Strategies\Storage\Storage;
use Hexatex\LaravelImage\Image\Strategies\Storage\DefaultStorageFactory;
use Hexatex\LaravelImage\Image\Strategies\Storage\StorageFactory;
use Hexatex\LaravelMisc\Contracts\Enums\StorageDisk as StorageDiskContract;
use Hexatex\LaravelMisc\Enums\StorageDisk;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelImageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        Route::macro('bindImageModel', function () {
            Route::model('image', ConcreteImage::class);
        });

        // $kernel = $this->app->make(HttpKernel::class);
        // $kernel->addMiddlewareAlias('signed', \Illuminate\Routing\Middleware\ValidateSignature::class);
        Route::aliasMiddleware('signed', \Illuminate\Routing\Middleware\ValidateSignature::class);
        Relation::enforceMorphMap(['laravel-image' => ConcreteImage::class]);

        // $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'hexatex');
        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'hexatex');
        $this->loadRoutesFrom(__DIR__ . '/../routes/image.php');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
            $this->bootForConsole();

            if ($this->app->environment('testing')) {
                Relation::enforceMorphMap([
                    'test-imageable' => TestImageable::class,
                ]);

                $this->loadMigrationsFrom(__DIR__.'/../database/migrations/testing');
            }
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/image.php', 'image');
        $this->app->bind(Builder::class, DefaultBuilder::class);
        $this->app->bind(BuilderFactory::class, DefaultBuilderFactory::class);
        $this->app->bind(Image::class, ConcreteImage::class);
        $this->app->bind(ImageRepository::class, ConcreteImageRepository::class);
        $this->app->bind(ImageService::class, ConcreteImageService::class);
        $this->app->bind(Resizer::class, DefaultResizer::class);
        $this->app->bind(ResizerFactory::class, DefaultResizerFactory::class);
        $this->app->bind(Storage::class, DefaultStorage::class);
        $this->app->bind(StorageFactory::class, DefaultStorageFactory::class);
        $this->app->bind(StorageDiskContract::class, function () {
            switch (config('image.storage-disk')) {
                case 's3':
                    return StorageDisk::s3;
                case 'local':
                default:
                    return StorageDisk::local;
            }
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Builder::class,
            BuilderFactory::class,
            Image::class,
            ImageRepository::class,
            ImageService::class,
            Storage::class,
            StorageDisk::class,
        ];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/image.php' => config_path('image.php'),
        ], 'laravel-image.config');

        // Publishing the routes.
        $this->publishes([
            __DIR__ . '/../routes/image.php' => config_path('routes/image.php'),
        ], 'laravel-image.routes');

        // Publishing the views.
        /*$this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/hexatex'),
        ], 'laravel-image.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/hexatex'),
        ], 'laravel-image.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/hexatex'),
        ], 'laravel-image.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
