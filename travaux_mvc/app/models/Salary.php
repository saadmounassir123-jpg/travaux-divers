<?php
class Salary extends Model
{
    public static function find(int $id) { return self::one('SELECT * FROM salaries WHERE id=?', [$id]); }

    public static function byMonth(int $mois, int $annee): array
    {
        return self::all('SELECT s.*, u.nom AS ouvrier, t.nom AS metier
                          FROM salaries s JOIN workers w ON w.id=s.worker_id JOIN users u ON u.id=w.user_id JOIN trades t ON t.id=w.trade_id
                          WHERE s.mois=? AND s.annee=? ORDER BY u.nom', [$mois, $annee]);
    }

    public static function byWorker(int $workerId): array
    {
        return self::all('SELECT * FROM salaries WHERE worker_id=? ORDER BY annee DESC, mois DESC', [$workerId]);
    }

    // Génère (ou met à jour si non payée) les fiches de paie du mois pour tous les ouvriers actifs
    public static function generate(int $mois, int $annee): int
    {
        $n = 0;
        foreach (self::all("SELECT * FROM workers WHERE statut='actif'") as $w) {
            $ab = self::one('SELECT COUNT(*) j, COALESCE(SUM(retenue),0) r FROM absences
                             WHERE worker_id=? AND MONTH(date_absence)=? AND YEAR(date_absence)=?', [$w['id'], $mois, $annee]);
            $retenues = (float)$ab['r'];
            $net = max(0, $w['salaire_mensuel'] - $retenues);
            $exist = self::one('SELECT id, statut FROM salaries WHERE worker_id=? AND mois=? AND annee=?', [$w['id'], $mois, $annee]);
            if ($exist) {
                if ($exist['statut'] === 'paye') continue;
                self::exec('UPDATE salaries SET salaire_base=?, jours_absence=?, retenues=?, salaire_net=? WHERE id=?',
                           [$w['salaire_mensuel'], $ab['j'], $retenues, $net, $exist['id']]);
            } else {
                self::insert('INSERT INTO salaries (worker_id,mois,annee,salaire_base,jours_absence,retenues,salaire_net) VALUES (?,?,?,?,?,?,?)',
                             [$w['id'], $mois, $annee, $w['salaire_mensuel'], $ab['j'], $retenues, $net]);
            }
            $n++;
        }
        return $n;
    }

    public static function markPaid(int $id): void
    {
        self::exec("UPDATE salaries SET statut='paye' WHERE id=?", [$id]);
    }

    public static function totals(int $mois, int $annee): array
    {
        return self::one("SELECT COALESCE(SUM(salaire_net),0) net, COALESCE(SUM(retenues),0) ret FROM salaries WHERE mois=? AND annee=?",
                         [$mois, $annee]);
    }
}
