<?php

namespace AngusMcritchie\BladeRepeatedDirective;

class BladeRepeatedDirective
{
    public static function open(string $expression): string
    {
        return <<<PHP
            <?php
                \$__repeatedDirectiveArguments = [{$expression}];
                if (!(\$__repeatedDirectiveKey = \$__repeatedDirectiveArguments[0] ?? null)) {
                    throw new \InvalidArgumentException('The name of the cache key is required.');
                }
                \$__repeatedDirectiveReplacements = \$__repeatedDirectiveArguments[1] ?? null;
                \$__repeatedDirectiveStore = \$__repeatedDirectiveArguments[2] ?? 'array';
                \$__repeatedDirectiveCallback = (function (\$arguments) {
                    return function () use (\$arguments) {
                        extract(\$arguments, EXTR_SKIP);
                        ob_start();
            ?>
        PHP;
    }

    public static function close(): string
    {
        return <<<PHP
            <?php return new \Illuminate\Support\HtmlString(ob_get_clean()); };
                })(get_defined_vars());

                if (\$__repeatedDirectiveReplacements) {
                    echo str_replace(array_keys(\$__repeatedDirectiveReplacements), \$__repeatedDirectiveReplacements, \Illuminate\Support\Facades\Cache::store(\$__repeatedDirectiveStore)->rememberForever(\$__repeatedDirectiveKey, \$__repeatedDirectiveCallback));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store(\$__repeatedDirectiveStore)->rememberForever(\$__repeatedDirectiveKey, \$__repeatedDirectiveCallback);
                }

                unset(\$__repeatedDirectiveKey);
                unset(\$__repeatedDirectiveReplacements);
                unset(\$__repeatedDirectiveCallback);
                unset(\$__repeatedDirectiveArguments);
                unset(\$__repeatedDirectiveStore);
            ?>
        PHP;
    }
}
