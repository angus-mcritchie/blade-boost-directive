<?php

namespace AngusMcritchie\BladeBoostDirective\Tests;

use AngusMcritchie\BladeBoostDirective\BoostServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BoostServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
