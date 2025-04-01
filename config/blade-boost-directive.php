<?php

return [
    /**
     * Enable or disable the package.
     * If disabled, the package will not register any Blade directives.
     */
    'enabled' => env('BLADE_BOOST_ENABLED', true),

    /**
     * The default cache store to use.
     * This is used when no cache store is specified in the directive.
     */
    'default_cache_store' => env('BLADE_BOOST_DIRECTIVE_DEFAULT_CACHE_STORE', 'array'),

    /**
     * The prefix to use for cache keys.
     * This is used to avoid key collisions with other packages or parts of your application.
     */
    'prefix' => env('BLADE_BOOST_DIRECTIVE_PREFIX', 'blade-boost-directive.'),
];
