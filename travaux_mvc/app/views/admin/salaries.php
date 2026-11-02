<h1>Salaires mensuels</h1>
<div class="card" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
  <form method="get" action="<?= e(url('salary')) ?>" style="display:flex;gap:10px;flex-wrap:wrap">
    <select name="mois" style="max-width:180px"><?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= $m === $mois ? 'selected' : '' ?>><?= monthName($m) ?></option><?php endfor; ?></select>
    <input type="number" name="annee" value="<?= (int)$annee ?>" style="max-width:110px">
    <button class="btn" type="submit">Afficher</button>
  </form>
  <form method="post" action="<?= e(url('salary/generate')) ?>">
    <?= Csrf::field() ?>
    <input type="hidden" name="mois" value="<?= (int)$mois ?>"><input type="hidden" name="annee" value="<?= (int)$annee ?>">
    <button class="btn green" type="submit">Générer / recalculer les fiches</button>
  </form>
</div>
<div class="stats">
  <div class="stat"><b><?= money($tot['net']) ?></b>Total salaires nets</div>
  <div class="stat"><b><?= money($tot['ret']) ?></b>Total retenues</div>
</div>
<div class="card table-wrap">
<?php if (!$list): ?><p>Aucune fiche pour <?= monthName($mois) ?> <?= (int)$annee ?>. Cliquez sur « Générer ».</p><?php else: ?>
<table>
  <tr><th>Ouvrier</th><th>Métier</th><th>Base</th><th>Jours d'absence</th><th>Retenues</th><th>Net</th><th>Statut</th><th></th></tr>
  <?php foreach ($list as $s): ?>
  <tr><td><?= e($s['ouvrier']) ?></td><td><?= e($s['metier']) ?></td><td><?= money($s['salaire_base']) ?></td>
      <td><?= (int)$s['jours_absence'] ?></td><td><?= money($s['retenues']) ?></td><td><b><?= money($s['salaire_net']) ?></b></td>
      <td><?= badge($s['statut']) ?></td>
      <td><?= $s['statut'] === 'a_payer' ? postBtn('salary/pay/' . $s['id'], 'Marquer payé', 'btn small green', 'Confirmer le paiement ?') : '' ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
