<?php

namespace AngusMcritchie\BladeRepeatedDirective;

use RyanChandler\BladeCaptureDirective\BladeCaptureDirective;

class BladeRepeatedDirective
{
    public static function open(string $expression): string
    {
        \Illuminate\Support\Facades\Cache::store('array')->increment('repeated');

        if (str_contains($expression, ',')) {
            $name = substr($expression, 0, strpos($expression, ','));
            $replacements = substr($expression, strpos($expression, ',') + 1);
            $replacements = trim($replacements, ',');
        } else {
            $name = $expression;
            $replacements = 'null';
        }

        $callbackVariable = '$__repeated'.\Illuminate\Support\Facades\Cache::store('array')->get('repeated');
        $output = "<?php \$__repeatKey = {$name}; ?>";
        $output .= "<?php \$__repeatReplacements = {$replacements}; ?>";
        $output .= BladeCaptureDirective::open($callbackVariable);

        return $output;
    }

    public static function close(): string
    {
        $output = BladeCaptureDirective::close();
        $callbackVariable = '$__repeated'.\Illuminate\Support\Facades\Cache::store('array')->get('repeated');

        $output .= '<?php if($__repeatReplacements) : ?>';
        $output .= "<?php echo str_replace(array_keys(\$__repeatReplacements), \$__repeatReplacements, \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatKey, {$callbackVariable})); ?>";
        $output .= '<?php else : ?>';
        $output .= "<?php echo \Illuminate\Support\Facades\Cache::store('array')->rememberForever(\$__repeatKey, {$callbackVariable}); ?>";
        $output .= '<?php endif; ?>';

        $output .= '<?php unset($__repeatKey); ?>';
        $output .= '<?php unset($__repeatReplacements); ?>';
        $output .= "<?php unset({$callbackVariable}); ?>";

        \Illuminate\Support\Facades\Cache::store('array')->decrement('repeated');

        return $output;
    }
}
