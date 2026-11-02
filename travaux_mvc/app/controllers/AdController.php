<?php
class AdController extends Controller
{
    // Clic public : compte le clic puis redirige vers le lien de la publicité
    public function click(string $id = ''): void
    {
        $link = Ad::click((int)$id);
        if ($link && preg_match('#^https?://#i', $link)) { header('Location: ' . $link); exit; }
        $this->redirect('');
    }

    public function index(): void
    {
        $this->requireRole('admin');
        $this->render('admin/ads', ['ads' => Ad::all_()]);
    }

    public function form(string $id = ''): void
    {
        $this->requireRole('admin');
        $this->render('admin/ad_form', ['ad' => $id !== '' ? Ad::find((int)$id) : null]);
    }

    public function save(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('ad');
        $this->checkCsrf();
        $titre = $this->input('titre'); $lien = $this->input('lien'); $slot = $this->input('emplacement');
        $debut = $this->input('date_debut'); $fin = $this->input('date_fin');
        $back = 'ad/form' . ($id !== '' ? '/' . (int)$id : '');
        if ($titre === '' || !preg_match('#^https?://#i', $lien) || !in_array($slot, ['haut', 'cote', 'bas'], true)
            || !DateTime::createFromFormat('Y-m-d', $debut) || !DateTime::createFromFormat('Y-m-d', $fin) || $fin < $debut) {
            flash('Vérifiez les champs (le lien doit commencer par http:// ou https://, la date de fin après le début).', 'err');
            $this->redirect($back);
        }
        $image = $this->uploadImage('image');
        if ($id === '') {
            if (!$image) { flash('Image obligatoire (JPG, PNG, WEBP ou GIF, 2 Mo max).', 'err'); $this->redirect($back); }
            Ad::create($titre, $image, $lien, $slot, $debut, $fin);
            flash('Publicité ajoutée.');
        } else {
            Ad::update((int)$id, $titre, $lien, $slot, $debut, $fin, $image);
            flash('Publicité modifiée.');
        }
        $this->redirect('ad');
    }

    public function toggle(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('ad');
        $this->checkCsrf();
        Ad::toggle((int)$id);
        $this->redirect('ad');
    }

    public function delete(string $id = ''): void
    {
        $this->requireRole('admin');
        if (!$this->isPost()) $this->redirect('ad');
        $this->checkCsrf();
        $ad = Ad::find((int)$id);
        if ($ad) {
            $f = $GLOBALS['config']['upload_dir'] . basename($ad['image']);
            if (is_file($f)) @unlink($f);
            Ad::delete((int)$id);
            flash('Publicité supprimée.');
        }
        $this->redirect('ad');
    }
}
