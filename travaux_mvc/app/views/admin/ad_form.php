<div class="card">
  <h1><?= $ad ? 'Modifier la publicité' : 'Nouvelle publicité' ?></h1>
  <form method="post" enctype="multipart/form-data" action="<?= e(url('ad/save' . ($ad ? '/' . $ad['id'] : ''))) ?>">
    <?= Csrf::field() ?>
    <div class="grid2">
      <div class="row"><label>Titre</label><input type="text" name="titre" value="<?= e($ad['titre'] ?? '') ?>" required></div>
      <div class="row"><label>Lien (https://...)</label><input type="url" name="lien" value="<?= e($ad['lien'] ?? '') ?>" required></div>
      <div class="row"><label>Emplacement</label>
        <select name="emplacement">
          <?php foreach (['haut' => 'Bannière en haut', 'cote' => 'Colonne latérale', 'bas' => 'Bas de page'] as $k => $l): ?>
            <option value="<?= $k ?>" <?= ($ad['emplacement'] ?? '') === $k ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="row"><label>Image (JPG, PNG, WEBP, GIF – 2 Mo max)<?= $ad ? ' – vide = inchangée' : '' ?></label>
        <input type="file" name="image" accept="image/*" <?= $ad ? '' : 'required' ?>></div>
      <div class="row"><label>Date de début</label><input type="date" name="date_debut" value="<?= e($ad['date_debut'] ?? date('Y-m-d')) ?>" required></div>
      <div class="row"><label>Date de fin</label><input type="date" name="date_fin" value="<?= e($ad['date_fin'] ?? '') ?>" required></div>
    </div>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gray" href="<?= e(url('ad')) ?>">Annuler</a>
  </form>
</div>
