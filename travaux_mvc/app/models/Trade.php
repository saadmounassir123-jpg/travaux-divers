<?php
class Trade extends Model
{
    public static function all_(): array { return self::all('SELECT * FROM trades ORDER BY nom'); }
    public static function find(int $id) { return self::one('SELECT * FROM trades WHERE id=?', [$id]); }

    public static function create(string $nom, float $tarif): void
    {
        self::insert('INSERT INTO trades (nom,tarif_journalier) VALUES (?,?)', [$nom, $tarif]);
    }

    public static function update(int $id, string $nom, float $tarif): void
    {
        self::exec('UPDATE trades SET nom=?, tarif_journalier=? WHERE id=?', [$nom, $tarif, $id]);
    }

    public static function delete(int $id): bool
    {
        $used = self::one('SELECT (SELECT COUNT(*) FROM workers WHERE trade_id=?) + (SELECT COUNT(*) FROM reservations WHERE trade_id=?) n', [$id, $id]);
        if ((int)$used['n'] > 0) return false;
        self::exec('DELETE FROM trades WHERE id=?', [$id]);
        return true;
    }
}
