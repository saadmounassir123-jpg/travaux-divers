<?php
// Configuration générale
return [
    'db_host'    => 'localhost',
    'db_name'    => 'travaux_mvc',
    'db_user'    => 'root',
    'db_pass'    => '',
    'site_name'  => 'LocaOuvriers',
    'upload_dir' => __DIR__ . '/../public/uploads/',
    'max_upload' => 2 * 1024 * 1024, // 2 Mo
    'jours_mois' => 30,              // base de calcul de la retenue d'absence
];
