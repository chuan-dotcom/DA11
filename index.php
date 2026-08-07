<?php
session_start();                   

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Show errors during development to avoid blank pages
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); 

function view($name, $data = []) {
    $blade = new \eftec\bladeone\BladeOne(
        __DIR__ . '/views',
        __DIR__ . '/storage/cache',
        \eftec\bladeone\BladeOne::MODE_DEBUG
    );
    if (!is_dir(__DIR__ . '/storage/cache')) {
        @mkdir(__DIR__ . '/storage/cache', 0755, true);
    }
    
    echo $blade->run($name, $data);
}

function app_base_path() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(dirname($scriptName), '\\/');
    return $dir === '' || $dir === '.' ? '/' : $dir;
}

function redirect($url) {
    $basePath = app_base_path();
    $url = '/' . ltrim($url, '/');
    if ($basePath !== '/') {
        $url = $basePath . $url;
    }
    header('Location: ' . $url);
    exit;
}

function route($name) {
    $basePath = app_base_path();
    $name = '/' . ltrim($name, '/');
    if ($basePath === '/') {
        return $name;
    }
    return $basePath . $name;
}

if (!function_exists('absolute_url')) {
    function absolute_url($path) {
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

function file_url($path) {
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

    return route($cleanPath);
}

function setFlash($key, $value) {
    $_SESSION['flash'][$key] = $value;
}

function old($key, $default = null) {
    if (isset($_SESSION['old_input'][$key])) {
        $v = $_SESSION['old_input'][$key];
        return is_string($v) ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $v;
    }
    if (isset($_POST[$key])) {
        $v = $_POST[$key];
        return is_string($v) ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $v;
    }
    return $default;
}

function e($value, $doubleEncode = true) {
    if (is_null($value)) return '';
    if (is_string($value)) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
    if (is_numeric($value) || is_bool($value)) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
    if (is_array($value) || is_object($value)) {
        return '';
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}

function is_upload($name) {
    return isset($_FILES[$name]) && $_FILES[$name]['error'] == 0;
}

function upload_file($file, $folder) {
    $targetDir = 'storage/uploads/' . $folder . '/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    move_uploaded_file($file['tmp_name'], $targetFile);
    return $targetFile;
}

require 'routes/web.php';
