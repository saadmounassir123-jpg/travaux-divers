<h1>Clients</h1>
<div class="card table-wrap">
<table>
  <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Inscrit le</th><th>Statut</th><th>Actions</th></tr>
  <?php foreach ($clients as $c): ?>
  <tr>
    <td><?= e($c['nom']) ?></td><td><?= e($c['email']) ?></td><td><?= e($c['telephone']) ?></td>
    <td><?= dateFr($c['created_at']) ?></td><td><?= badge($c['statut']) ?></td>
    <td>
      <div class="actions">
      <?php if ($c['statut'] === 'actif'): ?>
        <?= postBtn('admin/client_status/' . $c['id'] . '/suspendu', 'Suspendre', 'btn small red', 'Suspendre ce client ?') ?>
      <?php else: ?>
        <?= postBtn('admin/client_status/' . $c['id'] . '/actif', 'Réactiver', 'btn small green') ?>
      <?php endif; ?>
      </div>
      <form method="post" action="<?= e(url('admin/client_reset/' . $c['id'])) ?>" style="margin-top:6px;display:flex;gap:4px">
        <?= Csrf::field() ?>
        <input type="text" name="password" placeholder="Nouveau mot de passe" style="padding:5px">
        <button class="btn small gray" type="submit">Réinitialiser</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
