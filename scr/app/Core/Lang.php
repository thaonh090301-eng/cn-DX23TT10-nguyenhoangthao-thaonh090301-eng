<?php

declare(strict_types=1);

namespace App\Core;

class Lang
{
    private static string $locale = 'vi';
    private static string $basePath = '';
    private static array $translations = [];
    private static array $supported = ['vi', 'en'];

    public static function boot(string $basePath): void
    {
        self::$basePath = $basePath;
        self::setLocale((string) ($_SESSION['locale'] ?? 'vi'));
    }

    public static function setLocale(string $locale): void
    {
        if (!in_array($locale, self::$supported, true)) {
            $locale = 'vi';
        }

        self::$locale = $locale;
        $_SESSION['locale'] = $locale;

        $file = self::$basePath . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $locale . '.php';
        self::$translations = is_file($file) ? require $file : [];
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    public static function get(string $key, array $replace = []): string
    {
        $value = self::$translations[$key] ?? $key;

        foreach ($replace as $name => $replacement) {
            $value = str_replace(':' . $name, (string) $replacement, $value);
        }

        return $value;
    }
}
