<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Lang;

class LanguageController
{
    public function change(): never
    {
        Lang::setLocale((string) ($_GET['locale'] ?? 'vi'));

        header('Location: ' . $this->redirectPath());
        exit;
    }

    private function redirectPath(): string
    {
        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');

        if ($referer === '') {
            return '/';
        }

        $refererHost = (string) parse_url($referer, PHP_URL_HOST);
        $refererPort = parse_url($referer, PHP_URL_PORT);
        $currentHost = (string) ($_SERVER['HTTP_HOST'] ?? '');

        if ($refererPort !== null) {
            $refererHost .= ':' . $refererPort;
        }

        if ($refererHost !== $currentHost) {
            return '/';
        }

        $path = parse_url($referer, PHP_URL_PATH) ?: '/';

        if ($path === '/lang') {
            return '/';
        }

        $query = parse_url($referer, PHP_URL_QUERY);

        return $query ? $path . '?' . $query : $path;
    }
}
