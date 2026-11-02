<?php
class Review extends Model
{
    public static function create(int $resId, int $clientId, int $note, string $comment): void
    {
        self::insert('INSERT INTO reviews (reservation_id,client_id,note,commentaire) VALUES (?,?,?,?)', [$resId, $clientId, $note, $comment]);
    }

    public static function all_(): array
    {
        return self::all('SELECT v.*, u.nom AS client_nom, t.nom AS metier
                          FROM reviews v JOIN users u ON u.id=v.client_id
                          JOIN reservations r ON r.id=v.reservation_id JOIN trades t ON t.id=r.trade_id
                          ORDER BY v.created_at DESC');
    }

    public static function forReservation(int $resId) { return self::one('SELECT * FROM reviews WHERE reservation_id=?', [$resId]); }

    public static function delete(int $id): void { self::exec('DELETE FROM reviews WHERE id=?', [$id]); }

    public static function average(): float
    {
        return (float)self::one('SELECT COALESCE(AVG(note),0) m FROM reviews')['m'];
    }
}
