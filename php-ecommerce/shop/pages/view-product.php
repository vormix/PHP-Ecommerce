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
  $product = $pm->GetProductWithImages($id);
  $discPrice = $pm->getDiscountedPrice($id);
  $lineThrough = $discPrice ? 'text-muted line-through' : '';
 // var_dump($product); die;
  if ($product->id == 0) {
    echo "<script>location.href='".ROOT_URL."shop?page=products-list&msg=not_found';</script>";
    exit;
  }
?>
<a class="back underline" href="<?php echo ROOT_URL; ?>shop?page=products-list">&laquo; Lista Prodotti</a>

<div class="jumbotron">
  <h1 class="display-5"><?php echo esc_html($product->name); ?></h1>
  <p class="lead <?php echo $lineThrough ?>">
    Prezzo: <?php echo esc_html($product->price); ?> €
  </p>
  <?php if ($discPrice): ?>
  <span class="lead badge-pill badge-warning">
    Prezzo Scontato: <?php echo esc_html($discPrice); ?> €
  </span>
  <?php endif; ?>
  <hr class="my-4">

<?php if ($product->images ) : ?>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
      <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
      <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
      <?php $active = 'active'; ?>
      <?php foreach ($product->images as $image) : ?>
      <div class="carousel-item <?php echo $active ?>">
        <img src="<?php echo ROOT_URL . '/images/' . $product->id . '/' . $image->id . '.' . $image->image_extension ?>" class="d-block w-100" alt="...">
      </div>
      <?php $active = ''; ?>
      <?php endforeach ?>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
  <hr class="my-4">

  <?php endif ?>

  <p><?php echo esc_html($product->description); ?></p>
  <p class="lead p-3">
    <form method="post">
      <input name="id" type="hidden" value="<?php echo esc_html($product->id); ?>">
      <input name="add_to_cart" type="submit" class="btn btn-primary right" value="Aggiungi al carrello">
    </form>   
  </p>
</div>