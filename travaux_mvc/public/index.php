<?php
// Point d'entrée unique de l'application
session_start();

define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

$config = require ROOT . '/config/config.php';
$GLOBALS['config'] = $config;

require APP . '/core/helpers.php';
require APP . '/core/Database.php';
require APP . '/core/Model.php';
require APP . '/core/Controller.php';
require APP . '/core/Auth.php';
require APP . '/core/Csrf.php';
require APP . '/core/Router.php';

// Chargement automatique des modèles
spl_autoload_register(function ($class) {
    $file = APP . '/models/' . $class . '.php';
    if (is_file($file)) require $file;
});

(new Router())->dispatch($_GET['url'] ?? '');
