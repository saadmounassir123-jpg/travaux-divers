<?php
class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) $this->redirect(Auth::home());
        $email = '';
        if ($this->isPost()) {
            $this->checkCsrf();
            $email = $this->input('email');
            $user = User::findByEmail($email);
            if (!$user || !password_verify($this->input('password'), $user['mot_de_passe'])) {
                flash('Email ou mot de passe incorrect.', 'err');
            } elseif ($user['statut'] !== 'actif') {
                flash('Votre compte est suspendu. Contactez l\'administrateur.', 'err');
            } else {
                Auth::login($user);
                $this->redirect(Auth::home($user['role']));
            }
        }
        $this->render('auth/login', ['email' => $email]);
    }

    // Inscription réservée aux clients (les ouvriers sont créés par l'administrateur)
    public function register(): void
    {
        if (Auth::check()) $this->redirect(Auth::home());
        $old = ['nom' => '', 'email' => '', 'telephone' => ''];
        if ($this->isPost()) {
            $this->checkCsrf();
            $old = ['nom' => $this->input('nom'), 'email' => $this->input('email'), 'telephone' => $this->input('telephone')];
            $pass = (string)($_POST['password'] ?? '');
            if ($old['nom'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                flash('Nom et email valides obligatoires.', 'err');
            } elseif (strlen($pass) < 6) {
                flash('Le mot de passe doit contenir au moins 6 caractères.', 'err');
            } elseif ($pass !== ($_POST['password2'] ?? '')) {
                flash('Les deux mots de passe sont différents.', 'err');
            } elseif (User::findByEmail($old['email'])) {
                flash('Cet email est déjà utilisé.', 'err');
            } else {
                $id = User::create($old['nom'], $old['email'], $pass, $old['telephone'], 'client');
                Auth::login(User::find($id));
                flash('Bienvenue ! Votre compte a été créé.');
                $this->redirect(Auth::home());
            }
        }
        $this->render('auth/register', ['old' => $old]);
    }

    public function logout(): void
    {
        Auth::logout();
        session_start();
        flash('Vous êtes déconnecté.');
        $this->redirect('auth/login');
    }
}
