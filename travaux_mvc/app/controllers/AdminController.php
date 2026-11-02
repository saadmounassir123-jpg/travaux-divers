<?php
class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->requireRole('admin');
        $mois = (int)date('n'); $annee = (int)date('Y');
        $this->render('admin/dashboard', [
            's' => Reservation::stats(),
            'dispo' => Worker::countAvailableToday(),
            'abs_jour' => Absence::todayCount(),
            'a_remplacer' => Absence::pendingCount(),
            'clients' => User::countClients(),
            'sal' => Salary::totals($mois, $annee),
            'note' => Review::average(),
        ]);
    }

    /* ---------- Ouvriers ---------- */
    public function workers(): void
    {
        $this->requireRole('admin');
        $this->render('admin/workers', ['workers' => Worker::all_()]);
    }

    public function worker_form(string $id = ''): void
    {
        $this->requireRole('admin');
        $w = $id !== '' ? Worker::find((int)$id) : null;
        $this->render('admin/worker_form', ['w' => $w, 'trades' => Trade::all_()]);
    }

    public function worker_save(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/workers');
        $this->checkCsrf();
        $nom = $this->input('nom'); $email = $this->input('email'); $tel = $this->input('telephone');
        $trade = (int)$this->input('trade_id'); $spec = $this->input('specialites');
        $salaire = (float)str_replace(',', '.', $this->input('salaire_mensuel'));
        $back = 'admin/worker_form' . ($id !== '' ? '/' . (int)$id : '');
        if ($nom === '' || !Trade::find($trade) || $salaire <= 0) { flash('Nom, métier et salaire valides obligatoires.', 'err'); $this->redirect($back); }

        if ($id === '') {
            $pass = (string)($_POST['password'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) { flash('Email valide et mot de passe (6 caractères min.) obligatoires.', 'err'); $this->redirect($back); }
            if (User::findByEmail($email)) { flash('Cet email est déjà utilisé.', 'err'); $this->redirect($back); }
            Worker::create($nom, $email, $pass, $tel, $trade, $spec, $salaire);
            flash('Ouvrier ajouté.');
        } else {
            $w = Worker::find((int)$id);
            if (!$w) $this->redirect('admin/workers');
            Worker::update((int)$id, $nom, $tel, $trade, $spec, $salaire, $this->input('statut') === 'inactif' ? 'inactif' : 'actif');
            $pass = (string)($_POST['password'] ?? '');
            if ($pass !== '') { if (strlen($pass) < 6) { flash('Mot de passe trop court.', 'err'); $this->redirect($back); } User::setPassword((int)$w['user_id'], $pass); }
            flash('Ouvrier modifié.');
        }
        $this->redirect('admin/workers');
    }

    public function worker_delete(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/workers');
        $this->checkCsrf();
        try { Worker::delete((int)$id); flash('Ouvrier supprimé.'); }
        catch (PDOException $e) { flash('Suppression impossible : cet ouvrier a des missions. Désactivez-le plutôt.', 'err'); }
        $this->redirect('admin/workers');
    }

    /* ---------- Métiers ---------- */
    public function trades(): void
    {
        $this->requireRole('admin');
        $edit = $this->input('edit') !== '' ? Trade::find((int)$this->input('edit')) : null;
        if ($this->isPost()) {
            $this->checkCsrf();
            $nom = $this->input('nom'); $tarif = (float)str_replace(',', '.', $this->input('tarif_journalier'));
            if ($nom === '' || $tarif <= 0) { flash('Nom et tarif valides obligatoires.', 'err'); }
            else {
                $id = (int)$this->input('id');
                if ($id) { Trade::update($id, $nom, $tarif); flash('Métier modifié.'); }
                else { Trade::create($nom, $tarif); flash('Métier ajouté.'); }
            }
            $this->redirect('admin/trades');
        }
        $this->render('admin/trades', ['trades' => Trade::all_(), 'edit' => $edit]);
    }

    public function trade_delete(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/trades');
        $this->checkCsrf();
        Trade::delete((int)$id) ? flash('Métier supprimé.') : flash('Impossible : ce métier est utilisé.', 'err');
        $this->redirect('admin/trades');
    }

    /* ---------- Clients ---------- */
    public function clients(): void
    {
        $this->requireRole('admin');
        $this->render('admin/clients', ['clients' => User::clients()]);
    }

    public function client_status(string $id = '', string $statut = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/clients');
        $this->checkCsrf();
        if (in_array($statut, ['actif', 'suspendu'], true)) { User::setStatus((int)$id, $statut); flash('Statut du client mis à jour.'); }
        $this->redirect('admin/clients');
    }

    // Pas de "mot de passe oublié" : l'admin réinitialise manuellement
    public function client_reset(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/clients');
        $this->checkCsrf();
        $pass = (string)($_POST['password'] ?? '');
        $u = User::find((int)$id);
        if (!$u || $u['role'] !== 'client') flash('Client introuvable.', 'err');
        elseif (strlen($pass) < 6) flash('Nouveau mot de passe : 6 caractères minimum.', 'err');
        else { User::setPassword((int)$id, $pass); flash('Mot de passe réinitialisé. Communiquez-le au client.'); }
        $this->redirect('admin/clients');
    }

    /* ---------- Réservations ---------- */
    public function reservations(): void
    {
        $this->requireRole('admin');
        $f = $this->input('statut');
        $this->render('admin/reservations', ['list' => Reservation::all_($f), 'filtre' => $f]);
    }

    public function reservation(string $id = ''): void
    {
        $this->requireRole('admin');
        $r = Reservation::find((int)$id);
        if (!$r) $this->redirect('admin/reservations');
        $dates = datesBetween($r['date_debut'], $r['date_fin']);
        $this->render('admin/reservation', [
            'r' => $r, 'missions' => Assignment::byReservation((int)$r['id']),
            'dispo' => in_array($r['statut'], ['acceptee', 'affectee'], true) ? Worker::available((int)$r['trade_id'], $dates) : [],
            'nb_affectes' => Assignment::distinctWorkers((int)$r['id']),
        ]);
    }

    // Accepter / refuser / marquer payée
    public function reservation_action(string $id = '', string $action = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/reservations');
        $this->checkCsrf();
        $r = Reservation::find((int)$id);
        if (!$r) $this->redirect('admin/reservations');
        switch ($action) {
            case 'accepter':
                if ($r['statut'] === 'en_attente') { Reservation::setStatus((int)$id, 'acceptee'); flash('Réservation acceptée. Affectez maintenant un ouvrier.'); }
                break;
            case 'refuser':
                if ($r['statut'] === 'en_attente') { Reservation::setStatus((int)$id, 'refusee'); flash('Réservation refusée.'); }
                break;
            case 'annuler':
                if (in_array($r['statut'], ['acceptee', 'affectee'], true)) {
                    Reservation::setStatus((int)$id, 'annulee');
                    Database::pdo()->prepare('DELETE FROM assignments WHERE reservation_id=?')->execute([$id]);
                    flash('Réservation annulée.');
                }
                break;
            case 'payee':   Reservation::setPaid((int)$id, 1); flash('Réservation marquée comme payée.'); break;
            case 'impayee': Reservation::setPaid((int)$id, 0); flash('Paiement retiré.'); break;
        }
        $this->redirect('admin/reservation/' . (int)$id);
    }

    public function assign(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/reservations');
        $this->checkCsrf();
        $r = Reservation::find((int)$id);
        if (!$r || !in_array($r['statut'], ['acceptee', 'affectee'], true)) { flash('Affectation impossible pour cette réservation.', 'err'); $this->redirect('admin/reservations'); }
        if (Assignment::distinctWorkers((int)$id) >= (int)$r['nombre_ouvriers']) { flash('Tous les ouvriers demandés sont déjà affectés.', 'err'); }
        elseif (Assignment::assignWorker($r, (int)$this->input('worker_id'))) {
            if (Assignment::distinctWorkers((int)$id) >= (int)$r['nombre_ouvriers']) Reservation::setStatus((int)$id, 'affectee');
            flash('Ouvrier affecté.');
        } else { flash('Cet ouvrier n\'est pas disponible sur toute la période.', 'err'); }
        $this->redirect('admin/reservation/' . (int)$id);
    }

    /* ---------- Planning général ---------- */
    public function planning(): void
    {
        $this->requireRole('admin');
        $mois = max(1, min(12, (int)($this->input('mois') ?: date('n'))));
        $annee = (int)($this->input('annee') ?: date('Y'));
        $this->render('admin/planning', ['rows' => Assignment::byMonth($mois, $annee), 'mois' => $mois, 'annee' => $annee]);
    }

    /* ---------- Avis ---------- */
    public function reviews(): void
    {
        $this->requireRole('admin');
        $this->render('admin/reviews', ['reviews' => Review::all_()]);
    }

    public function review_delete(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('admin/reviews');
        $this->checkCsrf();
        Review::delete((int)$id);
        flash('Avis supprimé.');
        $this->redirect('admin/reviews');
    }
}
