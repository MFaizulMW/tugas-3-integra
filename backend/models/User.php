<?php
namespace App\Models;

class User extends BaseModel {
    protected $table = 'fw_users';

    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM fw_users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        if ($user) {
            $user['roles'] = $this->getRoles($user['user_id']);
        }
        return $user ?: null;
    }

    public function findByUserId(string $user_id) {
        $stmt = $this->db->prepare("SELECT * FROM fw_users WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch();
        if ($user) $user['roles'] = $this->getRoles($user['user_id']);
        return $user ?: null;
    }

    public function create(array $data) {
        // data: user_id, username, password (plain), nama, email, active
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO fw_users (user_id, username, password, salt, nama, email, active) VALUES (:user_id,:username,:password,:salt,:nama,:email,:active)");
        $salt = bin2hex(random_bytes(8));
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':username' => $data['username'],
            ':password' => $passwordHash,
            ':salt' => $salt,
            ':nama' => $data['nama'] ?? null,
            ':email' => $data['email'] ?? null,
            ':active' => $data['active'] ?? 1
        ]);
        return $this->findByUserId($data['user_id']);
    }

    public function getRoles(string $user_id): array {
        $stmt = $this->db->prepare("
            SELECT g.group_id
            FROM fw_group_has_users ghu
            JOIN fw_groups g ON g.group_id = ghu.group_id
            WHERE ghu.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        $rows = $stmt->fetchAll();
        return array_column($rows, 'group_id');
    }
}
