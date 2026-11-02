<h1>Mon planning</h1>
<div class="card">
  <p><b><?= e($w['nom']) ?></b> – <?= e($w['metier']) ?><?= $w['specialites'] ? ' (' . e($w['specialites']) . ')' : '' ?></p>
  <p class="muted">Vous ne choisissez ni les prix ni les clients : les missions vous sont affectées par l'administrateur.</p>
</div>
<div class="card table-wrap">
<?php if (!$missions): ?><p>Aucune mission pour le moment.</p><?php else: ?>
<table>
  <tr><th>Date</th><th>Client</th><th>Adresse</th><th>Travail à faire</th><th>Statut</th><th>Pointage</th></tr>
  <?php foreach ($missions as $m): ?>
  <tr>
    <td><?= dateFr($m['date_mission']) ?><?= $m['date_mission'] === $today ? ' <b>(aujourd\'hui)</b>' : '' ?></td>
    <td><?= e($m['client_nom']) ?><br><span class="muted"><?= e($m['client_tel']) ?></span></td>
    <td><?= e($m['adresse']) ?></td>
    <td><?= nl2br(e($m['description'])) ?></td>
    <td><?= badge($m['statut']) ?><?= $m['remplace'] ? '<br><span class="muted">(mission de remplacement)</span>' : '' ?></td>
    <td>
      <?php if ($m['date_mission'] === $today && $m['statut'] === 'planifiee'): ?>
        <?= postBtn('worker/start/' . $m['id'], 'Début de mission', 'btn small green') ?>
      <?php elseif ($m['statut'] === 'en_cours'): ?>
        <?= postBtn('worker/finish/' . $m['id'], 'Fin de mission', 'btn small', 'Confirmer la fin de la mission ?') ?>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
