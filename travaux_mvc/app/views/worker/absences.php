<h1>Mes absences</h1>
<div class="card">
  <h2>Déclarer une absence</h2>
  <p class="muted">Si une mission est prévue ce jour-là, l'administrateur vous remplacera. Une absence non justifiée entraîne une retenue sur votre salaire.</p>
  <form method="post" action="<?= e(url('worker/absences')) ?>">
    <?= Csrf::field() ?>
    <div class="grid2">
      <div class="row"><label>Date</label><input type="date" name="date_absence" min="<?= date('Y-m-d') ?>" required></div>
      <div class="row"><label>Motif</label><input type="text" name="motif" maxlength="255"></div>
    </div>
    <button class="btn" type="submit">Déclarer</button>
  </form>
</div>
<div class="card table-wrap">
  <h2>Historique</h2>
  <?php if (!$absences): ?><p>Aucune absence.</p><?php else: ?>
  <table>
    <tr><th>Date</th><th>Motif</th><th>Justifiée</th><th>Retenue</th></tr>
    <?php foreach ($absences as $a): ?>
    <tr><td><?= dateFr($a['date_absence']) ?></td><td><?= e($a['motif']) ?></td>
        <td><?= $a['justifiee'] ? 'Oui' : 'Non' ?></td><td><?= money($a['retenue']) ?></td></tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
