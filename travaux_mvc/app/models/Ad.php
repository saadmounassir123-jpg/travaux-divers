<?php
class Ad extends Model
{
    public static function all_(): array { return self::all('SELECT * FROM ads ORDER BY id DESC'); }
    public static function find(int $id) { return self::one('SELECT * FROM ads WHERE id=?', [$id]); }

    // Une publicité active pour l'emplacement (au hasard) ; compte l'affichage
    public static function forSlot(string $slot)
    {
        $ad = self::one("SELECT * FROM ads WHERE emplacement=? AND actif=1 AND CURDATE() BETWEEN date_debut AND date_fin
                         ORDER BY RAND() LIMIT 1", [$slot]);
        if ($ad) self::exec('UPDATE ads SET affichages=affichages+1 WHERE id=?', [$ad['id']]);
        return $ad;
    }

    public static function click(int $id): ?string
    {
        $ad = self::find($id);
        if (!$ad) return null;
        self::exec('UPDATE ads SET clics=clics+1 WHERE id=?', [$id]);
        return $ad['lien'];
    }

    public static function create(string $titre, string $image, string $lien, string $slot, string $debut, string $fin): void
    {
        self::insert('INSERT INTO ads (titre,image,lien,emplacement,date_debut,date_fin) VALUES (?,?,?,?,?,?)',
                     [$titre, $image, $lien, $slot, $debut, $fin]);
    }

    public static function update(int $id, string $titre, string $lien, string $slot, string $debut, string $fin, ?string $image): void
    {
        if ($image) {
            self::exec('UPDATE ads SET titre=?, lien=?, emplacement=?, date_debut=?, date_fin=?, image=? WHERE id=?',
                       [$titre, $lien, $slot, $debut, $fin, $image, $id]);
        } else {
            self::exec('UPDATE ads SET titre=?, lien=?, emplacement=?, date_debut=?, date_fin=? WHERE id=?',
                       [$titre, $lien, $slot, $debut, $fin, $id]);
        }
    }

    public static function toggle(int $id): void { self::exec('UPDATE ads SET actif = 1 - actif WHERE id=?', [$id]); }

    public static function delete(int $id): void { self::exec('DELETE FROM ads WHERE id=?', [$id]); }
}
