<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  if (!isset($_GET['id'])) {
    Header('Location '. ROOT_URL);
    exit;
  } 

  if (isset($_POST['add_to_cart'])) {

    $productId = trim($_POST['id']);

    $cm = new CartManager();
    $cartId = $cm->getCurrentCartId();
    //var_dump($cartId); die;
    $cm->addToCart($productId, $cartId);

    $alertMsg = 'add_to_cart';
    echo "<script>location.href='".ROOT_URL."shop?page=products-list&msg=add_to_cart';</script>";
    exit;
  }

  $id = esc_html(trim($_GET['id']));

  $pm = new ProductManager();
  $product = $pm->get($id);
  
  if ($product->id == 0) {
    echo "<script>location.href='".ROOT_URL."shop?page=products-list&msg=not_found';</script>";
    exit;
  }
?>
<a class="back underline" href="<?php echo ROOT_URL; ?>shop?page=products-list">&laquo; Lista Prodotti</a>

<div class="jumbotron">
  <h1 class="display-5"><?php echo esc_html($product->name); ?></h1>
  <p class="lead">Prezzo: <?php echo esc_html($product->price); ?> €</p>
  <hr class="my-4">
  <p><?php echo esc_html($product->description); ?></p>
  <p class="lead p-3">
    <form method="post">
      <input name="id" type="hidden" value="<?php echo esc_html($product->id); ?>">
      <input name="add_to_cart" type="submit" class="btn btn-primary right" value="Aggiungi al carrello">
    </form>   
  </p>
</div>