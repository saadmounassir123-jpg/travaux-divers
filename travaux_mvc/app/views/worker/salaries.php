<h1>Mes salaires</h1>
<div class="card"><p>Salaire mensuel de base : <b><?= money($w['salaire_mensuel']) ?></b></p></div>
<div class="card table-wrap">
<?php if (!$salaries): ?><p>Aucune fiche de paie pour le moment.</p><?php else: ?>
<table>
  <tr><th>Mois</th><th>Salaire de base</th><th>Jours d'absence</th><th>Retenues</th><th>Salaire net</th><th>Statut</th></tr>
  <?php foreach ($salaries as $s): ?>
  <tr><td><?= monthName((int)$s['mois']) ?> <?= (int)$s['annee'] ?></td><td><?= money($s['salaire_base']) ?></td>
      <td><?= (int)$s['jours_absence'] ?></td><td><?= money($s['retenues']) ?></td>
      <td><b><?= money($s['salaire_net']) ?></b></td><td><?= badge($s['statut']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
