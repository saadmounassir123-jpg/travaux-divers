<h1>Planning général</h1>
<form method="get" action="<?= e(url('admin/planning')) ?>" class="card" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
  <select name="mois" style="max-width:180px"><?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= $m === $mois ? 'selected' : '' ?>><?= monthName($m) ?></option><?php endfor; ?></select>
  <input type="number" name="annee" value="<?= (int)$annee ?>" style="max-width:110px">
  <button class="btn" type="submit">Afficher</button>
</form>
<div class="card table-wrap">
<?php if (!$rows): ?><p>Aucune mission ce mois-ci.</p><?php else: ?>
<table>
  <tr><th>Date</th><th>Ouvrier</th><th>Métier</th><th>Client</th><th>Réservation</th><th>Statut</th></tr>
  <?php foreach ($rows as $a): ?>
  <tr><td><?= dateFr($a['date_mission']) ?></td><td><?= e($a['ouvrier']) ?></td><td><?= e($a['metier']) ?></td>
      <td><?= e($a['client_nom']) ?></td>
      <td><a href="<?= e(url('admin/reservation/' . $a['reservation_id'])) ?>">#<?= (int)$a['reservation_id'] ?></a></td>
      <td><?= badge($a['statut']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
