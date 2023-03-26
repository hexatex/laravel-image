<?php

namespace Hexatex\LaravelImage;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Hexatex\LaravelImage\Commands\LaravelImageCommand;

class LaravelImageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-image')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-image_table')
            ->hasCommand(LaravelImageCommand::class);
    }
}
