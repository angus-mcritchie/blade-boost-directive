<?php

namespace AngusMcritchie\BladeRepeatedDirective;

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

        return <<<PHP
            <?php
                \$__repeatKey = {$name};
                \$__repeatReplacements = {$replacements};

                \$__repeated = (function (\$args) {
                    return function () use (\$args) {
                        extract(\$args, EXTR_SKIP);
                        ob_start();
            ?>
        PHP;
    }

    public static function close(): string
    {
        return <<<PHP
            <?php return new \Illuminate\Support\HtmlString(ob_get_clean()); };
                })(get_defined_vars());

                if(\$__repeatReplacements) {
                    echo str_replace(array_keys(\$__repeatReplacements), \$__repeatReplacements, \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatKey, \$__repeated));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatKey, \$__repeated);
                }

                unset(\$__repeatKey);
                unset(\$__repeatReplacements);
                unset(\$__repeated);
            ?>
        PHP;
    }
}
