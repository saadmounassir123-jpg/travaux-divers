<?php
class Assignment extends Model
{
    public static function find(int $id)
    {
        return self::one('SELECT a.*, w.trade_id FROM assignments a JOIN workers w ON w.id=a.worker_id WHERE a.id=?', [$id]);
    }

    public static function byReservation(int $resId): array
    {
        return self::all('SELECT a.*, u.nom AS ouvrier, w.id AS wid
                          FROM assignments a JOIN workers w ON w.id=a.worker_id JOIN users u ON u.id=w.user_id
                          WHERE a.reservation_id=? ORDER BY a.date_mission, u.nom', [$resId]);
    }

    // Ouvriers distincts affectés à une réservation
    public static function distinctWorkers(int $resId): int
    {
        return (int)self::one('SELECT COUNT(DISTINCT worker_id) c FROM assignments WHERE reservation_id=?', [$resId])['c'];
    }

    // Affecte un ouvrier sur toutes les dates de la réservation
    public static function assignWorker(array $res, int $workerId): bool
    {
        $dates = datesBetween($res['date_debut'], $res['date_fin']);
        $libres = array_column(Worker::available((int)$res['trade_id'], $dates), 'id');
        if (!in_array($workerId, array_map('intval', $libres), true)) return false;
        foreach ($dates as $d) {
            self::insert('INSERT INTO assignments (reservation_id,worker_id,date_mission) VALUES (?,?,?)', [$res['id'], $workerId, $d]);
        }
        return true;
    }

    // Planning d'un ouvrier
    public static function byWorker(int $workerId): array
    {
        return self::all('SELECT a.*, r.adresse, r.description, u.nom AS client_nom, u.telephone AS client_tel
                          FROM assignments a JOIN reservations r ON r.id=a.reservation_id JOIN users u ON u.id=r.client_id
                          WHERE a.worker_id=? ORDER BY a.date_mission DESC', [$workerId]);
    }

    // Planning général sur un mois
    public static function byMonth(int $mois, int $annee): array
    {
        return self::all('SELECT a.*, u.nom AS ouvrier, t.nom AS metier, c.nom AS client_nom
                          FROM assignments a JOIN workers w ON w.id=a.worker_id JOIN users u ON u.id=w.user_id
                          JOIN trades t ON t.id=w.trade_id JOIN reservations r ON r.id=a.reservation_id JOIN users c ON c.id=r.client_id
                          WHERE MONTH(a.date_mission)=? AND YEAR(a.date_mission)=?
                          ORDER BY a.date_mission, u.nom', [$mois, $annee]);
    }

    public static function setStatus(int $id, string $statut): void
    {
        self::exec('UPDATE assignments SET statut=? WHERE id=?', [$statut, $id]);
    }

    // Remplacement : la mission passe au nouvel ouvrier
    public static function replace(int $id, int $newWorkerId): void
    {
        self::exec("UPDATE assignments SET worker_id=?, statut='planifiee', remplace=1 WHERE id=?", [$newWorkerId, $id]);
    }

    // Met à jour le statut de la réservation selon l'état de ses missions
    public static function syncReservation(int $resId): void
    {
        $r = self::one("SELECT COUNT(*) total,
                               SUM(statut='terminee') fin, SUM(statut IN ('en_cours','terminee')) debut
                        FROM assignments WHERE reservation_id=?", [$resId]);
        if ((int)$r['total'] === 0) return;
        if ((int)$r['fin'] === (int)$r['total']) Reservation::setStatus($resId, 'terminee');
        elseif ((int)$r['debut'] > 0) Reservation::setStatus($resId, 'en_cours');
    }
}
