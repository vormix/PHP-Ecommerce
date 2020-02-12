<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
?>

<h1>Il Cruscotto di <?php echo $loggedInUser->first_name; ?></h1>

<div class="card mb-3 mt-3">
  <ul class="list-group list-group-flush">
    <li class="list-group-item">
    <h4>
      <a href="<?php echo ROOT_URL; ?>shop?page=cart" class="underline">Continua gli acquisti &raquo;
          <span class="badge badge-primary badge-pill">
            <span class="js-totCartItems"></span> prodotti nel carrello
          </span>   
      </a>
      </h4>
    </li>
    <li class="list-group-item"><h4><a href="<?php echo ROOT_URL; ?>shop?page=my-orders" class="underline">Visualizza lo storico dei i miei ordini &raquo;</a></h4></li>
    <li class="list-group-item"><h4><a href="<?php echo ROOT_URL; ?>user?page=profile" class="underline">Modifica i miei dati &raquo;</a></h4></li>
  </ul>
</div>


