<h1>Publicités</h1>
<p><a class="btn" href="<?= e(url('ad/form')) ?>">+ Ajouter une publicité</a></p>
<div class="card table-wrap">
<?php if (!$ads): ?><p>Aucune publicité.</p><?php else: ?>
<table>
  <tr><th>Image</th><th>Titre</th><th>Emplacement</th><th>Période</th><th>Affichages</th><th>Clics</th><th>Actif</th><th></th></tr>
  <?php foreach ($ads as $a): ?>
  <tr>
    <td><img src="<?= e(asset('uploads/' . $a['image'])) ?>" alt="" style="max-width:90px;max-height:50px"></td>
    <td><?= e($a['titre']) ?><br><span class="muted"><?= e($a['lien']) ?></span></td>
    <td><?= ['haut' => 'Bannière haut', 'cote' => 'Colonne latérale', 'bas' => 'Bas de page'][$a['emplacement']] ?? '' ?></td>
    <td><?= dateFr($a['date_debut']) ?> → <?= dateFr($a['date_fin']) ?></td>
    <td><?= (int)$a['affichages'] ?></td><td><?= (int)$a['clics'] ?></td>
    <td><?= $a['actif'] ? '<span class="badge actif">Oui</span>' : '<span class="badge inactif">Non</span>' ?></td>
    <td class="actions">
      <a class="btn small" href="<?= e(url('ad/form/' . $a['id'])) ?>">Modifier</a>
      <?= postBtn('ad/toggle/' . $a['id'], $a['actif'] ? 'Désactiver' : 'Activer', 'btn small gray') ?>
      <?= postBtn('ad/delete/' . $a['id'], 'Supprimer', 'btn small red', 'Supprimer cette publicité ?') ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
