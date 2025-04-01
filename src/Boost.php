<?php

namespace AngusMcritchie\BladeBoostDirective;

class Boost
{
    public static function open(string $expression): string
    {
        return <<<PHP
            <?php
                \$__boostDirectiveArguments = [{$expression}];
                if (!(\$__boostDirectiveKey = \$__boostDirectiveArguments[0] ?? null)) {
                    throw new \InvalidArgumentException('The name of the cache key is required.');
                }
                \$__boostDirectiveKey = \AngusMcritchie\BladeBoostDirective\Boost::prefix(\$__boostDirectiveKey);
                \$__boostDirectiveReplacements = \$__boostDirectiveArguments[1] ?? null;
                \$__boostDirectiveStore = \$__boostDirectiveArguments[2] ?? config()->get('blade-boost-directive.default_cache_store');
                \$__boostDirectiveCallback = (fn(array \$__boostDirectiveArguments) => function () use (\$__boostDirectiveArguments) {
                    extract(\$__boostDirectiveArguments, EXTR_SKIP);
                    ob_start();
            ?>
        PHP;
    }

    public static function close(): string
    {
        return <<<PHP
            <?php
                    return new \Illuminate\Support\HtmlString(ob_get_clean());
                })(get_defined_vars());

                if (\$__boostDirectiveReplacements) {
                    echo str_replace(array_keys(\$__boostDirectiveReplacements), \$__boostDirectiveReplacements, \Illuminate\Support\Facades\Cache::store(\$__boostDirectiveStore)->rememberForever(\$__boostDirectiveKey, \$__boostDirectiveCallback));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store(\$__boostDirectiveStore)->rememberForever(\$__boostDirectiveKey, \$__boostDirectiveCallback);
                }

                unset(
                    \$__boostDirectiveKey,
                    \$__boostDirectiveReplacements,
                    \$__boostDirectiveCallback,
                    \$__boostDirectiveArguments,
                    \$__boostDirectiveStore
                );
            ?>
        PHP;
    }

    public static function prefix(string $key): string
    {
        return config('blade-boost-directive.prefix') . $key;
    }
}
