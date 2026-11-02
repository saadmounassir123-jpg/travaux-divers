<h1>Mes réservations</h1>
<p><a class="btn" href="<?= e(url('reservation/create')) ?>">+ Nouvelle réservation</a></p>
<div class="card table-wrap">
<?php if (!$reservations): ?>
  <p>Vous n'avez pas encore de réservation.</p>
<?php else: ?>
<table>
  <tr><th>N°</th><th>Métier</th><th>Période</th><th>Ouvriers</th><th>Montant</th><th>Statut</th><th></th></tr>
  <?php foreach ($reservations as $r): ?>
  <tr>
    <td>#<?= (int)$r['id'] ?></td>
    <td><?= e($r['metier']) ?></td>
    <td><?= dateFr($r['date_debut']) ?> → <?= dateFr($r['date_fin']) ?></td>
    <td><?= (int)$r['nombre_ouvriers'] ?></td>
    <td><?= money($r['montant']) ?> <?= $r['paye'] ? '<span class="badge paye">payée</span>' : '' ?></td>
    <td><?= badge($r['statut']) ?></td>
    <td><a class="btn small" href="<?= e(url('reservation/show/' . $r['id'])) ?>">Détails</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
