<?php

/**
 * Front controller para hospedagens cPanel onde o DocumentRoot
 * aponta para a raiz do projeto em vez de public/.
 *
 * Redireciona assets estaticos para public/ e bootstrapa o Laravel
 * sem expor /public/ na URL.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve arquivos estaticos de public/ diretamente
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$staticFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($staticFile) && !is_dir($staticFile)) {
    return false;
}

// Maintenance mode
if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require __DIR__ . '/vendor/autoload.php';

// Define DOCUMENT_ROOT correto para o Laravel encontrar as views
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/public';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->handleRequest(Request::capture());