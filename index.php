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

function redirect($url) {
    header('Location: ' . $_ENV['APP_URL'] . $url);
    exit;
}

function route($name) {
    return $_ENV['APP_URL'] . $name;
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
    if ($path) {
        return $_ENV['APP_URL'] . $path;
    }
    return '';
}

function setFlash($key, $value) {
    $_SESSION[$key] = $value;
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
