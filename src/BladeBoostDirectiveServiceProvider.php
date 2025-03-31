<?php

namespace AngusMcritchie\BladeBoostDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeBoostDirectiveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('blade-boost-directive');
    }

    public function packageBooted()
    {
        Blade::directive('boost', fn(string $expression) => BladeBoostDirective::open($expression));
        Blade::directive('endboost', fn() => BladeBoostDirective::close());
    }
}
