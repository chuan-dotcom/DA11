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
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['auth'], $_SESSION['old_input']);
        session_regenerate_id(true);
    }

    public static function redirectPath(?array $user = null): string
    {
        $user = $user ?? self::user();

        if (!$user) {
            return 'auth/login';
        }

        return ($user['role'] ?? 'user') === 'admin'
            ? 'admin/dashboard'
            : 'auth/account';
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
