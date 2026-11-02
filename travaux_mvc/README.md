# LocaOuvriers – Location d'ouvriers à la journée (PHP pur, MVC)

## Installation (XAMPP / WAMP / LAMP)
1. Copier le dossier `travaux_mvc` dans `htdocs` (ou `www`).
2. Importer `database/script.sql` dans MySQL (phpMyAdmin > Importer). La base `travaux_mvc` est créée.
3. Adapter si besoin `config/config.php` (hôte, utilisateur, mot de passe MySQL).
4. Activer `mod_rewrite` (Apache) et `AllowOverride All`.
5. Ouvrir : http://localhost/travaux_mvc/public/

## Comptes de test (mot de passe : admin123)
- Administrateur : admin@site.com
- Client : client@site.com
- Ouvriers : ouvrier@site.com, karim@site.com
Changez ces mots de passe avant toute mise en ligne.

## Architecture MVC
- public/index.php : point d'entrée unique ; public/.htaccess : réécriture d'URL
- app/core : Router, Controller, Model, Database, Auth, Csrf, helpers
- app/models : accès base de données (PDO, requêtes préparées) et règles métier
- app/controllers : un contrôleur par thème (Auth, Client, Reservation, Worker, Admin, Absence, Salary, Ad)
- app/views : pages HTML (layouts, auth, client, worker, admin)
- URL : /contrôleur/action/paramètre  (ex. /reservation/create, /admin/reservation/3)

## Règles métier
- Une seule société (celle de l'admin) ; les ouvriers sont ses employés, créés par l'admin.
- Réservation = tarif journalier × nombre de jours × nombre d'ouvriers ; paiement hors site, marqué "payée" par l'admin.
- Un ouvrier = une mission par jour. Absence d'un ouvrier en mission : mission "à remplacer" -> l'admin choisit un remplaçant libre du même métier -> le client voit "Ouvrier remplacé".
- Absence non justifiée : retenue = salaire mensuel ÷ 30 par jour (config `jours_mois`). Justifiée : aucune retenue.
- Salaire net = salaire de base − retenues ; fiches générées chaque mois par l'admin puis marquées "payé".
- Pas de "mot de passe oublié" : l'admin réinitialise le mot de passe du client.

## Sécurité
password_hash, PDO préparé, échappement XSS (e()), jeton CSRF sur tous les POST, contrôle de rôle par action, upload d'images vérifié (type MIME + taille), dossiers app/config/database interdits d'accès web.
