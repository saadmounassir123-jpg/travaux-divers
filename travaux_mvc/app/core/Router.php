<?php
// Routeur : /controleur/action/param1/param2
class Router
{
    public function dispatch(string $url): void
    {
        $parts = array_values(array_filter(explode('/', trim($url, '/')), 'strlen'));
        $ctrl   = $parts[0] ?? 'home';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);

        // Sécurité : uniquement lettres/chiffres/underscore
        if (!preg_match('/^[a-z0-9_]+$/i', $ctrl) || !preg_match('/^[a-z0-9_]+$/i', $action)) {
            $this->notFound();
        }

        $class = ucfirst(strtolower($ctrl)) . 'Controller';
        $file  = APP . '/controllers/' . $class . '.php';
        if (!is_file($file)) $this->notFound();
        require_once $file;

        $obj = new $class();
        if (!method_exists($obj, $action) || !(new ReflectionMethod($obj, $action))->isPublic()) $this->notFound();

        call_user_func_array([$obj, $action], $params);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $content = '<div class="card"><h2>Page introuvable</h2><p><a href="' . e(url('')) . '">Retour à l\'accueil</a></p></div>';
        require APP . '/views/layouts/main.php';
        exit;
    }
}
