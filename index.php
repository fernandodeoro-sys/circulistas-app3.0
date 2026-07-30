<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// Detectar dinámicamente si los estilos están en la raíz (hosting) o en /public (XAMPP local)
if (file_exists(__DIR__.'/build')) {
    $app->usePublicPath(__DIR__);
} else {
    $app->usePublicPath(__DIR__.'/public');
}

$app->handleRequest(Request::capture());

