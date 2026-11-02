<?php
class User extends Model
{
    public static function find(int $id) { return self::one('SELECT * FROM users WHERE id = ?', [$id]); }
    public static function findByEmail(string $email) { return self::one('SELECT * FROM users WHERE email = ?', [$email]); }

    public static function create(string $nom, string $email, string $pass, string $tel, string $role, string $adresse = ''): int
    {
        return self::insert(
            'INSERT INTO users (nom,email,mot_de_passe,telephone,adresse,role) VALUES (?,?,?,?,?,?)',
            [$nom, $email, password_hash($pass, PASSWORD_DEFAULT), $tel, $adresse, $role]
        );
    }

    public static function updateProfile(int $id, string $nom, string $tel, string $adresse): void
    {
        self::exec('UPDATE users SET nom=?, telephone=?, adresse=? WHERE id=?', [$nom, $tel, $adresse, $id]);
    }

    public static function setPassword(int $id, string $pass): void
    {
        self::exec('UPDATE users SET mot_de_passe=? WHERE id=?', [password_hash($pass, PASSWORD_DEFAULT), $id]);
    }

    public static function setStatus(int $id, string $statut): void
    {
        self::exec("UPDATE users SET statut=? WHERE id=? AND role='client'", [$statut, $id]);
    }

    public static function clients(): array
    {
        return self::all("SELECT * FROM users WHERE role='client' ORDER BY created_at DESC");
    }

    public static function countClients(): int
    {
        return (int)self::one("SELECT COUNT(*) c FROM users WHERE role='client'")['c'];
    }
}
