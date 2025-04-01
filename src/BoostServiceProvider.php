<?php

namespace AngusMcritchie\BladeBoostDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BoostServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('blade-boost-directive')
            ->hasConfigFile('blade-boost-directive');
    }

    public function packageBooted()
    {
        if (!config()->get('blade-boost-directive.enabled')) {
            Blade::directive('boost', fn() => null);
            Blade::directive("endboost", fn() => null);
            return;
        }

        Blade::directive('boost', fn(string $expression) => Boost::open($expression));
        Blade::directive("endboost", fn() => Boost::close());
    }
}
