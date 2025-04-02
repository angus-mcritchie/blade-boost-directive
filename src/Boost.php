<?php

namespace AngusMcritchie\BladeBoostDirective;

class Boost
{
    public static function open(string $expression): string
    {
        return <<<PHP
            <?php
                \$__boostDirectiveArguments = [{$expression}];

                if (isset(\$__boostDirectiveArguments[1])) {
                    \$__boostDirectiveArguments[1]['key'] = \$__boostDirectiveArguments[0];
                    \$__boostDirectiveArguments = \$__boostDirectiveArguments[1];
                }

                if(isset(\$__boostDirectiveArguments[0]) && is_string(\$__boostDirectiveArguments[0])) {
                    \$__boostDirectiveArguments = ['key' => \$__boostDirectiveArguments[0]];
                }

                if (isset(\$__boostDirectiveArguments[0]['key'])) {
                    \$__boostDirectiveArguments = \$__boostDirectiveArguments[0];
                }

                \$__boostDirectiveKey = \$__boostDirectiveArguments['key'] ?? null;

                if (!\$__boostDirectiveKey || (!is_string(\$__boostDirectiveKey) && !is_array(\$__boostDirectiveKey))) {
                    throw new \InvalidArgumentException('@boost directive requires `key` to be defined as the first argument or as [\'key\' => \'my-key\'] in the options array and must be a string or an array.');
                }

                if(is_array(\$__boostDirectiveKey)) {
                    \$__boostDirectiveKey = implode('.', \$__boostDirectiveKey);
                }

                \$__boostDirectiveKey = \AngusMcritchie\BladeBoostDirective\Boost::prefix(\$__boostDirectiveKey);
                \$__boostDirectiveStore = \$__boostDirectiveArguments['store'] ?? config()->get('blade-boost-directive.default_cache_store');
                \$__boostDirectiveReplace = \$__boostDirectiveArguments['replace'] ?? null;

                if(\$__boostDirectiveReplace && !(\$__boostDirectiveArguments['raw'] ?? false)) {
                    \$__boostDirectiveReplace = array_map('e', \$__boostDirectiveReplace);
                }

                unset(\$__boostDirectiveArguments);

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

                if (\$__boostDirectiveReplace) {
                    echo str_replace(array_keys(\$__boostDirectiveReplace), \$__boostDirectiveReplace, \Illuminate\Support\Facades\Cache::store(\$__boostDirectiveStore)->rememberForever(\$__boostDirectiveKey, \$__boostDirectiveCallback));
                } else {
                    echo \Illuminate\Support\Facades\Cache::store(\$__boostDirectiveStore)->rememberForever(\$__boostDirectiveKey, \$__boostDirectiveCallback);
                }

                unset(
                    \$__boostDirectiveKey,
                    \$__boostDirectiveStore,
                    \$__boostDirectiveReplace,
                    \$__boostDirectiveCallback
                );
            ?>
        PHP;
    }

    public static function prefix(string $key): string
    {
        return config('blade-boost-directive.prefix').$key;
    }
}
