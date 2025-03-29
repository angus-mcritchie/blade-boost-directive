<?php

namespace AngusMcRitchie\BladeRepeatedDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeRepeatedDirectiveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('blade-repeated-directive');
    }

    public function packageBooted()
    {
        Blade::directive('repeated', fn(string $expression) => BladeRepeatedDirective::open($expression));
        Blade::directive('endrepeated', fn() => BladeRepeatedDirective::close());
    }
}
