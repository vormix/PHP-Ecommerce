<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
?>

<h1>Il Cruscotto amministrativo di <?php echo $loggedInUser->first_name; ?></h1>

<div class="card mb-3 mt-3">
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><a href="<?php echo ROOT_URL; ?>admin?page=orders-list" class="underline">Gestione Ordini &raquo;</a></li>
    <li class="list-group-item"><a href="<?php echo ROOT_URL; ?>admin?page=products-list" class="underline">Gestione Prodotti &raquo;</a></li>
    <li class="list-group-item"><a href="<?php echo ROOT_URL; ?>admin?page=category-list" class="underline">Gestione Categorie &raquo;</a></li>
    <li class="list-group-item"><a href="<?php echo ROOT_URL; ?>admin?page=users-list" class="underline">Gestione Utenti &raquo;</a></li>
  </ul>
</div>

