<?php
class Reservation extends Model
{
    public static function find(int $id)
    {
        return self::one('SELECT r.*, t.nom AS metier, t.tarif_journalier, u.nom AS client_nom, u.telephone AS client_tel
                          FROM reservations r JOIN trades t ON t.id=r.trade_id JOIN users u ON u.id=r.client_id
                          WHERE r.id=?', [$id]);
    }

    public static function create(int $clientId, int $tradeId, int $nb, string $debut, string $fin, string $adresse, string $desc): int
    {
        $trade = Trade::find($tradeId);
        $jours = count(datesBetween($debut, $fin));
        $montant = $trade['tarif_journalier'] * $jours * $nb;
        return self::insert('INSERT INTO reservations (client_id,trade_id,nombre_ouvriers,date_debut,date_fin,adresse,description,montant)
                             VALUES (?,?,?,?,?,?,?,?)', [$clientId, $tradeId, $nb, $debut, $fin, $adresse, $desc, $montant]);
    }

    public static function byClient(int $clientId): array
    {
        return self::all('SELECT r.*, t.nom AS metier, (SELECT COUNT(*) FROM reviews v WHERE v.reservation_id=r.id) AS note_faite
                          FROM reservations r JOIN trades t ON t.id=r.trade_id
                          WHERE r.client_id=? ORDER BY r.created_at DESC', [$clientId]);
    }

    public static function all_(string $statut = ''): array
    {
        $sql = 'SELECT r.*, t.nom AS metier, u.nom AS client_nom,
                       (SELECT COUNT(DISTINCT worker_id) FROM assignments a WHERE a.reservation_id=r.id) AS nb_affectes
                FROM reservations r JOIN trades t ON t.id=r.trade_id JOIN users u ON u.id=r.client_id';
        $p = [];
        if ($statut !== '') { $sql .= ' WHERE r.statut=?'; $p[] = $statut; }
        return self::all($sql . ' ORDER BY r.created_at DESC', $p);
    }

    public static function setStatus(int $id, string $statut): void
    {
        self::exec('UPDATE reservations SET statut=? WHERE id=?', [$statut, $id]);
    }

    public static function setPaid(int $id, int $paye): void
    {
        self::exec('UPDATE reservations SET paye=? WHERE id=?', [$paye, $id]);
    }

    public static function update(int $id, int $nb, string $debut, string $fin, string $adresse, string $desc): void
    {
        $r = self::find($id);
        $montant = $r['tarif_journalier'] * count(datesBetween($debut, $fin)) * $nb;
        self::exec('UPDATE reservations SET nombre_ouvriers=?, date_debut=?, date_fin=?, adresse=?, description=?, montant=? WHERE id=?',
                   [$nb, $debut, $fin, $adresse, $desc, $montant, $id]);
    }

    // Statistiques du tableau de bord
    public static function stats(): array
    {
        $s = [];
        $s['total']      = (int)self::one('SELECT COUNT(*) c FROM reservations')['c'];
        $s['en_attente'] = (int)self::one("SELECT COUNT(*) c FROM reservations WHERE statut='en_attente'")['c'];
        $s['en_mission'] = (int)self::one("SELECT COUNT(DISTINCT worker_id) c FROM assignments WHERE date_mission=CURDATE()")['c'];
        $s['ca_mois']    = (float)self::one("SELECT COALESCE(SUM(montant),0) c FROM reservations
                                             WHERE paye=1 AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")['c'];
        return $s;
    }
}
