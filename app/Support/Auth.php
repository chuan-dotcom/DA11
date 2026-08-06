<?php

namespace App\Support;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['auth'] ?? null;
    }

    public static function check(): bool
    {
        return !empty($_SESSION['auth']);
    }

    public static function isAdmin(): bool
    {
        return self::check() && (self::user()['role'] ?? null) === 'admin';
    }

    public static function isHdv(): bool
    {
        return self::check() && (self::user()['role'] ?? null) === 'hdv';
    }

    public static function hasBoundHdv(): bool
    {
        return self::isHdv() && !empty(self::user()['hdv_id']);
    }

    public static function canSwitchHdv(): bool
    {
        return self::isAdmin() || (self::isHdv() && !self::hasBoundHdv());
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => $user['role'] ?? 'user',
            'status' => $user['status'] ?? 1,
            'avatar' => $user['avatar'] ?? null,
            'hdv_id' => isset($user['hdv_id']) ? (int) $user['hdv_id'] : null,
            'hdv_name' => $user['hdv_name'] ?? null,
        ];

        if (($user['role'] ?? null) === 'hdv' && !empty($user['hdv_id'])) {
            $_SESSION['hdv_id'] = (int) $user['hdv_id'];
        }
    }

    public static function logout(): void
    {
        unset($_SESSION['auth'], $_SESSION['old_input'], $_SESSION['hdv_id']);
        session_regenerate_id(true);
    }

    public static function redirectPath(?array $user = null): string
    {
        $user = $user ?? self::user();

        if (!$user) {
            return 'auth/login';
        }

        $role = $user['role'] ?? 'user';

        if ($role === 'admin') {
            return 'admin/dashboard';
        }

        if ($role === 'hdv') {
            return 'hdv/dashboard';
        }

        return 'auth/account';
    }

    public static function storeOldInput(array $input, array $except = ['password', 'password_confirmation']): void
    {
        foreach ($except as $key) {
            unset($input[$key]);
        }

        $_SESSION['old_input'] = $input;
    }

    public static function clearOldInput(): void
    {
        unset($_SESSION['old_input']);
    }
}
