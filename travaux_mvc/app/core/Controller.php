<?php
abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $viewFile = APP . '/views/' . $view . '.php';
        if (!is_file($viewFile)) { http_response_code(404); echo 'Vue introuvable'; return; }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require APP . '/views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function requireRole(string ...$roles): void
    {
        if (!Auth::check()) { flash('Veuillez vous connecter.', 'err'); $this->redirect('auth/login'); }
        if (!in_array(Auth::role(), $roles, true)) {
            http_response_code(403);
            $this->render('errors/403');
            exit;
        }
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

    // À appeler au début de chaque action qui traite un formulaire POST
    protected function checkCsrf(): void
    {
        if (!Csrf::check()) {
            http_response_code(419);
            echo 'Jeton CSRF invalide. Retournez en arrière et rechargez la page.';
            exit;
        }
    }

    protected function input(string $key, $default = ''): string
    {
        return trim((string)($_POST[$key] ?? $_GET[$key] ?? $default));
    }

    // Upload sécurisé d'une image : retourne le nom du fichier ou null
    protected function uploadImage(string $field): ?string
    {
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
        $f = $_FILES[$field];
        $cfg = $GLOBALS['config'];
        if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > $cfg['max_upload']) return null;
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
        if (!$ext) return null;
        $name = bin2hex(random_bytes(12)) . '.' . $ext;
        return move_uploaded_file($f['tmp_name'], $cfg['upload_dir'] . $name) ? $name : null;
    }
}
