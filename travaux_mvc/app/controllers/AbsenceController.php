<?php
// Gestion des absences et des remplacements (administrateur)
class AbsenceController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $list = Absence::all_();
        foreach ($list as &$a) {
            $a['candidats'] = ($a['mission_id'] && $a['mission_statut'] === 'a_remplacer') ? Absence::replacementCandidates($a) : [];
        }
        unset($a);
        $this->render('admin/absences', ['list' => $list]);
    }

    // Remplacer l'ouvrier absent par un autre ouvrier libre du même métier
    public function replace(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('absence');
        $this->checkCsrf();
        $a = Absence::find((int)$id);
        $newId = (int)$this->input('worker_id');
        if (!$a || !$a['assignment_id']) { flash('Aucune mission à remplacer.', 'err'); $this->redirect('absence'); }
        $m = Assignment::find((int)$a['assignment_id']);
        $candidats = array_map('intval', array_column(Absence::replacementCandidates($a), 'id'));
        if (!$m || $m['statut'] !== 'a_remplacer') flash('Cette mission est déjà remplacée.', 'err');
        elseif (!in_array($newId, $candidats, true)) flash('Cet ouvrier n\'est pas disponible ce jour-là.', 'err');
        else {
            Assignment::replace((int)$m['id'], $newId);
            Absence::setReplacement((int)$id, $newId);
            flash('Remplacement effectué. Le client verra le changement d\'ouvrier.');
        }
        $this->redirect('absence');
    }

    // Absence justifiée => pas de retenue sur le salaire
    public function justify(string $id = '', string $value = '0'): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('absence');
        $this->checkCsrf();
        if (Absence::find((int)$id)) {
            Absence::setJustified((int)$id, $value === '1' ? 1 : 0);
            flash('Absence mise à jour.');
        }
        $this->redirect('absence');
    }
}
