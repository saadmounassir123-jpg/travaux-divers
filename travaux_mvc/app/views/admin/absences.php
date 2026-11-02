<h1>Absences et remplacements</h1>
<p class="muted">Quand un ouvrier déjà en mission est absent, choisissez un remplaçant libre du même métier. Une absence non justifiée = retenue (salaire ÷ 30) sur le salaire du mois.</p>
<div class="card table-wrap">
<?php if (!$list): ?><p>Aucune absence enregistrée.</p><?php else: ?>
<table>
  <tr><th>Date</th><th>Ouvrier absent</th><th>Motif</th><th>Mission</th><th>Remplacement</th><th>Justifiée / Retenue</th></tr>
  <?php foreach ($list as $a): ?>
  <tr>
    <td><?= dateFr($a['date_absence']) ?></td>
    <td><?= e($a['ouvrier']) ?><br><span class="muted"><?= e($a['metier']) ?></span></td>
    <td><?= e($a['motif']) ?></td>
    <td><?php if ($a['mission_id']): ?><a href="<?= e(url('admin/reservation/' . $a['reservation_id'])) ?>">Réservation #<?= (int)$a['reservation_id'] ?></a><?php else: ?><span class="muted">Aucune mission</span><?php endif; ?></td>
    <td>
      <?php if ($a['remplacant']): ?>
        Remplacé par <b><?= e($a['remplacant']) ?></b>
      <?php elseif ($a['mission_id'] && $a['mission_statut'] === 'a_remplacer'): ?>
        <?php if ($a['candidats']): ?>
        <form method="post" action="<?= e(url('absence/replace/' . $a['id'])) ?>" style="display:flex;gap:4px">
          <?= Csrf::field() ?>
          <select name="worker_id" required><?php foreach ($a['candidats'] as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nom']) ?></option><?php endforeach; ?></select>
          <button class="btn small" type="submit">Remplacer</button>
        </form>
        <?php else: ?><span class="badge a_remplacer">Aucun ouvrier libre : contactez le client</span><?php endif; ?>
      <?php else: ?>—<?php endif; ?>
    </td>
    <td>
      <?= $a['justifiee'] ? '<span class="badge paye">Justifiée</span>' : '<span class="badge a_payer">Non justifiée</span>' ?>
      – <?= money($a['retenue']) ?><br>
      <?= $a['justifiee']
          ? postBtn('absence/justify/' . $a['id'] . '/0', 'Marquer non justifiée', 'btn small gray')
          : postBtn('absence/justify/' . $a['id'] . '/1', 'Marquer justifiée', 'btn small green') ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
