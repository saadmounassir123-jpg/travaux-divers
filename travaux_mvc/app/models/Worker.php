<?php
class Worker extends Model
{
    public static function all_(): array
    {
        return self::all('SELECT w.*, u.nom, u.email, u.telephone, t.nom AS metier
                          FROM workers w JOIN users u ON u.id=w.user_id JOIN trades t ON t.id=w.trade_id
                          ORDER BY u.nom');
    }

    public static function find(int $id)
    {
        return self::one('SELECT w.*, u.nom, u.email, u.telephone, t.nom AS metier
                          FROM workers w JOIN users u ON u.id=w.user_id JOIN trades t ON t.id=w.trade_id
                          WHERE w.id=?', [$id]);
    }

    public static function findByUser(int $userId)
    {
        return self::one('SELECT w.*, u.nom, u.email, u.telephone, t.nom AS metier
                          FROM workers w JOIN users u ON u.id=w.user_id JOIN trades t ON t.id=w.trade_id
                          WHERE w.user_id=?', [$userId]);
    }

    public static function create(string $nom, string $email, string $pass, string $tel, int $tradeId, string $spec, float $salaire): int
    {
        $pdo = self::db();
        $pdo->beginTransaction();
        try {
            $uid = User::create($nom, $email, $pass, $tel, 'ouvrier');
            $wid = self::insert('INSERT INTO workers (user_id,trade_id,specialites,salaire_mensuel) VALUES (?,?,?,?)',
                                [$uid, $tradeId, $spec, $salaire]);
            $pdo->commit();
            return $wid;
        } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
    }

    public static function update(int $id, string $nom, string $tel, int $tradeId, string $spec, float $salaire, string $statut): void
    {
        $w = self::find($id);
        self::exec('UPDATE users SET nom=?, telephone=? WHERE id=?', [$nom, $tel, $w['user_id']]);
        self::exec('UPDATE workers SET trade_id=?, specialites=?, salaire_mensuel=?, statut=? WHERE id=?',
                   [$tradeId, $spec, $salaire, $statut, $id]);
    }

    public static function delete(int $id): void
    {
        $w = self::find($id);
        if ($w) self::exec('DELETE FROM users WHERE id=?', [$w['user_id']]); // cascade sur workers
    }

    // Ouvriers du métier, actifs, libres sur toutes les dates données (ni mission ni absence)
    public static function available(int $tradeId, array $dates): array
    {
        if (!$dates) return [];
        $in = implode(',', array_fill(0, count($dates), '?'));
        $sql = "SELECT w.*, u.nom FROM workers w JOIN users u ON u.id=w.user_id
                WHERE w.trade_id=? AND w.statut='actif' AND u.statut='actif'
                  AND w.id NOT IN (SELECT worker_id FROM assignments WHERE date_mission IN ($in))
                  AND w.id NOT IN (SELECT worker_id FROM absences WHERE date_absence IN ($in))
                ORDER BY u.nom";
        return self::all($sql, array_merge([$tradeId], $dates, $dates));
    }

    public static function countAvailableToday(): int
    {
        $r = self::one("SELECT COUNT(*) c FROM workers w
                        WHERE w.statut='actif'
                          AND w.id NOT IN (SELECT worker_id FROM assignments WHERE date_mission=CURDATE())
                          AND w.id NOT IN (SELECT worker_id FROM absences WHERE date_absence=CURDATE())");
        return (int)$r['c'];
    }

    // Nombre d'ouvriers actifs par métier (pour le catalogue client)
    public static function countByTrade(): array
    {
        $rows = self::all("SELECT trade_id, COUNT(*) c FROM workers WHERE statut='actif' GROUP BY trade_id");
        $out = [];
        foreach ($rows as $r) $out[$r['trade_id']] = (int)$r['c'];
        return $out;
    }
}
