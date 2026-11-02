<?php
class ReservationController extends Controller
{
    // Formulaire + enregistrement d'une réservation (client)
    public function create(string $tradeId = ''): void
    {
        $this->requireRole('client');
        $old = ['trade_id' => (int)$tradeId, 'nombre_ouvriers' => 1, 'date_debut' => '', 'date_fin' => '', 'adresse' => '', 'description' => ''];
        if ($this->isPost()) {
            $this->checkCsrf();
            $old = [
                'trade_id' => (int)$this->input('trade_id'), 'nombre_ouvriers' => max(1, (int)$this->input('nombre_ouvriers')),
                'date_debut' => $this->input('date_debut'), 'date_fin' => $this->input('date_fin'),
                'adresse' => $this->input('adresse'), 'description' => $this->input('description'),
            ];
            $err = $this->validate($old);
            if ($err) { flash($err, 'err'); }
            else {
                $id = Reservation::create(Auth::id(), $old['trade_id'], $old['nombre_ouvriers'], $old['date_debut'],
                                          $old['date_fin'], $old['adresse'], $old['description']);
                flash('Réservation envoyée. L\'administrateur va la traiter.');
                $this->redirect('reservation/show/' . $id);
            }
        }
        $this->render('client/reservation_form', ['old' => $old, 'trades' => Trade::all_(), 'edit' => false, 'id' => 0]);
    }

    private function validate(array $d): ?string
    {
        if (!Trade::find($d['trade_id'])) return 'Choisissez un métier.';
        if ($d['adresse'] === '') return 'L\'adresse du lieu de travail est obligatoire.';
        $ok = fn($s) => (bool)DateTime::createFromFormat('Y-m-d', $s);
        if (!$ok($d['date_debut']) || !$ok($d['date_fin'])) return 'Dates invalides.';
        if ($d['date_debut'] < date('Y-m-d')) return 'La date de début ne peut pas être dans le passé.';
        if ($d['date_fin'] < $d['date_debut']) return 'La date de fin doit être après la date de début.';
        if (count(datesBetween($d['date_debut'], $d['date_fin'])) > 31) return 'Durée maximale : 31 jours.';
        $libres = count(Worker::available($d['trade_id'], datesBetween($d['date_debut'], $d['date_fin'])));
        if ($libres < $d['nombre_ouvriers']) {
            return "Seulement $libres ouvrier(s) disponible(s) sur cette période pour ce métier.";
        }
        return null;
    }

    // Charge une réservation appartenant au client connecté
    private function mine(string $id): array
    {
        $r = Reservation::find((int)$id);
        if (!$r || (int)$r['client_id'] !== Auth::id()) { http_response_code(404); $this->render('errors/403'); exit; }
        return $r;
    }

    public function show(string $id = ''): void
    {
        $this->requireRole('client');
        $r = $this->mine($id);
        $this->render('client/reservation_show', [
            'r' => $r, 'missions' => Assignment::byReservation((int)$r['id']),
            'review' => Review::forReservation((int)$r['id']),
        ]);
    }

    // Modification possible tant que la réservation est en attente
    public function edit(string $id = ''): void
    {
        $this->requireRole('client');
        $r = $this->mine($id);
        if ($r['statut'] !== 'en_attente') { flash('Cette réservation ne peut plus être modifiée.', 'err'); $this->redirect('reservation/show/' . $r['id']); }
        $old = ['trade_id' => (int)$r['trade_id'], 'nombre_ouvriers' => (int)$r['nombre_ouvriers'], 'date_debut' => $r['date_debut'],
                'date_fin' => $r['date_fin'], 'adresse' => $r['adresse'], 'description' => $r['description']];
        if ($this->isPost()) {
            $this->checkCsrf();
            $old = array_merge($old, [
                'nombre_ouvriers' => max(1, (int)$this->input('nombre_ouvriers')), 'date_debut' => $this->input('date_debut'),
                'date_fin' => $this->input('date_fin'), 'adresse' => $this->input('adresse'), 'description' => $this->input('description'),
            ]);
            $err = $this->validate($old);
            if ($err) { flash($err, 'err'); }
            else {
                Reservation::update((int)$r['id'], $old['nombre_ouvriers'], $old['date_debut'], $old['date_fin'], $old['adresse'], $old['description']);
                flash('Réservation modifiée.');
                $this->redirect('reservation/show/' . $r['id']);
            }
        }
        $this->render('client/reservation_form', ['old' => $old, 'trades' => Trade::all_(), 'edit' => true, 'id' => (int)$r['id']]);
    }

    public function cancel(string $id = ''): void
    {
        $this->requireRole('client');
        if (!$this->isPost()) $this->redirect('client/dashboard');
        $this->checkCsrf();
        $r = $this->mine($id);
        // Annulation possible avant le début de la mission
        if (in_array($r['statut'], ['en_attente', 'acceptee', 'affectee'], true) && $r['date_debut'] > date('Y-m-d')) {
            Reservation::setStatus((int)$r['id'], 'annulee');
            Database::pdo()->prepare('DELETE FROM assignments WHERE reservation_id=?')->execute([$r['id']]);
            flash('Réservation annulée.');
        } else {
            flash('Cette réservation ne peut plus être annulée.', 'err');
        }
        $this->redirect('reservation/show/' . $r['id']);
    }

    public function review(string $id = ''): void
    {
        $this->requireRole('client');
        if (!$this->isPost()) $this->redirect('client/dashboard');
        $this->checkCsrf();
        $r = $this->mine($id);
        $note = (int)$this->input('note');
        if ($r['statut'] !== 'terminee') flash('Vous pouvez noter uniquement une mission terminée.', 'err');
        elseif ($note < 1 || $note > 5) flash('La note doit être entre 1 et 5.', 'err');
        else {
            try { Review::create((int)$r['id'], Auth::id(), $note, $this->input('commentaire')); flash('Merci pour votre avis !'); }
            catch (PDOException $e) { flash('Vous avez déjà noté cette réservation.', 'err'); }
        }
        $this->redirect('reservation/show/' . $r['id']);
    }
}
