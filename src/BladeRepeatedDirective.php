<?php

namespace AngusMcritchie\BladeRepeatedDirective;

use InvalidArgumentException;

class BladeRepeatedDirective
{
    public static function open(string $expression): string
    {
        if (str_contains($expression, ',')) {
            $name = substr($expression, 0, strpos($expression, ','));
            $replacements = substr($expression, strpos($expression, ',') + 1);
            $replacements = trim($replacements, ',');
        } else {
            $name = $expression;
            $replacements = 'null';
        }

        if (! $name) {
            throw new InvalidArgumentException('The name of the cache key is required.');
        }

        return <<<PHP
            <?php
                \$__repeatedKey = {$name};
                \$__repeatedVariables = {$replacements};

                \$__repeatedCallback = (function (\$arguments) {
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

                if(\$__repeatedVariables) {
                    echo str_replace(array_keys(\$__repeatedVariables), \$__repeatedVariables, \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatedKey, \$__repeatedCallback));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatedKey, \$__repeatedCallback);
                }

                unset(\$__repeatedKey);
                unset(\$__repeatedVariables);
                unset(\$__repeatedCallback);
            ?>
        PHP;
    }
}
