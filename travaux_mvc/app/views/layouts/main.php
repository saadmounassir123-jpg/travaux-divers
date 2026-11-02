<?php
$site = $GLOBALS['config']['site_name'];
$user = Auth::user();
$role = $user['role'] ?? null;
$flash = flash();
$adTop = Ad::forSlot('haut');
$adSide = Ad::forSlot('cote');
$adBottom = Ad::forSlot('bas');
function adHtml($ad) {
    if (!$ad) return '';
    return '<a class="ad" href="' . e(url('ad/click/' . $ad['id'])) . '" target="_blank" rel="noopener sponsored">'
         . '<img src="' . e(asset('uploads/' . $ad['image'])) . '" alt="' . e($ad['titre']) . '"><small>Publicité</small></a>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($site) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
<header class="top">
  <div class="wrap bar">
    <a class="logo" href="<?= e(url('')) ?>"><?= e($site) ?></a>
    <button class="burger" onclick="document.getElementById('nav').classList.toggle('open')" aria-label="Menu">☰</button>
    <nav id="nav">
      <?php if ($role === 'client'): ?>
        <a href="<?= e(url('client/dashboard')) ?>">Mes réservations</a>
        <a href="<?= e(url('reservation/create')) ?>">Réserver</a>
        <a href="<?= e(url('client/profile')) ?>">Profil</a>
      <?php elseif ($role === 'ouvrier'): ?>
        <a href="<?= e(url('worker/planning')) ?>">Planning</a>
        <a href="<?= e(url('worker/absences')) ?>">Absences</a>
        <a href="<?= e(url('worker/salaries')) ?>">Salaires</a>
      <?php elseif ($role === 'admin'): ?>
        <a href="<?= e(url('admin/dashboard')) ?>">Tableau de bord</a>
        <a href="<?= e(url('admin/reservations')) ?>">Réservations</a>
        <a href="<?= e(url('admin/planning')) ?>">Planning</a>
        <a href="<?= e(url('admin/workers')) ?>">Ouvriers</a>
        <a href="<?= e(url('admin/trades')) ?>">Métiers</a>
        <a href="<?= e(url('absence')) ?>">Absences</a>
        <a href="<?= e(url('salary')) ?>">Salaires</a>
        <a href="<?= e(url('admin/clients')) ?>">Clients</a>
        <a href="<?= e(url('admin/reviews')) ?>">Avis</a>
        <a href="<?= e(url('ad')) ?>">Publicités</a>
      <?php endif; ?>
      <?php if ($user): ?>
        <a class="out" href="<?= e(url('auth/logout')) ?>">Déconnexion (<?= e($user['nom']) ?>)</a>
      <?php else: ?>
        <a href="<?= e(url('auth/login')) ?>">Connexion</a>
        <a class="btn-sm" href="<?= e(url('auth/register')) ?>">Inscription</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<?php if ($adTop): ?><div class="wrap ad-top"><?= adHtml($adTop) ?></div><?php endif; ?>

<main class="wrap layout <?= $adSide ? 'with-side' : '' ?>">
  <section class="main">
    <?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
    <?= $content ?>
  </section>
  <?php if ($adSide): ?><aside class="side"><?= adHtml($adSide) ?></aside><?php endif; ?>
</main>

<?php if ($adBottom): ?><div class="wrap ad-bottom"><?= adHtml($adBottom) ?></div><?php endif; ?>
<footer><div class="wrap">© <?= date('Y') ?> <?= e($site) ?> – Location d'ouvriers à la journée</div></footer>
</body>
</html>
