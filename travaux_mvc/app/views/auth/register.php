<div class="card narrow">
  <h1>Inscription client</h1>
  <form method="post" action="<?= e(url('auth/register')) ?>">
    <?= Csrf::field() ?>
    <div class="row"><label>Nom complet</label><input type="text" name="nom" value="<?= e($old['nom']) ?>" required></div>
    <div class="row"><label>Email</label><input type="email" name="email" value="<?= e($old['email']) ?>" required></div>
    <div class="row"><label>Téléphone</label><input type="text" name="telephone" value="<?= e($old['telephone']) ?>"></div>
    <div class="row"><label>Mot de passe (6 caractères min.)</label><input type="password" name="password" required minlength="6"></div>
    <div class="row"><label>Confirmer le mot de passe</label><input type="password" name="password2" required></div>
    <button class="btn" type="submit">Créer mon compte</button>
  </form>
</div>
