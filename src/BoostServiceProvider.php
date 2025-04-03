<?php

namespace AngusMcritchie\BladeBoostDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Blade Boost Directive package.
 *
 * This provider registers the @boost and @endboost Blade directives
 * and handles the package configuration.
 */
class BoostServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('blade-boost-directive')
            ->hasConfigFile('blade-boost-directive');
    }

    /**
     * Boot the package services.
     *
     * @return void
     */
    public function packageBooted()
    {
        Blade::directive('boost', fn(string $expression) => Boost::open($expression));
        Blade::directive('endboost', fn() => Boost::close());
    }
}
