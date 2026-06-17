<?php

date_default_timezone_set('Europe/Warsaw');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/cukiernia');
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'cukiernia');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('ADMIN_LOGIN')) {
    define('ADMIN_LOGIN', 'admin');
}

if (!defined('ADMIN_PASSWORD')) {
    define('ADMIN_PASSWORD', 'admin123');
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $base = rtrim(BASE_URL, '/');
        $path = ltrim($path, '/');

        return $path === '' ? $base : $base . '/' . $path;
    }
}

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('money')) {
    function money($value): string
    {
        return number_format((float) $value, 2, '.', '') . ' zł';
    }
}

if (!function_exists('cake_image')) {
    function cake_image(array $row): string
    {
        if (!empty($row['zdjecie'])) {
            $src = trim((string)$row['zdjecie']);

            if (preg_match('~^https?://~i', $src)) {
                return $src;
            }

            $plik = basename($src);
            if ($plik === 'tort_śmietanka.jpg') {
                $plik = 'tort_smietanka.jpg';
            }

            return app_url('images/' . $plik);
        }

        return app_url('images/logo.png');
    }
}
