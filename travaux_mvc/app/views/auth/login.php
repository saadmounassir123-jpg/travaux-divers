<div class="card narrow">
  <h1>Connexion</h1>
  <form method="post" action="<?= e(url('auth/login')) ?>">
    <?= Csrf::field() ?>
    <div class="row"><label>Email</label><input type="email" name="email" value="<?= e($email) ?>" required autofocus></div>
    <div class="row"><label>Mot de passe</label><input type="password" name="password" required></div>
    <button class="btn" type="submit">Se connecter</button>
  </form>
  <p class="muted">Pas de compte ? <a href="<?= e(url('auth/register')) ?>">Inscrivez-vous (client)</a>.<br>
  Mot de passe perdu ? Contactez l'administrateur.</p>
</div>
