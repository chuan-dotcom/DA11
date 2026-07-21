<?php
session_start();

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function view($name, $data = []) {
    $blade = new \eftec\bladeone\BladeOne(
        __DIR__ . '/views',
        __DIR__ . '/storage/cache',
        \eftec\bladeone\BladeOne::MODE_DEBUG
    );
    
    echo $blade->run($name, $data);
}

function redirect($url) {
    header('Location: ' . $_ENV['APP_URL'] . $url);
    exit;
}

function route($name) {
    return $_ENV['APP_URL'] . $name;
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
