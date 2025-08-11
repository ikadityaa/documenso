<?php

namespace App\Support;

class EnvWriter
{
    public static function setValues(array $map): void
    {
        $envPath = base_path('.env');
        $content = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($map as $key => $value) {
            $value = self::formatValue($value);
            $pattern = "/^".preg_quote($key, '/')."=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $key.'='.$value, $content);
            } else {
                $content .= (strlen($content) ? PHP_EOL : '').$key.'='.$value.PHP_EOL;
            }
        }

        file_put_contents($envPath, $content);
    }

    private static function formatValue($value): string
    {
        if ($value === null) {
            return '';
        }
        $string = (string) $value;

        if ($string === '' || preg_match('/\s|#|"|\'"'"'|=/', $string)) {
            $escaped = str_replace('"', '\\"', $string);
            return '"'.$escaped.'"';
        }

        return $string;
    }
}