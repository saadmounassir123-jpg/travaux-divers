<?php
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'], 'nom' => $user['nom'],
            'email' => $user['email'], 'role' => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function check(): bool { return isset($_SESSION['user']); }
    public static function id(): int { return (int)($_SESSION['user']['id'] ?? 0); }
    public static function role(): ?string { return $_SESSION['user']['role'] ?? null; }

    public static function home(?string $role = null): string
    {
        switch ($role ?? self::role()) {
            case 'admin':   return 'admin/dashboard';
            case 'ouvrier': return 'worker/planning';
            case 'client':  return 'client/dashboard';
            default:        return 'auth/login';
        }
    }
}
