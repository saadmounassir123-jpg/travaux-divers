<h1>Ouvriers de la société</h1>
<p><a class="btn" href="<?= e(url('admin/worker_form')) ?>">+ Ajouter un ouvrier</a></p>
<div class="card table-wrap">
<table>
  <tr><th>Nom</th><th>Métier</th><th>Email</th><th>Téléphone</th><th>Salaire mensuel</th><th>Statut</th><th></th></tr>
  <?php foreach ($workers as $w): ?>
  <tr>
    <td><?= e($w['nom']) ?></td><td><?= e($w['metier']) ?></td><td><?= e($w['email']) ?></td><td><?= e($w['telephone']) ?></td>
    <td><?= money($w['salaire_mensuel']) ?></td><td><?= badge($w['statut']) ?></td>
    <td class="actions">
      <a class="btn small" href="<?= e(url('admin/worker_form/' . $w['id'])) ?>">Modifier</a>
      <?= postBtn('admin/worker_delete/' . $w['id'], 'Supprimer', 'btn small red', 'Supprimer cet ouvrier ?') ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
