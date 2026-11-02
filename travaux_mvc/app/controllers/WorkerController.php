<?php
class WorkerController extends Controller
{
    private function me(): array
    {
        $this->requireRole('ouvrier');
        $w = Worker::findByUser(Auth::id());
        if (!$w) { flash('Profil ouvrier introuvable.', 'err'); $this->redirect('auth/logout'); }
        return $w;
    }

    public function planning(): void
    {
        $w = $this->me();
        $this->render('worker/planning', ['w' => $w, 'missions' => Assignment::byWorker((int)$w['id']), 'today' => date('Y-m-d')]);
    }

    // Pointage : début de mission (uniquement le jour même)
    public function start(string $id = ''): void
    {
        $w = $this->me();
        if (!$this->isPost()) $this->redirect('worker/planning');
        $this->checkCsrf();
        $a = Assignment::find((int)$id);
        if (!$a || (int)$a['worker_id'] !== (int)$w['id']) { flash('Mission introuvable.', 'err'); }
        elseif ($a['date_mission'] !== date('Y-m-d') || $a['statut'] !== 'planifiee') { flash('Cette mission ne peut pas être démarrée maintenant.', 'err'); }
        else {
            Assignment::setStatus((int)$a['id'], 'en_cours');
            Assignment::syncReservation((int)$a['reservation_id']);
            flash('Mission démarrée.');
        }
        $this->redirect('worker/planning');
    }

    public function finish(string $id = ''): void
    {
        $w = $this->me();
        if (!$this->isPost()) $this->redirect('worker/planning');
        $this->checkCsrf();
        $a = Assignment::find((int)$id);
        if (!$a || (int)$a['worker_id'] !== (int)$w['id'] || $a['statut'] !== 'en_cours') { flash('Action impossible.', 'err'); }
        else {
            Assignment::setStatus((int)$a['id'], 'terminee');
            Assignment::syncReservation((int)$a['reservation_id']);
            flash('Mission terminée. Bon travail !');
        }
        $this->redirect('worker/planning');
    }

    public function absences(): void
    {
        $w = $this->me();
        if ($this->isPost()) {
            $this->checkCsrf();
            $date = $this->input('date_absence');
            if (!DateTime::createFromFormat('Y-m-d', $date) || $date < date('Y-m-d')) {
                flash('Choisissez une date valide (aujourd\'hui ou plus tard).', 'err');
            } elseif (Absence::declareAbsence((int)$w['id'], $date, $this->input('motif'))) {
                flash('Absence déclarée. L\'administrateur va organiser le remplacement.');
            } else {
                flash('Une absence existe déjà à cette date.', 'err');
            }
            $this->redirect('worker/absences');
        }
        $this->render('worker/absences', ['w' => $w, 'absences' => Absence::byWorker((int)$w['id'])]);
    }

    public function salaries(): void
    {
        $w = $this->me();
        $this->render('worker/salaries', ['w' => $w, 'salaries' => Salary::byWorker((int)$w['id'])]);
    }
}
