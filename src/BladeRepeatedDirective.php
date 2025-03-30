<?php

namespace AngusMcritchie\BladeRepeatedDirective;

use InvalidArgumentException;

class BladeRepeatedDirective
{
    public static function open(string $expression): string
    {
        $arguments = array_map('trim', explode(',', $expression));
        $name = $arguments[0] ?? null;
        $replacements = $arguments[1] ?? 'null';
        $store = $arguments[2] ?? "'array'";

        if (!$name) {
            throw new InvalidArgumentException('The name of the cache key is required.');
        }

        return <<<PHP
            <?php
                \$__repeatedDirectiveKey = {$name};
                \$__repeatedDirectiveVariables = {$replacements};
                \$__repeatedDirectiveStore = {$store};
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

                if (\$__repeatedDirectiveVariables) {
                    echo str_replace(array_keys(\$__repeatedDirectiveVariables), \$__repeatedDirectiveVariables, \Illuminate\Support\Facades\Cache::store(\$__repeatedDirectiveStore)->rememberForever(\$__repeatedDirectiveKey, \$__repeatedDirectiveCallback));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store(\$__repeatedDirectiveStore)->rememberForever(\$__repeatedDirectiveKey, \$__repeatedDirectiveCallback);
                }

                unset(\$__repeatedDirectiveKey);
                unset(\$__repeatedDirectiveVariables);
                unset(\$__repeatedDirectiveCallback);
            ?>
        PHP;
    }
}
