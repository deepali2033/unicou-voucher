<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode...
if (file_exists($maintenance = __DIR__.'/../../laravel_project/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload...
require __DIR__.'/../../laravel_project/vendor/autoload.php';

// Bootstrap...
$app = require_once __DIR__.'/../../laravel_project/bootstrap/app.php';

$app->handleRequest(Request::capture());