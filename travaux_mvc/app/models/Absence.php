<?php
class Absence extends Model
{
    public static function find(int $id) { return self::one('SELECT * FROM absences WHERE id=?', [$id]); }

    public static function all_(): array
    {
        return self::all('SELECT ab.*, u.nom AS ouvrier, t.nom AS metier, w.salaire_mensuel,
                                 ru.nom AS remplacant,
                                 a.id AS mission_id, a.statut AS mission_statut, a.reservation_id
                          FROM absences ab
                          JOIN workers w ON w.id=ab.worker_id JOIN users u ON u.id=w.user_id JOIN trades t ON t.id=w.trade_id
                          LEFT JOIN assignments a ON a.id=ab.assignment_id
                          LEFT JOIN workers rw ON rw.id=ab.remplacant_id LEFT JOIN users ru ON ru.id=rw.user_id
                          ORDER BY ab.date_absence DESC');
    }

    public static function byWorker(int $workerId): array
    {
        return self::all('SELECT * FROM absences WHERE worker_id=? ORDER BY date_absence DESC', [$workerId]);
    }

    // Déclare une absence. Si l'ouvrier avait une mission ce jour-là, elle passe "à remplacer".
    // Retenue par défaut = salaire / 30 (annulée si l'admin la déclare justifiée).
    public static function declareAbsence(int $workerId, string $date, string $motif): bool
    {
        if (self::one('SELECT id FROM absences WHERE worker_id=? AND date_absence=?', [$workerId, $date])) return false;
        $w = Worker::find($workerId);
        $retenue = round($w['salaire_mensuel'] / $GLOBALS['config']['jours_mois'], 2);
        $mission = self::one('SELECT id FROM assignments WHERE worker_id=? AND date_mission=?', [$workerId, $date]);
        $aid = $mission ? (int)$mission['id'] : null;
        self::insert('INSERT INTO absences (worker_id,date_absence,motif,retenue,assignment_id) VALUES (?,?,?,?,?)',
                     [$workerId, $date, $motif, $retenue, $aid]);
        if ($aid) Assignment::setStatus($aid, 'a_remplacer');
        return true;
    }

    // L'admin décide si l'absence est justifiée (pas de retenue) ou non
    public static function setJustified(int $id, int $justifiee): void
    {
        $a = self::find($id);
        $w = Worker::find((int)$a['worker_id']);
        $retenue = $justifiee ? 0 : round($w['salaire_mensuel'] / $GLOBALS['config']['jours_mois'], 2);
        self::exec('UPDATE absences SET justifiee=?, retenue=? WHERE id=?', [$justifiee, $retenue, $id]);
    }

    public static function setReplacement(int $id, int $remplacantId): void
    {
        self::exec('UPDATE absences SET remplacant_id=? WHERE id=?', [$remplacantId, $id]);
    }

    // Ouvriers du même métier libres à cette date
    public static function replacementCandidates(array $absence): array
    {
        $w = Worker::find((int)$absence['worker_id']);
        return Worker::available((int)$w['trade_id'], [$absence['date_absence']]);
    }

    public static function pendingCount(): int
    {
        return (int)self::one("SELECT COUNT(*) c FROM assignments WHERE statut='a_remplacer'")['c'];
    }

    public static function todayCount(): int
    {
        return (int)self::one('SELECT COUNT(*) c FROM absences WHERE date_absence=CURDATE()')['c'];
    }
}
