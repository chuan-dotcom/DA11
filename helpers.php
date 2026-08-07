<?php

use eftec\bladeone\BladeOne;

if (!function_exists('view')) {
    function view($view, $data = [])
    {               
        $views = __DIR__ . '/views';
        $cache = __DIR__ . '/storage/compiles';

        // MODE_DEBUG allows to pinpoint troubles.
        $blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

        echo $blade->run($view, $data);
    }
}

if (!function_exists('is_upload')) {
    function is_upload($key)
    {
        return isset($_FILES[$key]) && $_FILES[$key]['size'] > 0;
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        header('Location: ' . $_ENV['APP_URL'] . $path);
        exit;
    }
}

if (!function_exists('redirect404')) {
    function redirect404()
    {
        header('HTTP/1.1 404 Not Found');
        exit;
    }
}

if (!function_exists('file_url')) {
    function file_url(?string $path): ?string
    {
        if (empty($path)) {
            return 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';
        }

        $cleanPath = str_replace('\\', '/', trim($path));

        if (preg_match('#^(https?://|data:image/)#i', $cleanPath)) {
            return $cleanPath;
        }

        $cleanPath = ltrim($cleanPath, '/');
        $fullPath = __DIR__ . '/' . $cleanPath;

        if (!file_exists($fullPath)) {
            return 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';
        }

        $base = $_ENV['APP_URL'] ?? '';
        return rtrim($base, '/') . '/' . $cleanPath;
    }
}

if (!function_exists('debug')) {
    function debug(...$data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('route')) {
    function route($path)
    {
        return $_ENV['APP_URL'] . $path;
    }
}

if (!function_exists('absolute_url')) {
    /**
     * URL tuyệt đối theo host đang truy cập (hữu ích cho QR trên điện thoại cùng mạng LAN).
     */
    function absolute_url(string $path): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $basePath = '';
        if (!empty($_ENV['APP_URL'])) {
            $parsed = parse_url($_ENV['APP_URL']);
            $basePath = rtrim($parsed['path'] ?? '', '/');
        }

        return $scheme . '://' . $host . $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('setFlash')) {
    function setFlash($key, $message)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $message;
    }
}
