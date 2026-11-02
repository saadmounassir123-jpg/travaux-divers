<div class="card">
  <h1><?= $edit ? 'Modifier la réservation #' . (int)$id : 'Nouvelle réservation' ?></h1>
  <form method="post" action="<?= e(url($edit ? 'reservation/edit/' . $id : 'reservation/create')) ?>">
    <?= Csrf::field() ?>
    <div class="grid2">
      <div class="row"><label>Métier</label>
        <select name="trade_id" <?= $edit ? 'disabled' : 'required' ?>>
          <option value="">-- Choisir --</option>
          <?php foreach ($trades as $t): ?>
            <option value="<?= (int)$t['id'] ?>" <?= (int)$old['trade_id'] === (int)$t['id'] ? 'selected' : '' ?>>
              <?= e($t['nom']) ?> – <?= money($t['tarif_journalier']) ?>/jour</option>
          <?php endforeach; ?>
        </select>
        <?php if ($edit): ?><input type="hidden" name="trade_id" value="<?= (int)$old['trade_id'] ?>"><?php endif; ?>
      </div>
      <div class="row"><label>Nombre d'ouvriers</label><input type="number" name="nombre_ouvriers" min="1" max="20" value="<?= (int)$old['nombre_ouvriers'] ?>" required></div>
      <div class="row"><label>Date de début</label><input type="date" name="date_debut" min="<?= date('Y-m-d') ?>" value="<?= e($old['date_debut']) ?>" required></div>
      <div class="row"><label>Date de fin</label><input type="date" name="date_fin" min="<?= date('Y-m-d') ?>" value="<?= e($old['date_fin']) ?>" required></div>
    </div>
    <div class="row"><label>Adresse du lieu de travail</label><input type="text" name="adresse" value="<?= e($old['adresse']) ?>" required></div>
    <div class="row"><label>Description du travail</label><textarea name="description"><?= e($old['description']) ?></textarea></div>
    <p class="muted">Prix = tarif journalier × nombre de jours × nombre d'ouvriers. Le paiement se fait en dehors du site.</p>
    <button class="btn" type="submit"><?= $edit ? 'Enregistrer' : 'Envoyer la réservation' ?></button>
  </form>
</div>
