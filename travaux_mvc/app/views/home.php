<div class="hero">
  <h1>Des ouvriers qualifiés, à la journée</h1>
  <p>Jardinier, plombier, agent de sécurité, électricien... Réservez l'ouvrier dont vous avez besoin en quelques clics.</p>
  <?php if (!Auth::check()): ?>
    <a class="btn red" href="<?= e(url('auth/register')) ?>">Créer un compte client</a>
    <a class="btn gray" href="<?= e(url('auth/login')) ?>">Se connecter</a>
  <?php elseif (Auth::role() === 'client'): ?>
    <a class="btn red" href="<?= e(url('reservation/create')) ?>">Faire une réservation</a>
  <?php else: ?>
    <a class="btn red" href="<?= e(url(Auth::home())) ?>">Mon espace</a>
  <?php endif; ?>
</div>

<h2>Nos métiers et tarifs</h2>
<div class="cards">
<?php foreach ($trades as $t): ?>
  <div class="card">
    <h3><?= e($t['nom']) ?></h3>
    <div class="price"><?= money($t['tarif_journalier']) ?> <small>/ jour</small></div>
    <p class="muted"><?= (int)($counts[$t['id']] ?? 0) ?> ouvrier(s) dans notre équipe</p>
    <?php if (Auth::role() === 'client'): ?>
      <a class="btn small" href="<?= e(url('reservation/create/' . $t['id'])) ?>">Réserver</a>
    <?php elseif (!Auth::check()): ?>
      <a class="btn small" href="<?= e(url('auth/login')) ?>">Se connecter pour réserver</a>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
