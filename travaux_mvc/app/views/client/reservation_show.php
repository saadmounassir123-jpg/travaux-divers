<h1>Réservation #<?= (int)$r['id'] ?> <?= badge($r['statut']) ?></h1>
<div class="card">
  <div class="grid2">
    <div>
      <p><b>Métier :</b> <?= e($r['metier']) ?></p>
      <p><b>Nombre d'ouvriers :</b> <?= (int)$r['nombre_ouvriers'] ?></p>
      <p><b>Période :</b> <?= dateFr($r['date_debut']) ?> → <?= dateFr($r['date_fin']) ?></p>
    </div>
    <div>
      <p><b>Adresse :</b> <?= e($r['adresse']) ?></p>
      <p><b>Montant :</b> <?= money($r['montant']) ?> <?= $r['paye'] ? '<span class="badge paye">payée</span>' : '<span class="badge a_payer">non payée</span>' ?></p>
    </div>
  </div>
  <?php if ($r['description']): ?><p><b>Description :</b><br><?= nl2br(e($r['description'])) ?></p><?php endif; ?>
  <div class="actions">
    <?php if ($r['statut'] === 'en_attente'): ?>
      <a class="btn small" href="<?= e(url('reservation/edit/' . $r['id'])) ?>">Modifier</a>
    <?php endif; ?>
    <?php if (in_array($r['statut'], ['en_attente', 'acceptee', 'affectee'], true) && $r['date_debut'] > date('Y-m-d')): ?>
      <?= postBtn('reservation/cancel/' . $r['id'], 'Annuler la réservation', 'btn small red', 'Annuler cette réservation ?') ?>
    <?php endif; ?>
  </div>
</div>

<?php if ($missions): ?>
<div class="card table-wrap">
  <h2>Ouvriers affectés</h2>
  <table>
    <tr><th>Date</th><th>Ouvrier</th><th>Statut</th><th></th></tr>
    <?php foreach ($missions as $m): ?>
    <tr>
      <td><?= dateFr($m['date_mission']) ?></td>
      <td><?= e($m['ouvrier']) ?></td>
      <td><?= badge($m['statut']) ?></td>
      <td><?= $m['remplace'] ? '<span class="badge a_remplacer">Ouvrier remplacé</span>' : '' ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<?php if ($r['statut'] === 'terminee'): ?>
<div class="card">
  <h2>Votre avis</h2>
  <?php if ($review): ?>
    <p>Note : <b><?= (int)$review['note'] ?>/5</b></p>
    <p><?= nl2br(e($review['commentaire'])) ?></p>
  <?php else: ?>
    <form method="post" action="<?= e(url('reservation/review/' . $r['id'])) ?>">
      <?= Csrf::field() ?>
      <div class="row"><label>Note</label>
        <select name="note"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?>/5</option><?php endfor; ?></select></div>
      <div class="row"><label>Commentaire</label><textarea name="commentaire"></textarea></div>
      <button class="btn" type="submit">Envoyer mon avis</button>
    </form>
  <?php endif; ?>
</div>
<?php endif; ?>
<p><a href="<?= e(url('client/dashboard')) ?>">← Retour</a></p>
