
<?php

if (!defined('ROOT_URL')) {
  die;
}

if (! isset($_GET['id'])) {
  echo "<script>location.href='".ROOT_URL."';</script>";
  exit;
}

if (isset($_POST['add_to_cart'])) {

  $productId = htmlspecialchars(trim($_POST['id']));
  // addToCart Logic
  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();

  // aggiumngi al carrello "cartId" il prodotto "productId"
  $cm->addToCart($productId, $cartId);

  // stampato un messaggio per l'utente
  //echo 'ok';
}

$id = htmlspecialchars(trim($_GET['id']));

$pm = new ProductManager();
$product = $pm->get($id);

if (! (property_exists($product, 'id')) ) {
  echo "<script>location.href='".ROOT_URL."';</script>";
  exit;
}


?>

<div class="jumbotron">
  <h1 class="display-5"><?php  echo $product->name ?></h1>
  <p class="lead">Prezzo: <?php  echo $product->price ?> €</p>
  <hr class="my-4">
  <p>
   <?php  echo $product->description ?>
  </p>
  <p class="lead p-3">
    </p>
      <form method="post">
        <input name="id" type="hidden" value="<?php  echo $product->id ?>">
        <input name="add_to_cart" type="submit" class="btn btn-primary right" value="Aggiungi al carrello">
      </form>   
    </p>
</div>