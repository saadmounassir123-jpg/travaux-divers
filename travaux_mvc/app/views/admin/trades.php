<h1>Métiers et tarifs journaliers</h1>
<div class="card">
  <h2><?= $edit ? 'Modifier le métier' : 'Ajouter un métier' ?></h2>
  <form method="post" action="<?= e(url('admin/trades')) ?>">
    <?= Csrf::field() ?>
    <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <div class="grid2">
      <div class="row"><label>Nom du métier</label><input type="text" name="nom" value="<?= e($edit['nom'] ?? '') ?>" required></div>
      <div class="row"><label>Tarif journalier (DH)</label><input type="number" step="0.01" min="1" name="tarif_journalier" value="<?= e($edit['tarif_journalier'] ?? '') ?>" required></div>
    </div>
    <button class="btn" type="submit">Enregistrer</button>
    <?php if ($edit): ?><a class="btn gray" href="<?= e(url('admin/trades')) ?>">Annuler</a><?php endif; ?>
  </form>
</div>
<div class="card table-wrap">
<table>
  <tr><th>Métier</th><th>Tarif / jour</th><th></th></tr>
  <?php foreach ($trades as $t): ?>
  <tr><td><?= e($t['nom']) ?></td><td><?= money($t['tarif_journalier']) ?></td>
    <td class="actions"><a class="btn small" href="<?= e(url('admin/trades?edit=' . $t['id'])) ?>">Modifier</a>
      <?= postBtn('admin/trade_delete/' . $t['id'], 'Supprimer', 'btn small red', 'Supprimer ce métier ?') ?></td></tr>
  <?php endforeach; ?>
</table>
</div>
