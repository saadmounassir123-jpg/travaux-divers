<h1>Tableau de bord</h1>
<div class="stats">
  <div class="stat"><b><?= (int)$s['total'] ?></b>Réservations</div>
  <div class="stat"><b><?= (int)$s['en_attente'] ?></b>En attente</div>
  <div class="stat"><b><?= (int)$s['en_mission'] ?></b>Ouvriers en mission aujourd'hui</div>
  <div class="stat"><b><?= (int)$dispo ?></b>Ouvriers disponibles aujourd'hui</div>
  <div class="stat"><b><?= (int)$abs_jour ?></b>Absences aujourd'hui</div>
  <div class="stat"><b><?= (int)$a_remplacer ?></b>Missions à remplacer</div>
  <div class="stat"><b><?= (int)$clients ?></b>Clients</div>
  <div class="stat"><b><?= number_format($note, 1, ',', '') ?>/5</b>Note moyenne</div>
</div>
<div class="stats">
  <div class="stat"><b><?= money($s['ca_mois']) ?></b>Chiffre d'affaires du mois (réservations payées)</div>
  <div class="stat"><b><?= money($sal['net']) ?></b>Salaires nets du mois (fiches générées)</div>
  <div class="stat"><b><?= money($sal['ret']) ?></b>Retenues du mois</div>
</div>
<?php if ($a_remplacer > 0): ?>
  <div class="flash err">Il y a <?= (int)$a_remplacer ?> mission(s) à remplacer. <a href="<?= e(url('absence')) ?>">Traiter les absences</a></div>
<?php endif; ?>
