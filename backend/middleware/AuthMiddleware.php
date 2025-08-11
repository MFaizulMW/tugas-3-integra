<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JwtConfig;
use App\Models\User;

class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? null);
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'Missing Bearer token']);
            exit;
        }
        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key(JwtConfig::secret(), 'HS256'));

            // Cek expired token
            if (property_exists($decoded, 'exp') && $decoded->exp < time()) {
                http_response_code(401);
                echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'Token expired']);
                exit;
            }

            // Jika user dummy (misal user_id=1), langsung buat user dummy tanpa cek DB
            if (isset($decoded->user_id) && $decoded->user_id == 1) {
                $user = [
                    'user_id' => 1,
                    'username' => 'admin',
                    'nama' => 'Administrator',
                    'email' => 'admin@example.com',
                    'roles' => ['admin']
                ];
            } else {
                // User biasa: cari di database
                $userModel = new User();
                $user = $userModel->findByUserId($decoded->user_id ?? '');
                if (!$user) {
                    http_response_code(401);
                    echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'User not found']);
                    exit;
                }
            }

            // Simpan user ke global supaya controller bisa akses
            $GLOBALS['auth_user'] = $user;
            return $user;

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>$e->getMessage()]);
            exit;
        }
    }
}
