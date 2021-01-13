<?php
  
  if (!defined('ROOT_URL')) {
    die;
  }

  if (isset($_POST['add_to_cart'])) {

    // Aggingi al carrello
    $productId = htmlspecialchars(trim($_POST['id']));
    // addToCart Logic
    $cm = new CartManager();
    $cartId = $cm->getCurrentCartId();

    // aggiumngi al carrello "cartId" il prodotto "productId"
    $cm->addToCart($productId, $cartId);

    echo 'ok aggiunto al carrello';
  }

  $productMgr = new ProductManager();
  $products = $productMgr->getAll();
?>

<div class="row">

<?php if($products) : ?>
  <?php foreach($products as $product) : ?>

  <div class="product-card card mb-3 col-md-3 col-6">
      <div class="card-header bg-dark text-light rounded-0">
        <?php echo $product->name ?>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
        <?php echo $product->description ?>
         <small class="text-muted right"><?php echo $product->price ?> €</small>
        </li>
      </ul>
      <div class="footer">
        <div class="product-actions">
          <button class="btn btn-secondary btn-sm btn-block rounded-0" onclick="location.href='<?php echo ROOT_URL . 'shop?page=view-product&id=' . $product->id ?>'">Vedi</button>
          <!--<a class="btn btn-outline-primary btn-sm" href="#">Aggiungi al carrello</a>-->
          <form method="post">
            <input type="hidden" name="id" value="<?php echo $product->id ?>">
            <input name="add_to_cart" type="submit" class="btn btn-primary btn-sm btn-block rounded-0" value="Aggiungi al carrello">
          </form>
        </div>
      </div>
    </div>

  <?php endforeach; ?>
<?php else : ?>
  <p>Nessun prodotto disponibile...</p>
<?php endif; ?>

</div>