<?php
class ClientController extends Controller
{
    public function dashboard(): void
    {
        $this->requireRole('client');
        $this->render('client/dashboard', ['reservations' => Reservation::byClient(Auth::id())]);
    }

    public function profile(): void
    {
        $this->requireRole('client');
        if ($this->isPost()) {
            $this->checkCsrf();
            $nom = $this->input('nom');
            if ($nom === '') { flash('Le nom est obligatoire.', 'err'); }
            else {
                User::updateProfile(Auth::id(), $nom, $this->input('telephone'), $this->input('adresse'));
                $_SESSION['user']['nom'] = $nom;
                $pass = (string)($_POST['password'] ?? '');
                if ($pass !== '') {
                    if (strlen($pass) < 6) { flash('Mot de passe trop court (6 caractères min.).', 'err'); $this->redirect('client/profile'); }
                    User::setPassword(Auth::id(), $pass);
                }
                flash('Profil mis à jour.');
                $this->redirect('client/profile');
            }
        }
        $this->render('client/profile', ['user' => User::find(Auth::id())]);
    }
}
