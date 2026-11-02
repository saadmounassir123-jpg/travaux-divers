<div class="card">
  <h1><?= $w ? 'Modifier l\'ouvrier' : 'Nouvel ouvrier' ?></h1>
  <form method="post" action="<?= e(url('admin/worker_save' . ($w ? '/' . $w['id'] : ''))) ?>">
    <?= Csrf::field() ?>
    <div class="grid2">
      <div class="row"><label>Nom complet</label><input type="text" name="nom" value="<?= e($w['nom'] ?? '') ?>" required></div>
      <div class="row"><label>Téléphone</label><input type="text" name="telephone" value="<?= e($w['telephone'] ?? '') ?>"></div>
      <div class="row"><label>Email (identifiant de connexion)</label>
        <input type="email" name="email" value="<?= e($w['email'] ?? '') ?>" <?= $w ? 'disabled' : 'required' ?>></div>
      <div class="row"><label>Mot de passe <?= $w ? '(vide = inchangé)' : '(6 caractères min.)' ?></label>
        <input type="password" name="password" <?= $w ? '' : 'required' ?>></div>
      <div class="row"><label>Métier</label>
        <select name="trade_id" required>
          <?php foreach ($trades as $t): ?>
            <option value="<?= (int)$t['id'] ?>" <?= ($w && (int)$w['trade_id'] === (int)$t['id']) ? 'selected' : '' ?>><?= e($t['nom']) ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="row"><label>Salaire mensuel (DH)</label><input type="number" step="0.01" min="1" name="salaire_mensuel" value="<?= e($w['salaire_mensuel'] ?? '') ?>" required></div>
    </div>
    <div class="row"><label>Spécialités</label><input type="text" name="specialites" value="<?= e($w['specialites'] ?? '') ?>"></div>
    <?php if ($w): ?>
    <div class="row"><label>Statut</label>
      <select name="statut"><option value="actif" <?= $w['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
      <option value="inactif" <?= $w['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option></select></div>
    <?php endif; ?>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gray" href="<?= e(url('admin/workers')) ?>">Annuler</a>
  </form>
</div>
