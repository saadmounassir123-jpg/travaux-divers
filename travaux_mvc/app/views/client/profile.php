<div class="card narrow">
  <h1>Mon profil</h1>
  <form method="post" action="<?= e(url('client/profile')) ?>">
    <?= Csrf::field() ?>
    <div class="row"><label>Nom</label><input type="text" name="nom" value="<?= e($user['nom']) ?>" required></div>
    <div class="row"><label>Email (non modifiable)</label><input type="email" value="<?= e($user['email']) ?>" disabled></div>
    <div class="row"><label>Téléphone</label><input type="text" name="telephone" value="<?= e($user['telephone']) ?>"></div>
    <div class="row"><label>Adresse</label><input type="text" name="adresse" value="<?= e($user['adresse']) ?>"></div>
    <div class="row"><label>Nouveau mot de passe (laisser vide pour ne pas changer)</label><input type="password" name="password"></div>
    <button class="btn" type="submit">Enregistrer</button>
  </form>
</div>
