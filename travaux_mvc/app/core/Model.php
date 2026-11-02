<?php
// Classe de base des modèles : requêtes préparées PDO
abstract class Model
{
    protected static function db(): PDO { return Database::pdo(); }

    protected static function query(string $sql, array $params = []): PDOStatement
    {
        $st = self::db()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    protected static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    protected static function one(string $sql, array $params = [])
    {
        $r = self::query($sql, $params)->fetch();
        return $r === false ? null : $r;
    }

    protected static function exec(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    protected static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int)self::db()->lastInsertId();
    }
}
