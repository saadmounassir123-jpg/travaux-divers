<h1>Réservations</h1>
<form method="get" action="<?= e(url('admin/reservations')) ?>" class="card" style="display:flex;gap:10px;align-items:center">
  <label style="margin:0">Statut :</label>
  <select name="statut" style="max-width:220px" onchange="this.form.submit()">
    <option value="">Tous</option>
    <?php foreach (['en_attente','acceptee','affectee','en_cours','terminee','refusee','annulee'] as $s): ?>
      <option value="<?= $s ?>" <?= $filtre === $s ? 'selected' : '' ?>><?= e(statusLabel($s)) ?></option>
    <?php endforeach; ?>
  </select>
</form>
<div class="card table-wrap">
<table>
  <tr><th>N°</th><th>Client</th><th>Métier</th><th>Période</th><th>Ouvriers (affectés/demandés)</th><th>Montant</th><th>Statut</th><th></th></tr>
  <?php foreach ($list as $r): ?>
  <tr>
    <td>#<?= (int)$r['id'] ?></td><td><?= e($r['client_nom']) ?></td><td><?= e($r['metier']) ?></td>
    <td><?= dateFr($r['date_debut']) ?> → <?= dateFr($r['date_fin']) ?></td>
    <td><?= (int)$r['nb_affectes'] ?>/<?= (int)$r['nombre_ouvriers'] ?></td>
    <td><?= money($r['montant']) ?> <?= $r['paye'] ? '<span class="badge paye">payée</span>' : '' ?></td>
    <td><?= badge($r['statut']) ?></td>
    <td><a class="btn small" href="<?= e(url('admin/reservation/' . $r['id'])) ?>">Gérer</a></td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
