<?php
// Tambahkan ini DI BARIS PERTAMA sebelum kode lain apapun
header("Access-Control-Allow-Origin: *"); // Ganti * dengan origin frontend kalau perlu
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request (tanpa lanjut ke routing)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Routes\Routes;

header('Content-Type: application/json; charset=utf-8');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Very lightweight router dispatch
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove possible base path, if you host under /backend, adjust accordingly
$basePath = '/'; // adjust if needed
$path = substr($uri, strlen($basePath));
if ($path === false) $path = $uri;

try {
    $routes = new Routes();
    $routes->dispatch($method, $path);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'code' => 500,
        'message' => 'Internal Server Error',
        'details' => $e->getMessage()
    ]);
}
