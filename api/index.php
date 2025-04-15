<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/auth/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($uri);
$path = trim($parsed_url['path'], '/');

$path = str_replace('api/', '', $path);

$segments = explode('/', $path);
if (count($segments) < 2) {
    Response::error("Ruta inválida", 404);
}

$modulo = $segments[0];
$archivo = $segments[1];

$target = __DIR__ . "/endpoints/$modulo/$archivo.php";
if (!file_exists($target)) {
    Response::error("Endpoint no encontrado", 404);
}

require $target;
