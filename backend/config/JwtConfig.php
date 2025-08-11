<?php
namespace App\Config;

class JwtConfig {
    public static function secret(): string {
        return $_ENV['JWT_SECRET'] ?? 'insecure_default_secret_change_me';
    }

    public static function issuer(): string {
        return $_ENV['JWT_ISSUER'] ?? 'my-app';
    }

    public static function audience(): string {
        return $_ENV['JWT_AUD'] ?? 'my-app-users';
    }

    public static function expireSeconds(): int {
        return intval($_ENV['JWT_EXPIRE_SECONDS'] ?? 3600);
    }
}
