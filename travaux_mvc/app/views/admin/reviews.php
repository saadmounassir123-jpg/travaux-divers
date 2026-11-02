<h1>Avis des clients</h1>
<div class="card table-wrap">
<?php if (!$reviews): ?><p>Aucun avis.</p><?php else: ?>
<table>
  <tr><th>Date</th><th>Client</th><th>Métier</th><th>Note</th><th>Commentaire</th><th></th></tr>
  <?php foreach ($reviews as $v): ?>
  <tr><td><?= dateFr($v['created_at']) ?></td><td><?= e($v['client_nom']) ?></td><td><?= e($v['metier']) ?></td>
      <td><?= (int)$v['note'] ?>/5</td><td><?= nl2br(e($v['commentaire'])) ?></td>
      <td><?= postBtn('admin/review_delete/' . $v['id'], 'Supprimer', 'btn small red', 'Supprimer cet avis ?') ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
