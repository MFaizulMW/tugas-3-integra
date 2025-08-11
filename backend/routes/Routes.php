<?php
namespace App\Routes;

use App\Controllers\AuthController;
use App\Controllers\KendaraanController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RBACMiddleware;

class Routes
{
    private array $routes = [];

    public function __construct()
    {
        // Format: method => [ path => [controller, action, authRequired, allowedRoles] ]
        $this->routes = [
            'POST' => [
                'auth/register' => [AuthController::class, 'register', false],
                'auth/login'    => [AuthController::class, 'login', false],
                'ref_kendaraan' => [KendaraanController::class, 'create', true, ['admin', 'developer', 'grp-staf']],
            ],
            'GET' => [
                'auth/me'       => [AuthController::class, 'me', true],
                'ref_kendaraan' => [KendaraanController::class, 'index', true],
            ],
            'PUT' => [
                'ref_kendaraan/{id}' => [KendaraanController::class, 'update', true, ['admin', 'developer', 'grp-staf']],
            ],
            'PATCH' => [
                'ref_kendaraan/{id}' => [KendaraanController::class, 'update', true, ['admin', 'developer', 'grp-staf']],
            ],
            'DELETE' => [
                'ref_kendaraan/{id}' => [KendaraanController::class, 'delete', true, ['admin', 'developer']],
            ],
            'GET_ID' => [
                'ref_kendaraan/{id}' => [KendaraanController::class, 'show', true],
            ]
        ];
    }

    public function dispatch($method, $path)
    {
        $path = trim($path, '/');

        // Cek route langsung tanpa {id}
        if (isset($this->routes[$method][$path])) {
            return $this->handleRoute($this->routes[$method][$path]);
        }

        // Cek route yang mengandung {id}
        if (preg_match('#^ref_kendaraan/(\d+)$#', $path, $matches)) {
            $id = (int) $matches[1];

            // GET /ref_kendaraan/{id}
            if ($method === 'GET' && isset($this->routes['GET_ID']['ref_kendaraan/{id}'])) {
                return $this->handleRoute($this->routes['GET_ID']['ref_kendaraan/{id}'], $id);
            }

            // PUT/PATCH/DELETE
            if (isset($this->routes[$method]['ref_kendaraan/{id}'])) {
                return $this->handleRoute($this->routes[$method]['ref_kendaraan/{id}'], $id);
            }
        }

        // Jika tidak cocok, kirim 404
        $this->sendJson(['code' => 404, 'message' => 'Endpoint not found'], 404);
    }

    private function handleRoute(array $routeConfig, $id = null)
    {
        [$controllerClass, $action, $authRequired, $allowedRoles] = array_pad($routeConfig, 4, []);

        // Auth check
        if ($authRequired) {
            AuthMiddleware::authenticate();
            if (!empty($allowedRoles)) {
                RBACMiddleware::allow($allowedRoles);
            }
        }

        // Panggil controller
        $controller = new $controllerClass();
        if ($id !== null) {
            $controller->$action($id);
        } else {
            $controller->$action();
        }
    }

    private function sendJson(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
