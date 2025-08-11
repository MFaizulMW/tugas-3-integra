<?php
namespace App\Controllers;

use App\Models\User;
use App\Config\JwtConfig;
use Firebase\JWT\JWT;

class AuthController {
    protected $userModel;
    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['code'=>400,'message'=>'Bad Request','details'=>'Invalid JSON']);
            return;
        }

        // Basic validation (improve as needed)
        $required = ['user_id','username','password','nama'];
        foreach ($required as $r) {
            if (empty($input[$r])) {
                http_response_code(422);
                echo json_encode(['code'=>422,'message'=>'Validation Error','details'=>"Field {$r} is required"]);
                return;
            }
        }

        // Check if username exists
        if ($this->userModel->findByUsername($input['username'])) {
            http_response_code(409);
            echo json_encode(['code'=>409,'message'=>'Conflict','details'=>'Username already exists']);
            return;
        }

        $user = $this->userModel->create($input);
        unset($user['password']);
        echo json_encode(['code'=>201,'message'=>'User created','data'=>$user]);
    }

    /*public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['username']) || empty($input['password'])) {
            http_response_code(422);
            echo json_encode(['code'=>422,'message'=>'Validation Error','details'=>'username and password required']);
            return;
        }

        $user = $this->userModel->findByUsername($input['username']);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'Invalid credentials']);
            return;
        }

        // Password verification - support both password_hash and legacy SHA1 (if needed)
        $passValid = false;
        if (password_verify($input['password'], $user['password'])) {
            $passValid = true;
        } else {
            // legacy: if DB uses sha1(salt+pass)
            // attempt fallback for existing hashed values — BE CAREFUL: only use if required
            $legacy = sha1(($user['salt'] ?? '') . $input['password']);
            if ($legacy === $user['password']) {
                $passValid = true;
                // Optionally rehash to modern algorithm and update DB
                $this->rehashPassword($user['user_id'], $input['password']);
            }
        }

        if (!$passValid) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'Invalid credentials']);
            return;
        }

        $payload = [
            'iss' => JwtConfig::issuer(),
            'aud' => JwtConfig::audience(),
            'iat' => time(),
            'exp' => time() + JwtConfig::expireSeconds(),
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'roles' => $user['roles'] ?? []
        ];

        $jwt = JWT::encode($payload, JwtConfig::secret(), 'HS256');

        echo json_encode(['code'=>200,'message'=>'Login success','data'=>[
            'token' => $jwt,
            'expires_in' => JwtConfig::expireSeconds(),
            'user' => [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'roles' => $user['roles'] ?? []
            ]
        ]]);
    }*/
    
    public function login() {
    $input = json_decode(file_get_contents('php://input'), true);

    // Dummy data
    $dummyUser = [
        'user_id' => 1,
        'username' => 'admin',
        'nama' => 'Administrator',
        'email' => 'admin@example.com',
        'roles' => ['admin']
    ];
    $dummyPassword = '123456';

    // Validasi input
    if (!$input || empty($input['username']) || empty($input['password'])) {
        http_response_code(422);
        echo json_encode([
            'code'=>422,
            'message'=>'Validation Error',
            'details'=>'username and password required'
        ]);
        return;
    }

    // Cek username & password dummy
    if ($input['username'] !== $dummyUser['username'] || $input['password'] !== $dummyPassword) {
        http_response_code(401);
        echo json_encode([
            'code'=>401,
            'message'=>'Unauthorized',
            'details'=>'Invalid credentials'
        ]);
        return;
    }

    // Generate JWT dummy
    $payload = [
        'iss' => JwtConfig::issuer(),
        'aud' => JwtConfig::audience(),
        'iat' => time(),
        'exp' => time() + JwtConfig::expireSeconds(),
        'user_id' => $dummyUser['user_id'],
        'username' => $dummyUser['username'],
        'roles' => $dummyUser['roles']
    ];

    $jwt = JWT::encode($payload, JwtConfig::secret(), 'HS256');

    echo json_encode([
        'code'=>200,
        'message'=>'Login success (dummy)',
        'data'=>[
            'token' => $jwt,
            'expires_in' => JwtConfig::expireSeconds(),
            'user' => $dummyUser
        ]
    ]);
}


    public function me() {
        $user = $GLOBALS['auth_user'] ?? null;
        if (!$user) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized']);
            return;
        }
        unset($user['password']);
        echo json_encode(['code'=>200,'message'=>'OK','data'=>$user]);
    }

    protected function rehashPassword($user_id, $plain) {
        // update user password to password_hash
        try {
            $db = (new \App\Config\Database())::getInstance();
            $stmt = $db->prepare("UPDATE fw_users SET password = :pwd WHERE user_id = :uid");
            $stmt->execute([':pwd' => password_hash($plain, PASSWORD_DEFAULT), ':uid' => $user_id]);
        } catch (\Exception $e) {
            // ignore silently
        }
    }
}
