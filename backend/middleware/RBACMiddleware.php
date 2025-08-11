<?php
namespace App\Middleware;

class RBACMiddleware {
    /**
     * $allowedRoles array|string. If user has any role in allowed -> OK.
     */
    public static function allow($allowedRoles) {
        $user = $GLOBALS['auth_user'] ?? null;
        if (!$user) {
            http_response_code(401);
            echo json_encode(['code'=>401,'message'=>'Unauthorized','details'=>'Not authenticated']);
            exit;
        }
        $userRoles = $user['roles'] ?? [];
        if (is_string($allowedRoles)) $allowedRoles = [$allowedRoles];
        $intersect = array_intersect($userRoles, $allowedRoles);
        if (empty($intersect)) {
            http_response_code(403);
            echo json_encode(['code'=>403,'message'=>'Forbidden','details'=>'Insufficient role privileges']);
            exit;
        }
        return true;
    }
}
