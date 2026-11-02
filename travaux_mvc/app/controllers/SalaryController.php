<?php
// Gestion des salaires mensuels (administrateur)
class SalaryController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $mois = max(1, min(12, (int)($this->input('mois') ?: date('n'))));
        $annee = (int)($this->input('annee') ?: date('Y'));
        $this->render('admin/salaries', [
            'list' => Salary::byMonth($mois, $annee), 'mois' => $mois, 'annee' => $annee,
            'tot' => Salary::totals($mois, $annee),
        ]);
    }

    public function generate(): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('salary');
        $this->checkCsrf();
        $mois = max(1, min(12, (int)$this->input('mois'))); $annee = (int)$this->input('annee');
        $n = Salary::generate($mois, $annee);
        flash("Fiches de paie générées/mises à jour : $n.");
        $this->redirect("salary?mois=$mois&annee=$annee");
    }

    public function pay(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('salary');
        $this->checkCsrf();
        $s = Salary::find((int)$id);
        if ($s) { Salary::markPaid((int)$id); flash('Salaire marqué comme payé.'); $this->redirect("salary?mois={$s['mois']}&annee={$s['annee']}"); }
        $this->redirect('salary');
    }
}
