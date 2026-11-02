<h1>Réservation #<?= (int)$r['id'] ?> <?= badge($r['statut']) ?></h1>
<div class="card">
  <div class="grid2">
    <div>
      <p><b>Client :</b> <?= e($r['client_nom']) ?> (<?= e($r['client_tel']) ?>)</p>
      <p><b>Métier :</b> <?= e($r['metier']) ?> – <?= money($r['tarif_journalier']) ?>/jour</p>
      <p><b>Ouvriers demandés :</b> <?= (int)$r['nombre_ouvriers'] ?> (affectés : <?= (int)$nb_affectes ?>)</p>
    </div>
    <div>
      <p><b>Période :</b> <?= dateFr($r['date_debut']) ?> → <?= dateFr($r['date_fin']) ?></p>
      <p><b>Adresse :</b> <?= e($r['adresse']) ?></p>
      <p><b>Montant :</b> <?= money($r['montant']) ?> <?= $r['paye'] ? '<span class="badge paye">payée</span>' : '<span class="badge a_payer">non payée</span>' ?></p>
    </div>
  </div>
  <?php if ($r['description']): ?><p><b>Description :</b><br><?= nl2br(e($r['description'])) ?></p><?php endif; ?>
  <div class="actions">
    <?php if ($r['statut'] === 'en_attente'): ?>
      <?= postBtn('admin/reservation_action/' . $r['id'] . '/accepter', 'Accepter', 'btn small green') ?>
      <?= postBtn('admin/reservation_action/' . $r['id'] . '/refuser', 'Refuser', 'btn small red', 'Refuser cette réservation ?') ?>
    <?php endif; ?>
    <?php if (in_array($r['statut'], ['acceptee', 'affectee'], true)): ?>
      <?= postBtn('admin/reservation_action/' . $r['id'] . '/annuler', 'Annuler', 'btn small red', 'Annuler cette réservation ?') ?>
    <?php endif; ?>
    <?php if (!$r['paye'] && !in_array($r['statut'], ['refusee', 'annulee'], true)): ?>
      <?= postBtn('admin/reservation_action/' . $r['id'] . '/payee', 'Marquer comme payée', 'btn small') ?>
    <?php elseif ($r['paye']): ?>
      <?= postBtn('admin/reservation_action/' . $r['id'] . '/impayee', 'Retirer le paiement', 'btn small gray') ?>
    <?php endif; ?>
  </div>
</div>

<?php if (in_array($r['statut'], ['acceptee', 'affectee'], true) && $nb_affectes < (int)$r['nombre_ouvriers']): ?>
<div class="card">
  <h2>Affecter un ouvrier</h2>
  <?php if (!$dispo): ?>
    <p class="flash err">Aucun ouvrier de ce métier n'est libre sur toute la période.</p>
  <?php else: ?>
  <form method="post" action="<?= e(url('admin/assign/' . $r['id'])) ?>" style="display:flex;gap:10px;flex-wrap:wrap">
    <?= Csrf::field() ?>
    <select name="worker_id" style="max-width:300px" required>
      <?php foreach ($dispo as $w): ?><option value="<?= (int)$w['id'] ?>"><?= e($w['nom']) ?></option><?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Affecter</button>
  </form>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($missions): ?>
<div class="card table-wrap">
  <h2>Missions</h2>
  <table>
    <tr><th>Date</th><th>Ouvrier</th><th>Statut</th><th></th></tr>
    <?php foreach ($missions as $m): ?>
    <tr><td><?= dateFr($m['date_mission']) ?></td><td><?= e($m['ouvrier']) ?></td><td><?= badge($m['statut']) ?></td>
        <td><?= $m['remplace'] ? 'Remplacement' : '' ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>
<p><a href="<?= e(url('admin/reservations')) ?>">← Retour</a></p>
